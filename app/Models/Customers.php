<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customers extends Model
{

    use SoftDeletes;
    protected $table = 'customers';
    protected $fillable = [
        'id',
        'company_id',
        'assigned_user_id',
        'customer_type',
        'name',
        'business_name',
        'document_type',
        'document_number',
        'email',
        'phone',
        'whatsapp',
        'address',
        'source',
        'notes',
        'status',
    ];
}
