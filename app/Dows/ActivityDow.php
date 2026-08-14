<?php

namespace App\Dows;

use App\Constants\ActivityConstant;
use App\Middlewares\Application;
use App\Repositories\ActivityRepository;
use App\Services\ActivityService;
use App\Utilities\FG;
use App\Validators\ActivityValidator;
use Illuminate\Database\Capsule\Manager as DB;

class ActivityDow {
    private ActivityRepository $activityRepository;
    private ActivityService $activityService;

    public function __construct() {
        $this->activityRepository = new ActivityRepository();
        $this->activityService = new ActivityService();
    }

    public function index($request) {
        $response = FG::responseDefault();
        try {
            $company_id = Application::getItem('company_id');
            $activities = $this->activityRepository->getAllByCompany($company_id);

            $data = $activities->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'activity_type' => $item->activity_type,
                        'title' => $item->title,
                        'description' => $item->description,
                        'activity_at' => $item->activity_at,
                        'user' => $item->user ? [
                                    'id' => $item->user->id,
                                    'name' => $item->user->name,] : null,
                        'lead' => $item->lead ? [
                                    'id' => $item->lead->id,
                                    'name' => $item->lead->name,] : null,
                        'customer' => $item->customer ? [
                                    'id' => $item->customer->id,
                                    'name' => $item->customer->name,] : null,
                        'opportunity' => $item->opportunity ? [
                                    'id' => $item->opportunity->id,
                                    'title' => $item->opportunity->title,] : null,
                        'status' => $item->status,
                        'created_at' => $item->created_at,
                    ];
                }
            );

            $response['success'] = true;
            $response['data'] = $data;
            $response['message'] = 'Actividades obtenidas correctamente.';
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

            $activity = $this->activityRepository->getById($id, $company_id);
            if (!$activity) {
                throw new \Exception('La actividad no fue encontrada.');
            }

            $response['success'] = true;
            $response['data'] = $activity;
            $response['message'] = 'Actividad obtenida correctamente.';
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function store($request)
    {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try { 
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');
            $user_id = Application::getItem('user_id');

            $errors = ActivityValidator::store($input);
            if (!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $activity = $this->activityService->create($input, $company_id, $user_id);
            DB::commit();

            $response['success'] = true;
            $response['data'] = $activity;
            $response['message'] = 'Actividad registrada correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function update($request)
    {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $id = (int)$request->getAttribute('id');
            $input = $request->getParsedBody();
            $company_id = Application::getItem('company_id');

            $errors = ActivityValidator::update($input);
            if (!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $activity = $this->activityRepository->getById($id, $company_id);
            if (!$activity) {
                $response['success'] = false;
                $response['message'] = 'La actividad no fue encontrada.';
                return $response;
            }

            $activity = $this->activityService->update($activity, $input);
            DB::commit();
            $response['success'] = true;
            $response['data'] = $activity;
            $response['message'] = 'Actividad actualizada correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function remove($request)
    {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');
            $activity = $this->activityRepository->getById($id, $company_id);

            if (!$activity) {
                $response['success'] = false;
                $response['message'] = 'La actividad no fue encontrada.';
                return $response;
            }

            if (!in_array($activity->activity_type, ActivityConstant::manualTypes(), true )) {
                $response['success'] = false;
                $response['message'] = 'Las actividades automáticas del sistema no pueden eliminarse.';
                return $response;
            }

            $activity->delete();
            DB::commit();

            $response['success'] = true;
            $response['data'] = null;
            $response['message'] = 'Actividad eliminada correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }
}
