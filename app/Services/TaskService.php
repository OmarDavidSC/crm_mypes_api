<?php

namespace App\Services;

use App\Constants\ActivityConstant;
use App\Constants\TaskConstant;
use App\Models\Task;
use App\Repositories\CustomerRepository;
use App\Repositories\LeadRepository;
use App\Repositories\OpportunityRepository;
use App\Utilities\FG;

class TaskService {
    private LeadRepository $leadRepository;
    private OpportunityRepository $opportunityRepository;
    private ActivityService $activityService;
    private CustomerRepository $customerRepository;

    public function __construct() {
        $this->leadRepository = new LeadRepository();
        $this->opportunityRepository = new OpportunityRepository();
        $this->activityService = new ActivityService();
        $this->customerRepository = new CustomerRepository();
    }

    public function create(array $input, int $company_id, int $user_id): Task {
        if (empty($input['lead_id']) && empty($input['customer_id']) && empty($input['opportunity_id'])) {
            throw new \Exception('La tarea debe estar asociada a un prospecto, cliente u oportunidad.');
        }

        $this->validateRelations($input, $company_id);
        $priority = strtoupper(trim($input['priority'] ?? TaskConstant::PRIORITY_MEDIUM));

        $this->validatePriority($priority);
        if (strtotime($input['due_date']) < time()) {
            throw new \Exception('La fecha de vencimiento de la tarea no puede ser anterior a la fecha actual.');
        }

        $task = new Task();
        $task->company_id = $company_id;
        $task->assigned_user_id = !empty($input['assigned_user_id']) ? (int)$input['assigned_user_id'] : $user_id;
        $task->created_by = $user_id;
        $task->lead_id = !empty($input['lead_id']) ? (int)$input['lead_id'] : null;
        $task->customer_id = !empty($input['customer_id']) ? (int)$input['customer_id'] : null;
        $task->opportunity_id = !empty($input['opportunity_id']) ? (int)$input['opportunity_id'] : null;
        $task->title = trim($input['title']);
        $task->description = !empty($input['description']) ? trim($input['description']) : null;
        $task->priority = $priority;
        $task->due_date = $input['due_date'];
        $task->completed = 0;
        $task->completed_at = null;
        $task->status = 1;
        $task->save();
        return $task->fresh();
    }

    public function update(Task $task, array $input): Task {
        if ($task->completed) {
            throw new \Exception('No se puede modificar una tarea completada.');
        }
        if (array_key_exists('assigned_user_id', $input)) {
            $task->assigned_user_id = !empty($input['assigned_user_id']) ? (int)$input['assigned_user_id'] : $task->assigned_user_id;
        }
        if (array_key_exists('title', $input)) {
            $title = trim($input['title']);
            if (empty($title)) {
                throw new \Exception('El título de la tarea es obligatorio.');
            }
            $task->title = $title;
        }
        if (array_key_exists('description', $input)) {
            $task->description = !empty($input['description']) ? trim($input['description']) : null;
        }
        if (array_key_exists('priority', $input)) {
            $priority = strtoupper(trim($input['priority']));
            $this->validatePriority($priority);
            $task->priority = $priority;
        }
        if (array_key_exists('due_date', $input) && !empty($input['due_date'])) {
            if (strtotime($input['due_date']) < time()) {
                throw new \Exception('La fecha de vencimiento no puede ser anterior a la fecha actual.');
            }
            $task->due_date = $input['due_date'];
        }
        if (array_key_exists('status', $input)) {
            $task->status = (int)$input['status'];
        }
        $task->save();
        return $task->fresh();
    }

    public function complete(Task $task, int $user_id): Task {
        if ($task->completed) {
            throw new \Exception('La tarea ya se encuentra completada.');
        }
        if ((int)$task->assigned_user_id !== $user_id && (int)$task->created_by !== $user_id) {
            throw new \Exception('No tiene permisos para completar esta tarea.');
        }
        $task->completed = 1;
        $task->completed_at = FG::getDateHour();
        $task->save();

        $this->activityService->createSystemActivity(
                    $task->company_id,
                    $user_id,
                    ActivityConstant::TYPE_FOLLOW_UP,
                    'Tarea completada',
                    "Se completó la tarea: {$task->title}.",
                    $task->lead_id,
                    $task->customer_id,
                    $task->opportunity_id
            );
        return $task->fresh();
    }

    public function reopen(Task $task, int $user_id
    ): Task {

        if (!$task->completed) {
            throw new \Exception('La tarea ya se encuentra pendiente.');
        }
        if ((int)$task->assigned_user_id !== $user_id && (int)$task->created_by !== $user_id) {
            throw new \Exception('No tiene permisos para reabrir esta tarea.');
        }
        $task->completed = 0;
        $task->completed_at = null;
        $task->save();
        return $task->fresh();
    }

    private function validatePriority(string $priority): void {
        if (!in_array($priority, TaskConstant::priorities(), true)) {
            throw new \Exception( 'La prioridad de la tarea no es válida.');
        }
    }

    private function validateRelations(array $input, int $company_id): void {
        if (!empty($input['lead_id'])) {
            $lead = $this->leadRepository->getById((int)$input['lead_id'], $company_id);
            if (!$lead) {
                throw new \Exception('El prospecto seleccionado no existe.');
            }
        }
        if (!empty($input['opportunity_id'])) {
            $opportunity =$this->opportunityRepository->getById((int)$input['opportunity_id'],$company_id);
            if (!$opportunity) {
                throw new \Exception('La oportunidad seleccionada no existe.');
            }
        }

        if (!empty($input['customer_id'])) {
            $customer = $this->customerRepository->getById((int)$input['customer_id'], $company_id);
            if (!$customer) {
                throw new \Exception('El cliente seleccionado no existe.');
            }
        }
    }
}