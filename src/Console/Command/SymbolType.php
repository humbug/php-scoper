<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Console\Command;

// TODO: make this an enum in PHP 8.1
final class SymbolType
{
    public const CLASS_TYPE = 'class';
    public const FUNCTION_TYPE = 'function';
    public const CONSTANT_TYPE = 'constant';
    public const ANY_TYPE = 'any';

    public const ALL = [
        self::CLASS_TYPE,
        self::FUNCTION_TYPE,
        self::CONSTANT_TYPE,
        self::ANY_TYPE,
    ];

    /**
     * @return list<self::*_TYPE>
     */
    public static function getAllSpecificTypes(): array
    {
        return [
            self::CLASS_TYPE,
            self::FUNCTION_TYPE,
            self::CONSTANT_TYPE,
        ];
    }
}
