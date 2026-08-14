<?php

namespace App\Validators;

class PipelineStageValidator extends BaseValidator {
   public static function store(array $data): array {
    return self::makeValidator($data, [
        'pipeline_id' => 'required|integer',
        'name' => 'required|max:255',
        'stage_key' => 'required|max:100',
        'position' => 'nullable|integer|min:1',
        'probability' => 'nullable|numeric|min:0|max:100',
        'is_won' => 'nullable|integer',
        'is_lost' => 'nullable|integer',
    ]);
   }

   public static function update(array $data): array {
    return self::makeValidator($data, [
        'name' => 'nullable|max:255',
        'position' => 'nullable|integer|min:1',
        'probability' => 'nullable|numeric|min:0|max:100',
        'is_won' => 'nullable|integer',
        'is_lost' => 'nullable|integer',
        'status' => 'nullable|integer',
    ]);
   }
}
