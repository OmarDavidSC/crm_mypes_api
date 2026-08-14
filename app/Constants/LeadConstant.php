<?php

namespace App\Constants;

class LeadConstant {
    public const STATUS_NEW = 'NEW';
    public const STATUS_CONTACTED = 'CONTACTED';
    public const STATUS_INTERESTED = 'INTERESTED';
    public const STATUS_QUALIFIED = 'QUALIFIED';
    public const STATUS_CONVERTED = 'CONVERTED';
    public const STATUS_LOST = 'LOST';

    public const SOURCE_WHATSAPP = 'WHATSAPP';
    public const SOURCE_FACEBOOK = 'FACEBOOK';
    public const SOURCE_INSTAGRAM = 'INSTAGRAM';
    public const SOURCE_WEBSITE = 'WEBSITE';
    public const SOURCE_REFERRAL = 'REFERRAL';
    public const SOURCE_MANUAL = 'MANUAL';
    public const SOURCE_OTHER = 'OTHER';

    public static function statuses(): array {
        return [
            self::STATUS_NEW,
            self::STATUS_CONTACTED,
            self::STATUS_INTERESTED,
            self::STATUS_QUALIFIED,
            self::STATUS_CONVERTED,
            self::STATUS_LOST
        ];
    }

    public static function sources(): array {
        return [
            self::SOURCE_WHATSAPP,
            self::SOURCE_FACEBOOK,
            self::SOURCE_INSTAGRAM,
            self::SOURCE_WEBSITE,
            self::SOURCE_REFERRAL,
            self::SOURCE_MANUAL,
            self::SOURCE_OTHER
        ];
    }
}
