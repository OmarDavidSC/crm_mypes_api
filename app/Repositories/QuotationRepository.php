<?php

namespace App\Repositories;

use App\Models\Quotation;
use Illuminate\Database\Eloquent\Collection;

class QuotationRepository {    
    public function getAllByCompany(int $company_id, int $status = 1): Collection {
        return Quotation::query()
                ->with(['customer', 'lead', 'opportunity', 'assignedUser'])
                ->where('company_id', $company_id)->where('status', $status)->whereNull('deleted_at')
                ->orderBy('id', 'DESC')->get();
    }

    public function getById(int $id, int $company_id): ?Quotation {
        return Quotation::query()
                ->with(['customer', 'lead', 'opportunity', 'createdByUser', 'assignedUser', 'items.productService', 'statusHistory.user'])
                ->where('id', $id)->where('company_id', $company_id)->whereNull('deleted_at')->first();
    }

    public function getByOpportunity(int $opportunity_id, int $company_id): Collection {
        return Quotation::query()
                ->where('company_id', $company_id)->where('opportunity_id', $opportunity_id)->whereNull('deleted_at')
                ->orderBy('id', 'DESC')->get();
    }

    public function getByCustomer(int $customer_id, int $company_id): Collection {
        return Quotation::query()
                ->where('company_id', $company_id)->where('customer_id', $customer_id)->whereNull('deleted_at')
                ->orderBy('id', 'DESC')->get();
    }

    public function getByNumber(string $quotation_number, int $company_id): ?Quotation {
        return Quotation::query()
                ->where('company_id', $company_id)->where('quotation_number', $quotation_number)->whereNull('deleted_at')
                ->first();
    }

    public function getLastIdByCompany(int $company_id): int {
        return (int) Quotation::query()
                ->where('company_id', $company_id)->withTrashed()->max('id');
    }

    public function getLastQuotationByCompany(int $company_id): ?Quotation {
        return Quotation::query()->withTrashed()->where('company_id', $company_id)->orderBy('id', 'DESC')->first();
    }
}
