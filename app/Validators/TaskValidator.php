<?php

namespace App\Validators;

class TaskValidator extends BaseValidator
{
    public static function store(array $data): array {
        return self::makeValidator($data, [
            'assigned_user_id' => 'nullable|integer',
            'lead_id' => 'nullable|integer',
            'customer_id' => 'nullable|integer',
            'opportunity_id' => 'nullable|integer',
            'title' => 'required|max:255',
            'description' => 'nullable|max:5000',
            'priority' => 'nullable|max:30',
            'due_date' => 'required|date',
        ]);
    }

    public static function update(array $data): array {
        return self::makeValidator($data, [
            'assigned_user_id' => 'nullable|integer',
            'title' => 'nullable|max:255',
            'description' => 'nullable|max:5000',
            'priority' => 'nullable|max:30',
            'due_date' => 'nullable|date',
            'status' => 'nullable|integer',
        ]);
    }
}
