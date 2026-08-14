<?php

namespace App\Validators;

class CustomerContactValidator extends BaseValidator
{
    public static function store(array $data): array {
        return self::makeValidator($data, [
            'customer_id' => 'required|integer',
            'name' => 'required|max:255',
            'position' => 'nullable|max:150',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|max:30',
            'whatsapp' => 'nullable|max:30',
            'is_primary' => 'nullable|integer',
            'notes' => 'nullable|max:5000',
        ]);
    }

    public static function update(array $data): array {
        return self::makeValidator($data, [
            'name' => 'nullable|max:255',
            'position' => 'nullable|max:150',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|max:30',
            'whatsapp' => 'nullable|max:30',
            'is_primary' => 'nullable|integer',
            'notes' => 'nullable|max:5000',
            'status' => 'nullable|integer',
        ]);
    }
}
