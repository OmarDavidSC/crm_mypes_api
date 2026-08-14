<?php

namespace App\Validators;

class LeadValidator  extends BaseValidator
{
    public static function store(array $data): array
    {
        return self::makeValidator($data, [
            'assigned_user_id' => 'nullable|integer',
            'name' => 'required|max:255',
            'business_name' => 'nullable|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|max:30',
            'whatsapp' => 'nullable|max:30',
            'source' => 'nullable|max:100',
            'interest' => 'nullable|max:255',
            'estimated_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|max:5000',
        ]);
    }

    public static function update(array $data): array
    {
        return self::makeValidator($data, [
            'assigned_user_id' => 'nullable|integer',
            'name' => 'nullable|max:255',
            'business_name' => 'nullable|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|max:30',
            'whatsapp' => 'nullable|max:30',
            'source' => 'nullable|max:100',
            'interest' => 'nullable|max:255',
            'estimated_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|max:5000',
        ]);
    }

    public static function changeStatus(array $data): array
    {
        return self::makeValidator($data, [
            'lead_status' => 'required|max:50',
        ]);
    }
}
