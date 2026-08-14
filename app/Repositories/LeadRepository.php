<?php

namespace App\Repositories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Capsule\Manager as DB;

class LeadRepository {
    public function getAllByCompany(int $company_id, int $status = 1): Collection {
        return Lead::query()
                ->where('company_id', $company_id)
                ->where('status', $status)
                ->whereNull('deleted_at')
                ->orderBy('id', 'DESC')
                ->get();
    }

    public function getById(int $id, int $company_id): ?Lead {
        return Lead::query()
                ->where('id', $id)
                ->where('company_id', $company_id)
                ->whereNull('deleted_at')
                ->first();
    }

    public function getByEmail(string $email, int $company_id): ?Lead{
        return Lead::query()
                ->where('company_id', $company_id)
                ->where('email', $email)
                ->wherNull('deleted_at')
                ->first();
    }

    public function getByPhone(string $phone, int $company_id): ?Lead{
        return Lead::query()
                ->where('company_id', $company_id)
                ->where(function ($query) use ($phone){
                    $query->where('phone', $phone)
                            ->orWhere('whatsapp', $phone);
                })
                ->whereNull('deleted_at')
                ->first();
    }

    public function getByStatus(int $company_id, string $lead_status): Collection{
        return Lead::query()
                ->where('company_id', $company_id)
                ->where('lead_status', $lead_status)
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->get();
    }
}
