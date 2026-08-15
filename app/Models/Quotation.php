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

    public function opportunity() {
        return $this->belongsTo(Opportunity::class, 'opportunity_id');
    }

    public function customer() {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function lead() {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function createdByUser() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUser() {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function items() {
        return $this->hasMany(QuotationItem::class, 'quotation_id');
    }

    public function statusHistory() {
        return $this->hasMany(QuotationStatusHistory::class, 'quotation_id')
                        ->orderBy('changed_at', 'DESC');
    }
}
