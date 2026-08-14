<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerContact extends Model
{
    use SoftDeletes;
    protected $table = 'customer_contacts';
    protected $fillable = [
        'id',
        'company_id',
        'customer_id',
        'name',
        'position',
        'email',
        'phone',
        'whatsapp',
        'is_primary',
        'notes',
        'status',
    ];

    public function customer() {
        return $this->belongsTo(Customers::class, 'customer_id');
    }
}
