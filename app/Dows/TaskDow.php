<?php

namespace App\Dows;

use App\Middlewares\Application;
use App\Repositories\TaskRepository;
use App\Services\TaskService;
use App\Utilities\FG;
use App\Validators\TaskValidator;
use Illuminate\Database\Capsule\Manager as DB;

class TaskDow {
    private TaskRepository $taskRepository;
    private TaskService $taskService;

    public function __construct() {
        $this->taskRepository = new TaskRepository();
        $this->taskService = new TaskService();
    }

    public function index($request) {
        $response = FG::responseDefault();
        try {
            $company_id = Application::getItem('company_id');
            $tasks = $this->taskRepository->getAllByCompany($company_id);
            $data = $tasks->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'description' => $item->description,
                        'priority' => $item->priority,
                        'due_date' => $item->due_date,
                        'completed' => (bool)$item->completed,
                        'completed_at' => $item->completed_at,
                        'assigned_user' => $item->assignedUser ? [
                                    'id' => $item->assignedUser->id,
                                    'name' => $item->assignedUser->name,] : null,
                        'created_by' =>$item->createdByUser ? [
                                    'id' => $item->createdByUser->id,
                                    'name' => $item->createdByUser->name,]: null,
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
            $response['message'] = 'Tareas obtenidas correctamente.';
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function mytasks($request) {
        $response = FG::responseDefault();
        try {
            $company_id = Application::getItem('company_id');
            $user_id = Application::getItem('user_id');
            $tasks = $this->taskRepository->getPendingByUser($user_id,$company_id);
            $today = $this->taskRepository->getTodayByUser($user_id,$company_id);
            $overdue =$this->taskRepository->getOverdueByUser($user_id,$company_id);

            $response['success'] = true;
            $response['data'] = [
                'summary' => ['pending' => $tasks->count(),
                    'today' => $today->count(),
                    'overdue' => $overdue->count(),
                ],
                'tasks' => $tasks
            ];
            $response['message'] = 'Tareas obtenidas correctamente.';

        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function show($request)
    {
        $response = FG::responseDefault();
        try {
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');

            $task = $this->taskRepository->getById($id, $company_id);
            if (!$task) {
                $response['success'] = false;
                $response['message'] = 'La tarea no fue encontrada.';
                return $response;
            }

            $response['success'] = true;
            $response['data'] = $task;
            $response['message'] = 'Tarea obtenida correctamente.';
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

            $errors = TaskValidator::store($input);
            if (!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $task = $this->taskService->create($input, $company_id, $user_id);
            DB::commit();

            $response['success'] = true;
            $response['data'] = $task;
            $response['message'] = 'Tarea registrada correctamente.';
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

            $errors = TaskValidator::update($input);

            if (!empty($errors)) {
                $response['success'] = false;
                $response['message'] = implode(', ', $errors);
                return $response;
            }

            $task = $this->taskRepository->getById($id, $company_id);
            if (!$task) {
                $response['success'] = false;
                $response['message'] = 'La tarea no fue encontrada.';
                return $response;
            }

            $task = $this->taskService->update($task, $input);
            DB::commit();

            $response['success'] = true;
            $response['data'] = $task;
            $response['message'] = 'Tarea actualizada correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function complete($request)
    {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');
            $user_id = Application::getItem('user_id');
            $task = $this->taskRepository->getById($id, $company_id);
            if (!$task) {
                $response['success'] = false;
                $response['message'] = 'La tarea no fue encontrada.';
                return $response;
            }

            $task = $this->taskService->complete($task, $user_id);
            DB::commit();

            $response['success'] = true;
            $response['data'] = $task;
            $response['message'] = 'Tarea completada correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }

    public function reopen($request)
    {
        $response = FG::responseDefault();
        DB::beginTransaction();
        try {
            $id = (int)$request->getAttribute('id');
            $company_id = Application::getItem('company_id');
            $user_id = Application::getItem('user_id');

            $task = $this->taskRepository->getById($id, $company_id);
            if (!$task) {
                $response['success'] = false;
                $response['message'] = 'La tarea no fue encontrada.';
                return $response;
            }

            $task = $this->taskService->reopen($task, $user_id);
            DB::commit();

            $response['success'] = true;
            $response['data'] = $task;
            $response['message'] = 'Tarea reabierta correctamente.';
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

            $task = $this->taskRepository->getById($id, $company_id);
            if (!$task) {
                $response['success'] = false;
                $response['message'] = 'La tarea no fue encontrada.';
                return $response;
            }

            if ($task->completed) {
                $response['success'] = false;
                $response['message'] = 'No se puede eliminar una tarea completada.';
                return $response;
            }

            $task->delete();
            DB::commit();
            $response['success'] = true;
            $response['data'] = null;
            $response['message'] = 'Tarea eliminada correctamente.';
        } catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['message'] = $e->getMessage();
        }
        return $response;
    }    
}   
