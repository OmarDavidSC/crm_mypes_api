<?php

namespace App\Constants;

class QuotationConstant {
    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_SENT = 'SENT';
    public const STATUS_VIEWED = 'VIEWED';
    public const STATUS_ACCEPTED = 'ACCEPTED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_EXPIRED = 'EXPIRED';
    public const STATUS_CANCELLED = 'CANCELLED';

    public const CURRENCY_PEN = 'PEN';
    public const CURRENCY_USD = 'USD';

    public static function statuses(): array {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SENT,
            self::STATUS_VIEWED,
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED,
            self::STATUS_EXPIRED,
            self::STATUS_CANCELLED
        ];
    }

    public static function currencies(): array {
        return[self::CURRENCY_PEN, self::CURRENCY_USD];
    }

    public static function finalStatuses(): array {
        return [
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED,
            self::STATUS_EXPIRED,
            self::STATUS_CANCELLED,
        ]; 
    }
}
