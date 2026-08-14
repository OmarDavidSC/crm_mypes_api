<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PipelineStage extends Model
{

    use SoftDeletes;
    protected $table = 'pipeline_stages';
    protected $fillable = [
        'id',
        'company_id',
        'pipeline_id',
        'name',
        'stage_key',
        'position',
        'probability',
        'is_won',
        'is_lost',
        'status',
    ];

    public function pipeline() {
        return $this->belongsTo(Pipeline::class, 'pipeline_id');
    }
}
