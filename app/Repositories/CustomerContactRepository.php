<?php

namespace App\Repositories;

use App\Models\CustomerContact;
use Illuminate\Database\Eloquent\Collection;

class CustomerContactRepository {
    public function getByCustomer(int $customer_id, int $company_id, int $status = 1): Collection {
        return CustomerContact::query()
                ->where('company_id', $company_id)->where('customer_id', $customer_id)->where('status', $status)
                ->whereNull('deleted_at')->orderBy('is_primary', 'DESC')->orderBy('name', 'ASC')->get();
    }

    public function getById(int $id, int $company_id): ?CustomerContact {
        return CustomerContact::query()
                ->where('id', $id)->where('company_id', $company_id)->whereNull('deleted_at')->first();
    }

    public function getPrimary(int $customer_id, int $company_id): ?CustomerContact {
        return CustomerContact::query()
                ->where('company_id', $company_id)->where('customer_id', $customer_id)->where('is_primary', 1)
                ->where('status', 1)->whereNull('deleted_at')->first();
    }
}
