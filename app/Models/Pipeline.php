<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pipeline extends Model
{

    use SoftDeletes;
    protected $table = 'pipelines';
    protected $fillable = [
        'id',
        'company_id',
        'name',
        'description',
        'is_default',
        'status',
    ];

    public function stages() {
        return $this->hasMany(PipelineStage::class, 'pipeline_id')->orderBy('position', 'ASC');
    }
}
