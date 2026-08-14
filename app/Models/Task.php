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

    public function assignedUser() {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function createdByUser() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lead() {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function customer() {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function opportunity() {
        return $this->belongsTo(Opportunity::class, 'opportunity_id');
    }
}
