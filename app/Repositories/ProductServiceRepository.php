<?php

namespace App\Repositories;

use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\ProductsService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Capsule\Manager as DB;

class ProductServiceRepository {
    public function getAllByCompany(int $company_id, int $status = 1): Collection {
        return ProductsService::query()
                ->where('company_id', $company_id)->where('status', $status)->whereNull('deleted_at')
                ->orderBy('name', 'ASC')->get();
    }

    public function getById(int $id, int $company_id): ?ProductsService {
        return ProductsService::query()
                ->where('id', $id)->where('company_id', $company_id)->whereNull('deleted_at')->first();
    }

    public function getByCode(string $code, int $company_id): ?ProductsService {
        return ProductsService::query()
                ->where('company_id', $company_id)->where('code', $code)->whereNull('deleted_at')->first();
    }

    public function getByType(int $company_id, string $type, int $status = 1): Collection {
        return ProductsService::query()
                ->where('company_id', $company_id)->where('type', strtoupper(trim($type)))->where('status', $status)
                ->whereNull('deleted_at')->orderBy('name', 'ASC')->get();
    }

    public function search(int $company_id, string $search, ?string $type = null): Collection {
        $query = ProductsService::query()
                ->where('company_id', $company_id)->where('status', 1)->whereNull('deleted_at');

        if (!empty($type)) {
            $query->where('type', strtoupper(trim($type)));
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        return $query->orderBy('name', 'ASC')->get();
    }

    public function getActiveForQuotation(int $company_id): Collection {
        return ProductsService::query()
                ->select(['id', 'type', 'code', 'name', 'description', 'price', 'tax_percentage'])
                ->where('company_id', $company_id)->where('status', 1)->whereNull('deleted_at')
                ->orderBy('name', 'ASC')->get();
    }
}
