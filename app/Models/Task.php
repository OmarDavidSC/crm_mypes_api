<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{

    use SoftDeletes;
    protected $table = 'tasks';
    protected $fillable = [
        'id',
        'company_id',
        'assigned_user_id',
        'created_by',
        'lead_id',
        'customer_id',
        'opportunity_id',
        'title',
        'description',
        'priority',
        'due_date',
        'completed',
        'completed_at',
        'status',
    ];
}
