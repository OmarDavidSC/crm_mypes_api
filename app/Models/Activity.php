<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{

    use SoftDeletes;
    protected $table = 'activities';
    protected $fillable = [
        'id',
        'company_id',
        'user_id',
        'lead_id',
        'customer_id',
        'opportunity_id',
        'activity_type',
        'title',
        'description',
        'activity_at',
        'status',
    ];
}
