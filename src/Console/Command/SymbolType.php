<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Humbug\PhpScoper\Console\Command;

use function array_column;

enum SymbolType: string
{
    case CLASS_TYPE = 'class';
    case FUNCTION_TYPE = 'function';
    case CONSTANT_TYPE = 'constant';
    case ANY_TYPE = 'any';

    public const ALL = [
        self::CLASS_TYPE,
        self::FUNCTION_TYPE,
        self::CONSTANT_TYPE,
        self::ANY_TYPE,
    ];

    /**
     * @return list<self>
     */
    public static function getAllSpecificTypes(): array
    {
        return [
            self::CLASS_TYPE,
            self::FUNCTION_TYPE,
            self::CONSTANT_TYPE,
        ];
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(
            self::cases(),
            'value',
        );
    }
}
