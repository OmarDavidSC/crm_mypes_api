<?php

namespace App\Repositories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Collection;

class ActivityRepository {
    public function getAllByCompany(int $company_id, int $status = 1): Collection {
        return Activity::query()
                ->with(['user', 'lead', 'customer', 'opportunity'])
                ->where('company_id', $company_id)->where('status', $status)
                ->whereNull('deleted_at')->orderBy('activity_at', 'DESC')->orderBy('id', 'DESC')->get();
    }

    public function getById(int $id, int $company_id): ?Activity {
        return Activity::query()
                ->with(['user', 'lead', 'customer', 'opportunity'])
                ->where('id', $id)->where('company_id', $company_id)->whereNull('deleted_at')->first();
    }

    public function getByLead(int $lead_id, int $company_id): Collection {
        return Activity::query()
                ->with(['user', 'opportunity'])
                ->where('company_id', $company_id)->where('lead_id', $lead_id)->where('status', 1)
                ->whereNull('deleted_at')->orderBy('activity_at', 'DESC')->orderBy('id', 'DESC')->get();
    }

    public function getByCustomer(int $customer_id, int $company_id): Collection {
        return Activity::query()
                ->with(['user', 'opportunity'])
                ->where('company_id', $company_id)->where('customer_id', $customer_id)->where('status', 1)
                ->whereNull('deleted_at')->orderBy('activity_at', 'DESC')->orderBy('id', 'DESC')->get();
    }

    public function getByOpportunity(int $opportunity_id, int $company_id): Collection {
        return Activity::query()
                ->with(['user', 'lead', 'customer'])
                ->where('company_id', $company_id)->where('opportunity_id', $opportunity_id)->where('status', 1)
                ->whereNull('deleted_at')->orderBy('activity_at', 'DESC')->orderBy('id', 'DESC')->get();
    }

    public function getRecentByCompany(int $company_id, int $limit = 20): Collection {
        return Activity::query()
                ->with(['user', 'lead', 'customer', 'opportunity'])
                ->where('company_id', $company_id)->where('status', 1)->whereNull('deleted_at')
                ->orderBy('activity_at', 'DESC')->orderBy('id', 'DESC')->limit($limit)->get();
    }
}