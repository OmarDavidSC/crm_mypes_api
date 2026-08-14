<?php

namespace App\Constants;

class ActivityConstant {
    public const TYPE_CALL = 'CALL';
    public const TYPE_WHATSAPP = 'WHATSAPP';
    public const TYPE_EMAIL = 'EMAIL';
    public const TYPE_MEETING = 'MEETING';
    public const TYPE_NOTE = 'NOTE';
    public const TYPE_FOLLOW_UP = 'FOLLOW_UP';

    public const TYPE_LEAD_CREATED = 'LEAD_CREATED';
    public const TYPE_LEAD_CONVERTED = 'LEAD_CONVERTED';
    public const TYPE_OPPORTUNITY_CREATED = 'OPPORTUNITY_CREATED';
    public const TYPE_STAGE_CHANGE = 'STAGE_CHANGE';

    public const TYPE_QUOTATION_CREATED = 'QUOTATION_CREATED';
    public const TYPE_QUOTATION_SENT = 'QUOTATION_SENT';
    public const TYPE_QUOTATION_ACCEPTED = 'QUOTATION_ACCEPTED';
    public const TYPE_QUOTATION_REJECTED = 'QUOTATION_REJECTED';

    public const TYPE_SYSTEM = 'SYSTEM';

    public static function manualTypes(): array {
        return [self::TYPE_CALL, self::TYPE_WHATSAPP, self::TYPE_EMAIL, self::TYPE_MEETING, self::TYPE_NOTE, self::TYPE_FOLLOW_UP];
    }

    public static function automaticTypes(): array {
        return [
            self::TYPE_LEAD_CREATED,
            self::TYPE_LEAD_CONVERTED,
            self::TYPE_OPPORTUNITY_CREATED,
            self::TYPE_STAGE_CHANGE,
            self::TYPE_QUOTATION_CREATED,
            self::TYPE_QUOTATION_SENT,
            self::TYPE_QUOTATION_ACCEPTED,
            self::TYPE_QUOTATION_REJECTED,
            self::TYPE_SYSTEM,
        ];
    }
    
    public static function all(): array {
        return array_merge(self::manualTypes(), self::automaticTypes());
    }
}
