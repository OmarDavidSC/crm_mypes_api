<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationStatusHistory extends Model
{

    use SoftDeletes;
    protected $table = 'quotation_status_history';
    protected $fillable = [
        'id',
        'company_id',
        'quotation_id',
        'user_id',
        'previous_status',
        'new_status',
        'notes',
        'changed_at',
    ];

    public function quotation() {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
