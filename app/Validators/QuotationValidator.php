<?php

namespace App\Validators;

class QuotationValidator extends BaseValidator
{
    public static function store(array $data): array {
        return self::makeValidator($data, [
            'opportunity_id' => 'nullable|integer',
            'customer_id' => 'nullable|integer',
            'lead_id' => 'nullable|integer',
            'assigned_user_id' => 'nullable|integer',
            'quotation_date' => 'required|date',
            'expiration_date' => 'nullable|date',
            'currency' => 'nullable|max:10',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|max:5000',
            'terms_conditions' => 'nullable|max:10000',
            'items' => 'required',
        ]);
    }

    public static function update(array $data): array {
        return self::makeValidator($data, [
            'assigned_user_id' => 'nullable|integer',
            'quotation_date' => 'nullable|date',
            'expiration_date' => 'nullable|date',
            'currency' => 'nullable|max:10',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|max:5000',
            'terms_conditions' => 'nullable|max:10000',
            'items' => 'nullable',
        ]);
    }

    public static function changeStatus(array $data): array {
        return self::makeValidator($data, [
            'quotation_status' => 'required|max:30',
            'notes' => 'nullable|max:5000',
        ]);
    }
}
