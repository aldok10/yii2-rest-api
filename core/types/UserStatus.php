<?php

namespace app\core\types;

class UserStatus extends BaseType
{
    /** @var int Active status */
    public const ACTIVE = 1;

    /** @var int Inactive status */
    public const UNACTIVATED = 0;

    public static function names(): array
    {
        return [
            self::ACTIVE => 'active',
            self::UNACTIVATED => 'unactivated',
        ];
    }
}
