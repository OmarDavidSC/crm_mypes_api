<?php

namespace App\Validators;

class CustomerValidator extends BaseValidator
{
    public static function store(array $data): array {
        return self::makeValidator($data, [
            'assigned_user_id' => 'nullable|integer',
            'customer_type' => 'required|max:30',
            'name' => 'required|max:255',
            'business_name' => 'nullable|max:255',
            'document_type' => 'nullable|max:20',
            'document_number' => 'nullable|max:30',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|max:30',
            'whatsapp' => 'nullable|max:30',
            'address' => 'nullable|max:500',
            'source' => 'nullable|max:100',
            'notes' => 'nullable|max:5000',
        ]);
    }

    public static function update(array $data): array {
        return self::makeValidator($data, [
            'assigned_user_id' => 'nullable|integer',
            'customer_type' => 'nullable|max:30',
            'name' => 'nullable|max:255',
            'business_name' => 'nullable|max:255',
            'document_type' => 'nullable|max:20',
            'document_number' => 'nullable|max:30',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|max:30',
            'whatsapp' => 'nullable|max:30',
            'address' => 'nullable|max:500',
            'source' => 'nullable|max:100',
            'notes' => 'nullable|max:5000',
            'status' => 'nullable|integer',
        ]);
    }
}
