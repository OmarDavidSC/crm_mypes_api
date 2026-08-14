<?php

namespace App\Constants;

class CustomerConstant {
    public const TYPE_PERSON = 'PERSON';
    public const TYPE_COMPANY = 'COMPANY';

    public const DOCUMENT_DNI = 'DNI';
    public const DOCUMENT_RUC = 'RUC';
    public const DOCUMENT_CE = 'CE';
    public const DOCUMENT_PASSPORT = 'PASSPORT';

    public static function types(): array  {
        return [self::TYPE_PERSON, self::TYPE_COMPANY];
    }

    public static function documentTypes(): array {
        return [self::DOCUMENT_DNI, self::DOCUMENT_RUC, self::DOCUMENT_CE, self::DOCUMENT_PASSPORT,];
    }
}
