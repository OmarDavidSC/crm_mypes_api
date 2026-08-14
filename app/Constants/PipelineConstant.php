<?php

namespace App\Constants;

class PipelineConstant {
    public const STAGE_NEW = 'NEW';
    public const STAGE_CONTACTED = 'CONTACTED';
    public const STAGE_INTERESTED = 'INTERESTED';
    public const STAGE_QUOTATION = 'QUOTATION';
    public const STAGE_NEGOTIATION = 'NEGOTIATION';
    public const STAGE_WON = 'WON';
    public const STAGE_LOST = 'LOST';

    public static function defaultStages(): array
    {
        return [
            ['name' => 'Nuevo', 'stage_key' => self::STAGE_NEW, 'position' => 1, 'probability' => 10, 'is_won' => 0, 'is_lost' => 0,],
            ['name' => 'Contactado', 'stage_key' => self::STAGE_CONTACTED, 'position' => 2, 'probability' => 20, 'is_won' => 0, 'is_lost' => 0,],
            ['name' => 'Interesado', 'stage_key' => self::STAGE_INTERESTED, 'position' => 3, 'probability' => 40, 'is_won' => 0, 'is_lost' => 0,],
            ['name' => 'Cotización', 'stage_key' => self::STAGE_QUOTATION, 'position' => 4, 'probability' => 60, 'is_won' => 0, 'is_lost' => 0,],
            ['name' => 'Negociación', 'stage_key' => self::STAGE_NEGOTIATION, 'position' => 5, 'probability' => 80, 'is_won' => 0, 'is_lost' => 0,],
            ['name' => 'Ganado', 'stage_key' => self::STAGE_WON, 'position' => 6, 'probability' => 100, 'is_won' => 1, 'is_lost' => 0,],
            ['name' => 'Perdido', 'stage_key' => self::STAGE_LOST, 'position' => 7, 'probability' => 0, 'is_won' => 0, 'is_lost' => 1,],
        ];
    }
}
