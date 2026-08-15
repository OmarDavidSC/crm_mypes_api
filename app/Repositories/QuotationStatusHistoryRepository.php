<?php

namespace App\Repositories;

use App\Models\QuotationStatusHistory;
use Illuminate\Database\Eloquent\Collection;

class QuotationStatusHistoryRepository {    
    public function getByQuotation(int $quotation_id, int $company_id): Collection {
        return QuotationStatusHistory::query()
                ->with('user')->where('company_id', $company_id)->where('quotation_id', $quotation_id)
                ->whereNull('deleted_at')->orderBy('changed_at', 'DESC')->orderBy('id', 'DESC')->get();
    }
}
