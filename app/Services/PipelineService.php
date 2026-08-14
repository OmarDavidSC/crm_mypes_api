<?php

namespace App\Services;

use App\Constants\PipelineConstant;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Repositories\PipelineRepository;
use App\Repositories\PipelineStageRepository;

class PipelineService {

    private PipelineRepository $pipelineRepository;
    private PipelineStageRepository $pipelineStageRepository;

    public function __construct()
    {
        $this->pipelineRepository = new PipelineRepository();
        $this->pipelineStageRepository = new PipelineStageRepository();
    }

    public function create(array $input, int $company_id): Pipeline {
        $name = trim($input['name']);
        $existing = $this->pipelineRepository->getByName($name, $company_id);
        if($existing) {
           throw new \Exception('Ya existe un pipeline con este nombre.'); 
        }

        $isDefault = isset($input['is_default']) ? (int)$input['is_default'] : 0;

        /*
         * Si la empresa todavía no tiene ningún pipeline,
         * el primero será automáticamente el predeterminado.
         */
        $currentDefault = $this->pipelineRepository->getDefault($company_id);
        if (!$currentDefault) {
            $isDefault = 1;
        }
        if ($isDefault === 1) {
            $this->removeCurrentDefault($company_id);
        }

        $pipeline = new Pipeline();
        $pipeline->company_id = $company_id;
        $pipeline->name = $name;
        $pipeline->description = !empty($input['description']) ? trim($input['description']) : null;
        $pipeline->is_default = $isDefault;
        $pipeline->status = 1;
        $pipeline->save();

        /*
         * Cuando se crea un pipeline,
         * creamos automáticamente sus etapas.
         */
        $this->createDefaultStages($pipeline,$company_id);
        return $pipeline->fresh();
    }

    public function update(Pipeline $pipeline, array $input): Pipeline {
        if(array_key_exists('name', $input)) {
            $name = trim($input['name']);
            if(empty($name)) {
                throw new \Exception('El nombre del pipeline es obligatorio.');
            }
            $existing = $this->pipelineRepository->getByName($name,$pipeline->company_id);
            if ( $existing && $existing->id !== $pipeline->id) {
                throw new \Exception( 'Ya existe otro pipeline con este nombre.');
            }
            $pipeline->name = $name;
        }
        if (array_key_exists('description', $input)) {
            $pipeline->description = !empty($input['description']) ? trim($input['description']) : null;
        }
        if ( isset($input['is_default']) && (int)$input['is_default'] === 1) {
            $this->setAsDefault($pipeline);
        }
        if (array_key_exists('status', $input)) {
            $pipeline->status = (int)$input['status'];
        }
        $pipeline->save();
        return $pipeline->fresh();
    }

    public function setAsDefault(Pipeline $pipeline): Pipeline {
        $this->removeCurrentDefault($pipeline->company_id);
        $pipeline->is_default = 1;
        $pipeline->save();
        return $pipeline->fresh();
    }

    public function createStage(Pipeline $pipeline, array $input): PipelineStage {
        $stageKey = strtoupper(trim($input['stage_key']));
        $existing = $this->pipelineStageRepository->getByStageKey($pipeline->id, $stageKey, $pipeline->company_id);
        if ($existing) {
            throw new \Exception('Ya existe una etapa con este código en el pipeline.');
        }

        $isWon = (int)($input['is_won'] ?? 0);
        $isLost = (int)($input['is_lost'] ?? 0);
        if ($isWon === 1 && $isLost === 1) {
            throw new \Exception('Una etapa no puede ser ganada y perdida al mismo tiempo.');
        }

        if ($isWon === 1) {
            $wonStage = $this->pipelineStageRepository->getWonStage($pipeline->id, $pipeline->company_id);
            if ($wonStage) {
                throw new \Exception('El pipeline ya cuenta con una etapa de negocio ganado.');
            }
        }
        if ($isLost === 1) {
            $lostStage = $this->pipelineStageRepository->getLostStage($pipeline->id, $pipeline->company_id);
            if ($lostStage) {
                throw new \Exception('El pipeline ya cuenta con una etapa de negocio perdido.');
            }
        }

        $position = !empty($input['position']) ? (int)$input['position'] : $this->pipelineStageRepository->getLastPosition($pipeline->id, $pipeline->company_id) + 1;
        $stage = new PipelineStage();
        $stage->company_id = $pipeline->company_id;
        $stage->pipeline_id = $pipeline->id;
        $stage->name = trim($input['name']);
        $stage->stage_key = $stageKey;
        $stage->position = $position;
        $stage->probability = isset($input['probability']) ? (float)$input['probability'] : 0;
        $stage->is_won = $isWon;
        $stage->is_lost = $isLost;
        $stage->status = 1;
        $stage->save();
        return $stage;
    }

    private function createDefaultStages(Pipeline $pipeline, int $company_id): void {
        $stages = PipelineConstant::defaultStages();
        foreach ($stages as $item) {
            $stage = new PipelineStage();
            $stage->company_id = $company_id;
            $stage->pipeline_id = $pipeline->id;
            $stage->name = $item['name'];
            $stage->stage_key = $item['stage_key'];
            $stage->position = $item['position'];
            $stage->probability = $item['probability'];
            $stage->is_won = $item['is_won'];
            $stage->is_lost = $item['is_lost'];
            $stage->status = 1;
            $stage->save();
        }
    }   

    private function removeCurrentDefault(int $company_id): void {
        $default = $this->pipelineRepository->getDefault($company_id);
        if(!$default) {
            return;
        }
        $default->is_default = 0;
        $default->save();    
    }
}