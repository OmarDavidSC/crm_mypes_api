<?php

namespace App\Constants;

class OpportunityConstant {
    public const DEFAULT_PROBABILITY = 0;

    public const RESULT_OPEN = 'OPEN';
    public const RESULT_WON = 'WON';
    public const RESULT_LOST = 'LOST';

    public static function results(): array {
        return [self::RESULT_OPEN, self::RESULT_WON, self::RESULT_LOST];
    }
}
