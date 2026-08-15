<?php

namespace App\Services;

use App\Constants\ProductServiceConstant;
use App\Models\ProductsService;
use App\Repositories\ProductServiceRepository;
use App\Utilities\FG;

class ProductServiceService {
    private ProductServiceRepository $productServiceRepository;

    public function __construct()
    {
        $this->productServiceRepository = new ProductServiceRepository();
    }

    public function create(array $input, int $company_id): ProductsService {
        $type = strtoupper(trim($input['type']));
        $this->validateType($type);
        $code = !empty($input['code']) ? strtoupper(trim($input['code'])) : null;

        if ($code) {
            $existing = $this->productServiceRepository->getByCode($code, $company_id);
            if ($existing) {
                throw new \Exception('Ya existe un producto o servicio con este código.');
            }
        }

        $price = round((float)$input['price'], 2);
        if ($price < 0) {
            throw new \Exception('El precio no puede ser negativo.');
        }

        $taxPercentage = isset($input['tax_percentage']) ? round((float)$input['tax_percentage'], 2) : 0;
        if ( $taxPercentage < 0 || $taxPercentage > 100) {
            throw new \Exception('El porcentaje de impuesto debe estar entre 0 y 100.');
        }

        $item = new ProductsService();
        $item->company_id = $company_id;
        $item->type = $type;
        $item->name = trim($input['name']);
        $item->description = !empty($input['description']) ? trim($input['description']) : null;
        $item->code = $code;
        $item->price = $price;
        $item->tax_percentage = $taxPercentage;
        $item->status = 1;
        $item->save();
        return $item->fresh();
    }

    public function update(ProductsService $item, array $input): ProductsService {
        if (array_key_exists('type', $input)) {
            $type = strtoupper(trim($input['type']));
            $this->validateType($type);
            $item->type = $type;
        }

        if (array_key_exists('name', $input)) {
            $name = trim($input['name']);
            if (empty($name)) {
                throw new \Exception('El nombre del producto o servicio es obligatorio.');
            }
            $item->name = $name;
        }

        if (array_key_exists('description', $input)) {
            $item->description = !empty($input['description']) ? trim($input['description']) : null;
        }

        if (array_key_exists('code', $input)) {
            $code = !empty($input['code']) ? strtoupper(trim($input['code'])) : null;
            if ($code) {
                $existing = $this->productServiceRepository->getByCode($code, $item->company_id);
                if ( $existing && (int)$existing->id !== (int)$item->id) {
                    throw new \Exception('Ya existe otro producto o servicio con este código.');
                }
            }
            $item->code = $code;
        }

        if (array_key_exists('price', $input)) {
            $price = round((float)$input['price'], 2);
            if ($price < 0) {
                throw new \Exception('El precio no puede ser negativo.');
            }
            $item->price = $price;
        }

        if (array_key_exists('tax_percentage', $input)) {
            $taxPercentage = round((float)$input['tax_percentage'],2);
            if ($taxPercentage < 0 || $taxPercentage > 100) {
                throw new \Exception('El porcentaje de impuesto debe estar entre 0 y 100.');
            }
            $item->tax_percentage = $taxPercentage;
        }
        if (array_key_exists('status', $input)) {
            $item->status = (int)$input['status'];
        }
        $item->save();
        return $item->fresh();
    }

    private function validateType(string $type): void {
        if (!in_array($type, ProductServiceConstant::types(), true)) {
            throw new \Exception('El tipo debe ser PRODUCT o SERVICE.');
        }
    }
}