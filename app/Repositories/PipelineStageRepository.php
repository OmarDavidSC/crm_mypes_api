<?php

namespace App\Repositories;

use App\Models\PipelineStage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Capsule\Manager as DB;

class PipelineStageRepository {
    public function getByPipeline(int $pipeline_id, int $company_id, int $status = 1): Collection {
        return PipelineStage::query()
                ->where('company_id', $company_id)
                ->where('pipeline_id', $pipeline_id)
                ->where('status', $status)
                ->whereNull('deleted_at')
                ->orderBy('position', 'ASC')
                ->get();
    }

    public function getById(int $id, int $company_id): ?PipelineStage {
        return PipelineStage::query()->where('id', $id)->where('company_id', $company_id)->whereNull('deleted_at')->first();
    }

    public function getByStageKey(int $pipeline_id, string $stage_key, int $company_id): ?PipelineStage {
        return PipelineStage::query()
                ->where('company_id', $company_id)
                ->where('pipeline_id', $pipeline_id)
                ->where('stage_key', strtoupper(trim($stage_key)))
                ->where('status', 1)
                ->whereNull('deleted_at')->first();
    }

    public function getInitialStage(int $pipeline_id, int $company_id): ?PipelineStage {
        return PipelineStage::query()
                ->where('company_id', $company_id)
                ->where('pipeline_id', $pipeline_id)
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('position', 'ASC')->first();
    }

    public function getLastPosition(int $pipeline_id, int $company_id): int {
        return (int) PipelineStage::query()
                ->where('company_id', $company_id)->where('pipeline_id', $pipeline_id)
                ->whereNull('deleted_at')->max('position');
    }

    public function getWonStage(int $pipeline_id, int $company_id): ?PipelineStage {
        return PipelineStage::query()
                ->where('company_id', $company_id)->where('pipeline_id', $pipeline_id)
                ->where('is_won', 1)->where('status', 1)->whereNull('deleted_at')->first();
    }

    public function getLostStage(int $pipeline_id, int $company_id): ?PipelineStage {
        return PipelineStage::query()
                ->where('company_id', $company_id)->where('pipeline_id', $pipeline_id)
                ->where('is_lost', 1)->where('status', 1)->whereNull('deleted_at')->first();
    }
}
