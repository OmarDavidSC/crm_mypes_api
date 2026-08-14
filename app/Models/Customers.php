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

    public function assignedUser() {
        return $this->belongsTo(User::class,'assigned_user_id');
    }

    public function contacts() {
        return $this->hasMany(CustomerContact::class, 'customer_id');
    }

    public function opportunities() {
        return $this->hasMany(Opportunity::class, 'customer_id');
    }

    public function activities() {
        return $this->hasMany(Activity::class, 'customer_id');
    }

    public function tasks() {
        return $this->hasMany(Task::class, 'customer_id');
    }

    public function quotations() {
        return $this->hasMany(Quotation::class, 'customer_id');
    }
}
