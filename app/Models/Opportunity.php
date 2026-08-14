<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunity extends Model
{

    use SoftDeletes;
    protected $table = 'opportunities';
    protected $fillable = [
        'id',
        'company_id',
        'pipeline_id',
        'pipeline_stage_id',
        'lead_id',
        'customer_id',
        'assigned_user_id',
        'title',
        'description',
        'estimated_value',
        'probability',
        'expected_close_date',
        'closed_at',
        'lost_reason',
        'lost_notes',
        'status',
    ];
}
