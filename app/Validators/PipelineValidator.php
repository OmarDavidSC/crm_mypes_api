<?php

namespace App\Validators;

class PipelineValidator extends BaseValidator {
   public static function store(array $data): array {
    return self::makeValidator($data, [
        'name' => 'required|max:255',
        'description' => 'nullable|max:5000',
        'is_default' => 'nullable|integer',
    ]);
   }

   public static function update(array $data): array {
    return self::makeValidator($data, [
        'name' => 'nullable|max:255',
        'description' => 'nullable|max:5000',
        'is_default' => 'nullable|integer',
        'status' => 'nullable|integer',
    ]);
   }
}
