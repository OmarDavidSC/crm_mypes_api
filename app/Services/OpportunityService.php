<?php

namespace App\Services;

use App\Constants\ActivityConstant;
use App\Models\Opportunity;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Repositories\CustomerRepository;
use App\Repositories\LeadRepository;
use App\Repositories\PipelineRepository;
use App\Repositories\PipelineStageRepository;
use App\Utilities\FG;

class OpportunityService {

    private PipelineRepository $pipelineRepository;
    private PipelineStageRepository $pipelineStageRepository;
    private LeadRepository $leadRepository;
    private ActivityService $activityService;
    private CustomerRepository $customerRepository;

    public function __construct()
    {
        $this->pipelineRepository = new PipelineRepository();
        $this->pipelineStageRepository = new PipelineStageRepository();
        $this->leadRepository = new LeadRepository();
        $this->activityService = new ActivityService();
        $this->customerRepository = new CustomerRepository();
    }

    public function create(array $input, int $company_id, ?int $user_id = null): Opportunity {
        if (empty($input['lead_id']) && empty($input['customer_id'])) {
            throw new \Exception('La oportunidad debe estar asociada a un prospecto o cliente.');
        }

        if (!empty($input['lead_id'])) {
            $lead = $this->leadRepository->getById((int)$input['lead_id'], $company_id);
            if (!$lead) {
                throw new \Exception('El prospecto seleccionado no existe.');
            }
        }

        if (!empty($input['customer_id'])) {
            $customer = $this->customerRepository->getById((int)$input['customer_id'], $company_id);
            if (!$customer) {
                throw new \Exception('El cliente seleccionado no existe.');
            }
        }

        $pipeline = $this->resolvePipeline($input, $company_id);
        $stage = $this->resolveStage($pipeline, $input, $company_id);
        if ($stage->is_won || $stage->is_lost) {
            throw new \Exception('Una nueva oportunidad no puede iniciar en una etapa final.');
        }

        $opportunity = new Opportunity();
        $opportunity->company_id = $company_id;
        $opportunity->pipeline_id = $pipeline->id;
        $opportunity->pipeline_stage_id = $stage->id;
        $opportunity->lead_id = !empty($input['lead_id']) ? (int)$input['lead_id'] : null;
        $opportunity->customer_id = !empty($input['customer_id']) ? (int)$input['customer_id'] : null;
        $opportunity->assigned_user_id = !empty($input['assigned_user_id']) ? (int)$input['assigned_user_id'] : $user_id;
        $opportunity->title = trim($input['title']);
        $opportunity->description = !empty($input['description']) ? trim($input['description']) : null;
        $opportunity->estimated_value = isset($input['estimated_value']) ? round( (float)$input['estimated_value'], 2) : 0;
        $opportunity->probability = isset($input['probability']) ? (float)$input['probability'] : (float)$stage->probability;
        $opportunity->expected_close_date = !empty($input['expected_close_date']) ? $input['expected_close_date'] : null;
        $opportunity->closed_at = null;
        $opportunity->lost_reason = null;
        $opportunity->lost_notes = null;
        $opportunity->status = 1;
        $opportunity->save();

        $this->activityService->createSystemActivity(
                    $company_id,
                    $user_id,
                    ActivityConstant::TYPE_OPPORTUNITY_CREATED,
                    'Oportunidad creada',
                    "Se creó la oportunidad {$opportunity->title}.",
                    $opportunity->lead_id,
                    $opportunity->customer_id,
                    $opportunity->id
        );

        return $opportunity->fresh();
    }

    public function update(Opportunity $opportunity, array $input): Opportunity {
        if ($opportunity->closed_at) {
            throw new \Exception('No se puede modificar una oportunidad cerrada.');
        }
        if (array_key_exists('assigned_user_id', $input)) {
            $opportunity->assigned_user_id = !empty($input['assigned_user_id']) ? (int)$input['assigned_user_id'] : null;
        }
        if (array_key_exists('title', $input)) {
            $title = trim($input['title']);
            if (empty($title)) {
                throw new \Exception('El título de la oportunidad es obligatorio.');
            }
            $opportunity->title = $title;
        }
        if (array_key_exists('description', $input)) {
            $opportunity->description = !empty($input['description']) ? trim($input['description']) : null;
        }
        if (array_key_exists('estimated_value', $input)) {
            $opportunity->estimated_value = $input['estimated_value'] !== '' ? round((float) $input['estimated_value'], 2) : 0;
        }
        if (array_key_exists('probability', $input)) {
            $probability = (float)$input['probability'];
            if ( $probability < 0 || $probability > 100) {
                throw new \Exception('La probabilidad debe estar entre 0 y 100.');
            }
            $opportunity->probability = $probability;
        }
        if (array_key_exists('expected_close_date',$input)) {
            $opportunity->expected_close_date = !empty($input['expected_close_date']) ? $input['expected_close_date'] : null;
        }
        $opportunity->save();
        return $opportunity->fresh();
    }

    public function moveStage(Opportunity $opportunity, int $stage_id, array $input = [],  ?int $user_id = null): Opportunity {
        $previousStage = $opportunity->stage;

        if ($opportunity->closed_at) {
            throw new \Exception( 'La oportunidad ya se encuentra cerrada.');
        }

        $stage = $this->pipelineStageRepository->getById($stage_id,$opportunity->company_id);
        if (!$stage) {
            throw new \Exception('La etapa seleccionada no existe.');
        }
        if ((int)$stage->pipeline_id !== (int)$opportunity->pipeline_id) {
            throw new \Exception('La etapa seleccionada no pertenece al pipeline de la oportunidad.');
        }
        if ((int)$opportunity->pipeline_stage_id === (int)$stage->id) {
            throw new \Exception('La oportunidad ya se encuentra en esta etapa.');
        }

        $opportunity->pipeline_stage_id = $stage->id;
        $opportunity->probability = (float)$stage->probability;
        if ($stage->is_won) {
            $opportunity->probability = 100;
            $opportunity->closed_at = FG::getDateHour();
            $opportunity->lost_reason = null;
            $opportunity->lost_notes = null;
        }
        elseif ($stage->is_lost) {
            if (empty($input['lost_reason'])) {
                throw new \Exception('Debe indicar el motivo por el cual se perdió la oportunidad.');
            }
            $opportunity->probability = 0;
            $opportunity->closed_at = FG::getDateHour();
            $opportunity->lost_reason = trim($input['lost_reason']);
            $opportunity->lost_notes = !empty($input['lost_notes']) ? trim($input['lost_notes']) : null;
        }
        else {
            $opportunity->closed_at = null;
            $opportunity->lost_reason = null;
            $opportunity->lost_notes = null;
        }
        $opportunity->save();

        $this->activityService->createSystemActivity(
                    $opportunity->company_id,
                    $user_id,
                    ActivityConstant::TYPE_STAGE_CHANGE,
                    'Cambio de etapa',
                    "La oportunidad cambió de {$previousStage->name} a {$stage->name}.",
                    $opportunity->lead_id,
                    $opportunity->customer_id,
                    $opportunity->id
        );

        return $opportunity->fresh();
    }

    private function resolvePipeline(array $input, int $company_id): Pipeline {
        if (!empty($input['pipeline_id'])) {
            $pipeline = $this->pipelineRepository->getById( (int)$input['pipeline_id'],$company_id);
            if (!$pipeline) {
                throw new \Exception('El pipeline seleccionado no existe.');
            }

            if ((int)$pipeline->status !== 1) {
                throw new \Exception('El pipeline seleccionado se encuentra inactivo.');
            }
            return $pipeline;
        }
        $pipeline = $this->pipelineRepository->getDefault($company_id);
        if (!$pipeline) {
            throw new \Exception('La empresa no tiene un pipeline predeterminado.');
        }
        return $pipeline;
    }

    private function resolveStage(Pipeline $pipeline, array $input, int $company_id): PipelineStage {

        if (!empty($input['pipeline_stage_id'])) {
            $stage = $this->pipelineStageRepository->getById( (int)$input['pipeline_stage_id'], $company_id);
            if (!$stage) {
                throw new \Exception('La etapa seleccionada no existe.');
            }
            if ((int)$stage->pipeline_id !== (int)$pipeline->id) {
                throw new \Exception('La etapa seleccionada no pertenece al pipeline.');
            }
            return $stage;
        }

        $stage = $this->pipelineStageRepository->getInitialStage($pipeline->id,$company_id);
        if (!$stage) {
            throw new \Exception('El pipeline no tiene etapas configuradas.');
        }
        return $stage;
    }
}