<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository {
    public function getAllByCompany(int $company_id, int $status = 1): Collection {
        return Task::query()
                ->with(['assignedUser', 'createdByUser', 'lead', 'customer', 'opportunity'])
                ->where('company_id', $company_id)->where('status', $status)->whereNull('deleted_at')
                ->orderBy('completed', 'ASC')->orderBy('due_date', 'ASC')->get();
    }

    public function getById(int $id, int $company_id): ?Task {
        return Task::query()
                ->with(['assignedUser', 'createdByUser', 'lead', 'customer', 'opportunity'])
                ->where('id', $id)->where('company_id', $company_id)
                ->whereNull('deleted_at')->first();
    }

    public function getByAssignedUser(int $assigned_user_id, int $company_id): Collection {
        return Task::query()
                ->with(['lead', 'customer', 'opportunity'])
                ->where('company_id', $company_id)->where('assigned_user_id', $assigned_user_id)->where('status', 1)
                ->whereNull('deleted_at')->orderBy('completed', 'ASC')->orderBy('due_date', 'ASC')->get();
    }

    public function getPendingByUser(int $assigned_user_id, int $company_id): Collection {
        return Task::query()
                ->with(['lead', 'customer', 'opportunity'])
                ->where('company_id', $company_id)->where('assigned_user_id', $assigned_user_id)
                ->where('completed', 0)->where('status', 1)->whereNull('deleted_at')
                ->orderBy('due_date', 'ASC')->get();
    }

    public function getTodayByUser(int $assigned_user_id, int $company_id): Collection {
        $today = date('Y-m-d');
        return Task::query()
                ->with(['lead', 'customer', 'opportunity'])
                ->where('company_id', $company_id)->where('assigned_user_id', $assigned_user_id)
                ->where('completed', 0)->where('status', 1)->whereDate('due_date', $today)
                ->whereNull('deleted_at')->orderBy('due_date', 'ASC')->get();
    }

    public function getOverdueByUser(int $assigned_user_id, int $company_id): Collection {
        $now = date('Y-m-d H:i:s');
        return Task::query()
                ->with(['lead', 'customer', 'opportunity'])
                ->where('company_id', $company_id)->where('assigned_user_id', $assigned_user_id)
                ->where('completed', 0)->where('status', 1)->where('due_date', '<', $now)
                ->whereNull('deleted_at')->orderBy('due_date', 'ASC')->get();
    }

    public function getByLead(int $lead_id, int $company_id): Collection {
        return Task::query()
                ->with('assignedUser')->where('company_id', $company_id)
                ->where('lead_id', $lead_id)->where('status', 1)->whereNull('deleted_at')
                ->orderBy('due_date', 'ASC')->get();
    }

    public function getByOpportunity(int $opportunity_id, int $company_id): Collection {
        return Task::query()
                ->with('assignedUser')->where('company_id', $company_id)
                ->where('opportunity_id', $opportunity_id)->where('status', 1)->whereNull('deleted_at')
                ->orderBy('due_date', 'ASC')->get();
    }
}
