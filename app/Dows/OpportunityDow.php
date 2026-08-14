<?php

namespace App\Dows;

use App\Middlewares\Application;
use App\Repositories\OpportunityRepository;
use App\Services\OpportunityService;
use App\Utilities\FG;
use App\Validators\OpportunityValidator;
use Illuminate\Database\Capsule\Manager as DB;

class OpportunityDow {
    private OpportunityRepository $opportunityRepository;
    private OpportunityService $opportunityService;

    public function __construct() {
        $this->opportunityRepository = new OpportunityRepository();
        $this->opportunityService = new OpportunityService();
    }

    public function index($request) {
        $response = FG::responseDefault();
        try {
            $company_id = Application::getItem('company_id');
            $opportunities = $this->opportunityRepository->getAllByCompany($company_id);
            $data = $opportunities->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'description' => $item->description,
                        'estimated_value' => (float)$item->estimated_value,
                        'probability' => (float)$item->probability,
                        'expected_close_date' => $item->expected_close_date,
                        'closed_at' => $item->closed_at,
                        'lost_reason' => $item->lost_reason,
                        'pipeline' => ['id' => $item->pipeline?->id, 'name' => $item->pipeline?->name,],
                        'stage' => [
                            'id' => $item->stage?->id,
                            'name' => $item->stage?->name,
                            'stage_key' => $item->stage?->stage_key,
                            'position' => $item->stage?->position,
                            'is_won' => (bool)$item->stage?->is_won,
                            'is_lost' => (bool)$item->stage?->is_lost,
                        ],
                        'lead' => $item->lead ? [
                                    'id' => $item->lead->id,
                                    'name' => $item->lead->name,
                                    'business_name' => $item->lead->business_name,
                                    'whatsapp' => $item->lead->whatsapp,] : null,
                        'customer' => $item->customer ? [
                                    'id' => $item->customer->id,
                                    'name' => $item->customer->name,
                                    'business_name' => $item->customer->business_name,] : null,
                        'assigned_user' => $item->assignedUser ? [
                                    'id' => $item->assignedUser->id,
                                    'name' => $item->assignedUser->name,] : null,
                        'created_at' => $item->created_at,
                    ];
                }
            );
            $response['success'] = true;
            $response['data'] = $data;
            $response['message'] = 'Oportunidades obtenidas correctamente.';
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function show($request) {
        $response = FG::responseDefault();
        try {
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');
            $opportunity = $this->opportunityRepository->getById($id,$company_id);
            if (!$opportunity) {
                throw new \Exception('La oportunidad no fue encontrada.');
            }

            $response['success'] = true;
            $response['data'] = $opportunity;
            $response['message'] = 'Oportunidad obtenida correctamente.';
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function store($request) {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');
            $user_id = Application::getItem('user_id');

            $errors = OpportunityValidator::store($input);
            if (!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $opportunity = $this->opportunityService->create($input,$company_id,$user_id);
            DB::commit();
            $response['success'] = true;
            $response['data'] = $opportunity;
            $response['message'] ='Oportunidad registrada correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function update($request) {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $id = (int)$request->getAttribute('id');
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');

            $errors = OpportunityValidator::update($input);
            if (!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $opportunity = $this->opportunityRepository->getById($id,$company_id);
            if (!$opportunity) {
                $response['success'] = false;
                $response['message'] = 'La oportunidad no fue encontrada.';
                return $response;
            }

            $opportunity = $this->opportunityService->update($opportunity, $input);
            DB::commit();
            $response['success'] = true;
            $response['data'] = $opportunity;
            $response['message'] = 'Oportunidad actualizada correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function move($request) {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $id = (int)$request->getAttribute('id');
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');
            $user_id = Application::getItem('user_id');

            $errors = OpportunityValidator::moveStage($input);
            if (!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $opportunity = $this->opportunityRepository->getById($id, $company_id);
            if (!$opportunity) {
                $response['success'] = false;
                $response['message'] = 'La oportunidad no fue encontrada.';
                return $response;
            }

            $opportunity = $this->opportunityService->moveStage($opportunity, (int)$input['pipeline_stage_id'], $input, $user_id);
            DB::commit();
            
            $response['success'] = true;
            $response['data'] = $opportunity;
            $response['message'] = 'Oportunidad movida correctamente.';

        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function remove($request) {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');
            $opportunity = $this->opportunityRepository->getById($id,$company_id);

            if (!$opportunity) {
               $response['success'] = false;
                $response['message'] = 'La oportunidad no fue encontrada.';
                return $response;
            }

            if ($opportunity->closed_at) {
                $response['success'] = false;
                $response['message'] = 'No se puede eliminar una oportunidad cerrada.';
                return $response;
            }

            $opportunity->delete();
            DB::commit();

            $response['success'] = true;
            $response['data'] = null;
            $response['message'] = 'Oportunidad eliminada correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }
}
