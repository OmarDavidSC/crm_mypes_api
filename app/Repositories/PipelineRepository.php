<?php

namespace App\Repositories;

use App\Models\Lead;
use App\Models\Pipeline;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Capsule\Manager as DB;

class PipelineRepository {
    public function getAllByCompany(int $company_id, int $status = 1): Collection {
        return Pipeline::query()
                ->where('company_id', $company_id)
                ->where('status', $status)
                ->whereNull('deleted_at')
                ->orderBy('is_default', 'DESC')
                ->orderBy('id', 'asc')
                ->get();
    }

    public function getAllWithStages(int $company_id, int $status = 1): Collection {
        return Pipeline::query()
                ->with(['stages' => function ($query) {
                        $query->where('status', 1)
                            ->whereNull('deleted_at')
                            ->orderBy('position', 'ASC');
                }])
                ->where('company_id', $company_id)
                ->where('status', $status)
                ->whereNull('deleted_at')
                ->orderBy('is_default', 'DESC')
                ->get();    
    }

    public function getById(int $id, int $company_id): ?Pipeline {
        return Pipeline::query()
                ->where('id', $id)
                ->where('company_id', $company_id)
                ->whereNull('deleted_at')
                ->first();
    }

    public function getByIdWithStages(int $id, int $company_id): ?Pipeline {
        return Pipeline::query()
                ->with(['stages' => function ($query) {
                    $query->whereNull('deleted_at')
                        ->orderBy('position', 'ASC');
                }])
                ->where('id', $id)
                ->where('company_id', $company_id)
                ->whereNull('deleted_at')
                ->first();
    }

    public function getDefault(int $company_id): ?Pipeline {
        return Pipeline::query()
                ->where('company_id', $company_id)
                ->where('is_default', 1)
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->first();
    }

    public function getByName(string $name, int $company_id): ?Pipeline {
        return Pipeline::query()
                ->where('company_id', $company_id)
                ->where('name',$name)
                ->wherNull('deleted_at')
                ->first();
    }
}
