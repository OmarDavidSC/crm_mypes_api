<?php

namespace App\Validators;

class ProductServiceValidator extends BaseValidator {
   public static function store(array $data): array {
    return self::makeValidator($data, [
        'type' => 'required|max:30',
        'name' => 'required|max:255',
        'description' => 'nullable|max:5000',
        'code' => 'nullable|max:100',
        'price' => 'required|numeric|min:0',
        'tax_percentage' => 'nullable|numeric|min:0|max:100',
    ]);
   }

   public static function update(array $data): array {
    return self::makeValidator($data, [
        'type' => 'nullable|max:30',
        'name' => 'nullable|max:255',
        'description' => 'nullable|max:5000',
        'code' => 'nullable|max:100',
        'price' => 'nullable|numeric|min:0',
        'tax_percentage' => 'nullable|numeric|min:0|max:100',
        'status' => 'nullable|integer',
    ]);
   }
}
