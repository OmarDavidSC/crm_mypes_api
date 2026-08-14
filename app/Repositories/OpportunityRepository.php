<?php

namespace App\Repositories;

use App\Models\Opportunity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Capsule\Manager as DB;

class OpportunityRepository {
    public function getAllByCompany(int $company_id, int $status = 1): Collection {
        return Opportunity::query()
                ->with(['pipeline', 'stage', 'lead', 'custoner', 'assignedUser'])
                ->where('company_id', $company_id)
                ->where('status', $status)->whereNull('deleted_at')
                ->orderBy('id', 'DESC')->get();
    }

    public function getById(int $id, int $company_id): ?Opportunity {
        return Opportunity::query()
                ->with(['pipeline', 'stage', 'lead', 'custoner', 'assignedUser'])
                ->where('id', $id)->where('company_id', $company_id)
                ->whereNull('deleted_at')->first();
    }

    public function getByPipeline(int $pipeline_id, int $company_id): Collection {
        return Opportunity::query()
                ->with(['stage', 'lead', 'customer', 'assignedUser'])
                ->where('company_id', $company_id)->where('pipeline_id', $pipeline_id)
                ->where('status', 1)->whereNull('deleted_at')
                ->orderBy('id', 'DESC')->get();
    }

    public function getByStage(int $pipeline_stage_id, int $company_id): Collection {
        return Opportunity::query()
                ->with(['lead','customer','assignedUser'])
                ->where('company_id', $company_id)->where('pipeline_stage_id', $pipeline_stage_id)
                ->where('status', 1)->whereNull('deleted_at')
                ->orderBy('id', 'DESC')->get();
    }

    public function getByLead(int $lead_id, int $company_id): Collection {
        return Opportunity::query()
                ->with(['pipeline','stage'])
                ->where('company_id', $company_id)->where('lead_id', $lead_id)
                ->where('status', 1)->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
    }

    public function getByCustomer(int $customer_id, int $company_id): Collection {
        return Opportunity::query()
                ->with(['pipeline','stage'])
                ->where('company_id', $company_id)->where('customer_id', $customer_id)->where('status', 1)
                ->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
    }

    public function getByAssignedUser(int $assigned_user_id, int $company_id): Collection {
        return Opportunity::query()
                ->with(['pipeline','stage','lead','customer'])
                ->where('company_id', $company_id)->where('assigned_user_id',$assigned_user_id)
                ->where('status', 1)->whereNull('deleted_at')
                ->orderBy('id', 'DESC')->get();
    }
}
