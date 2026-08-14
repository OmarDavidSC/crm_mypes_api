<?php

namespace App\Validators;

class ActivityValidator extends BaseValidator
{
    public static function store(array $data): array {
        return self::makeValidator($data, [
            'lead_id' => 'nullable|integer',
            'customer_id' => 'nullable|integer',
            'opportunity_id' => 'nullable|integer',
            'activity_type' => 'required|max:50',
            'title' => 'required|max:255',
            'description' => 'nullable|max:5000',
            'activity_at' => 'nullable|date',
        ]);
    }

    public static function update(array $data): array {
        return self::makeValidator($data, [
            'activity_type' => 'nullable|max:50',
            'title' => 'nullable|max:255',
            'description' => 'nullable|max:5000',
            'activity_at' => 'nullable|date',
            'status' => 'nullable|integer',
        ]);
    }
}
