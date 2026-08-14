<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotationItem extends Model
{

    use SoftDeletes;
    protected $table = 'quotation_items';
    protected $fillable = [
        'id',
        'quotation_id',
        'product_service_id',
        'description',
        'quantity',
        'unit_price',
        'discount',
        'tax_percentage',
        'subtotal',
        'tax',
        'total',
    ];
}