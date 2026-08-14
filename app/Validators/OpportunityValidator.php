<?php

namespace App\Validators;

class OpportunityValidator extends BaseValidator
{
    public static function store(array $data): array {
        return self::makeValidator($data, [
            'pipeline_id' => 'nullable|integer',
            'pipeline_stage_id' => 'nullable|integer',
            'lead_id' => 'nullable|integer',
            'customer_id' => 'nullable|integer',
            'assigned_user_id' => 'nullable|integer',
            'title' => 'required|max:255',
            'description' => 'nullable|max:5000',
            'estimated_value' => 'nullable|numeric|min:0',
            'probability' => 'nullable|numeric|min:0|max:100',
            'expected_close_date' => 'nullable|date',
        ]);
    }

    public static function update(array $data): array {
        return self::makeValidator($data, [
            'assigned_user_id' => 'nullable|integer',
            'title' => 'nullable|max:255',
            'description' => 'nullable|max:5000',
            'estimated_value' => 'nullable|numeric|min:0',
            'probability' => 'nullable|numeric|min:0|max:100',
            'expected_close_date' => 'nullable|date',
        ]);
    }

    public static function moveStage(array $data): array {
        return self::makeValidator($data, [
            'pipeline_stage_id' => 'required|integer',
            'lost_reason' => 'nullable|max:255',
            'lost_notes' => 'nullable|max:5000',
        ]);
    }
}
