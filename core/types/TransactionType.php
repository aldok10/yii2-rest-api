<?php

namespace app\core\types;

class TransactionType extends BaseType
{
    /** @var int Topup status */
    public const TOPUP = 0;

    /** @var int Transfer status */
    public const TRANSFER = 1;

    public static function names(): array
    {
        return [
            self::TRANSFER => 'Transfer',
            self::TOPUP => 'Topup',
        ];
    }
}
