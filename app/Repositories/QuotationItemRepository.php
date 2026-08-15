<?php

namespace App\Repositories;

use App\Models\QuotationItem;
use Illuminate\Database\Eloquent\Collection;

class QuotationItemRepository {    
    public function getByQuotation(int $quotation_id): Collection {
        return QuotationItem::query()
                ->with('productService')->where('quotation_id', $quotation_id)->whereNull('deleted_at')
                ->orderBy('id', 'ASC')->get();
    }
}
