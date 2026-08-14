<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductsService extends Model
{

    use SoftDeletes;
    protected $table = 'products_services';
    protected $fillable = [
        'id',
        'company_id',
        'type',
        'name',
        'description',
        'code',
        'price',
        'tax_percentage',
        'status',
    ];
}
