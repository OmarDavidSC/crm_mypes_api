<?php

namespace App\Constants;

class ProductServiceConstant {
    public const TYPE_PRODUCT = 'PRODUCT';
    public const TYPE_SERVICE = 'SERVICE';

    public static function types(): array {
        return [self::TYPE_PRODUCT, self::TYPE_SERVICE];
    }
}
