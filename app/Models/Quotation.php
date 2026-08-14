<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{

    use SoftDeletes;
    protected $table = 'quotations';
    protected $fillable = [
        'id',
        'company_id',
        'opportunity_id',
        'customer_id',
        'lead_id',
        'created_by',
        'assigned_user_id',
        'quotation_number',
        'quotation_date',
        'expiration_date',
        'currency',
        'subtotal',
        'discount',
        'tax',
        'total',
        'quotation_status',
        'notes',
        'terms_conditions',
        'sent_at',
        'accepted_at',
        'rejected_at',
        'status',
    ];
}
