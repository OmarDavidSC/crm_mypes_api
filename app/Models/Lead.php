<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{

    use SoftDeletes;
    protected $table = 'leads';
    protected $fillable = [
        'id',
        'company_id',
        'assigned_user_id',
        'name',
        'business_name',
        'email',
        'phone',
        'whatsapp',
        'source',
        'interest',
        'estimated_value',
        'notes',
        'lead_status',
        'converted',
        'converted_customer_id',
        'converted_at',
        'status',
    ];
}
