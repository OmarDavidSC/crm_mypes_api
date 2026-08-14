<?php

namespace App\Dows;

use App\Middlewares\Application;
use App\Repositories\PipelineRepository;
use App\Services\PipelineService;
use App\Utilities\FG;
use App\Validators\PipelineStageValidator;
use App\Validators\PipelineValidator;
use Illuminate\Database\Capsule\Manager as DB;

class LeadDow {
    private PipelineRepository $pipelineRepository;
    private PipelineService $pipelineService;

    public function __construct()
    {
        $this->pipelineRepository = new PipelineRepository();
        $this->pipelineService = new PipelineService();
    }

    public function index($request) {
        $response = FG::responseDefault();
        try {
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');
            $pipelines = $this->pipelineRepository->getAllWithStages($company_id);

            $data = $pipelines->map(function ($pipeline) {
                    return [
                        'id' => $pipeline->id,
                        'name' => $pipeline->name,
                        'description' => $pipeline->description,
                        'is_default' => (bool)$pipeline->is_default,
                        'status' => $pipeline->status,
                        'stages' => $pipeline->stages->map(function ($stage) {
                                    return [
                                        'id' =>  $stage->id,
                                        'name' => $stage->name,
                                        'stage_key' => $stage->stage_key,
                                        'position' => $stage->position,
                                        'probability' => (float)$stage->probability,
                                        'is_won' => (bool)$stage->is_won,
                                        'is_lost' => (bool)$stage->is_lost,
                                    ];})->values()
                    ];
                }
            );

            $response['success'] = true;
            $response['data'] = $data;
            $response['message'] = 'Pipelines obtenidos correctamente.';
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function show($request) {
        $response = FG::responseDefault();
        try {
            $input = $request->getParsedBody();
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');

            $pipeline = $this->pipelineRepository->getByIdWithStages($id, $company_id);
            if (!$pipeline) {
                $response['success'] = false;
                $response['message'] = 'El pipeline no fue encontrado';
                return $response; 
            }
          
            $response['success'] = true;
            $response['data'] = [
                'id' => $pipeline->id,
                'name' => $pipeline->name,
                'description' => $pipeline->description,
                'is_default' => (bool)$pipeline->is_default,
                'status' => $pipeline->status,
                'stages' => $pipeline->stages->map(function ($stage) {
                            return [
                                'id' => $stage->id,
                                'name' => $stage->name,
                                'stage_key' => $stage->stage_key,
                                'position' => $stage->position,
                                'probability' => (float)$stage->probability,
                                'is_won' => (bool)$stage->is_won,
                                'is_lost' => (bool)$stage->is_lost,
                                'status' => $stage->status,
                            ];})->values()
            ];
            $response['message'] = 'Pipeline obtenido correctamente.';
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

            $erros = PipelineValidator::store($input);
            if(!empty($erros)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $erros);
                return $response;
            }

            $pipeline = $this->pipelineService->create($input, $company_id);
            DB::commit();

            $response['success'] = true;
            $response['data'] = $pipeline->load('stages');
            $response['message'] = 'Pipeline registrado correctamente.';
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
            $input = $request->getParsedBody();
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');

            $erros = PipelineValidator::update($input);
            if(!empty($erros)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $erros);
                return $response;
            }

            $pipeline = $this->pipelineRepository->getById($id, $company_id);
            if(!$pipeline) {
                $response['success'] = false;
                $response['message'] = 'El pipeline no fue encontrado.';
                return $response;
            }

            $pipeline = $this->pipelineService->update($pipeline,$input);
            DB::commit();

            $response['success'] = true;
            $response['data'] = $pipeline;
            $response['message'] = 'Pipeline actualizado correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function stage($request) {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');
            $errors = PipelineStageValidator::store($input);
            if (!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $pipeline = $this->pipelineRepository->getById((int)$input['pipeline_id'], $company_id);
            if (!$pipeline) {
                $response['success'] = false;
                $response['message'] = 'El pipeline no fue encontrado';
                return $response;
            }

            $stage = $this->pipelineService->createStage($pipeline, $input);
            DB::commit();

            $response['success'] = true;
            $response['data'] = $stage;
            $response['message'] = 'Etapa registrada correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function setDefault($request)
    {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');
            $pipeline = $this->pipelineRepository->getById($id, $company_id);

            if (!$pipeline) {
                $response['success'] = false;
                $response['message'] = 'El pipeline no fue encontrado';
                return $response;
            }
            $pipeline = $this->pipelineService->setAsDefault($pipeline);

            DB::commit();
            $response['success'] = true;
            $response['data'] = $pipeline;
            $response['message'] = 'Pipeline establecido como predeterminado.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }
}
