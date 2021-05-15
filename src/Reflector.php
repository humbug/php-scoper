<?php

/** @noinspection ClassConstantCanBeUsedInspection */

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

namespace Humbug\PhpScoper;

use JetBrains\PHPStormStub\PhpStormStubsMap;
use function array_fill_keys;
use function array_keys;
use function array_merge;
use function strtolower;

/**
 * @private
 */
final class Reflector
{
    private const MISSING_CLASSES = [
        // https://github.com/JetBrains/phpstorm-stubs/pull/594
        'parallel\Channel',
        'parallel\Channel\Error',
        'parallel\Channel\Error\Closed',
        'parallel\Channel\Error\Existence',
        'parallel\Channel\Error\IllegalValue',
        'parallel\Error',
        'parallel\Events',
        'parallel\Events\Error',
        'parallel\Events\Error\Existence',
        'parallel\Events\Error\Timeout',
        'parallel\Events\Event',
        'parallel\Events\Event\Type',
        'parallel\Events\Input',
        'parallel\Events\Input\Error',
        'parallel\Events\Input\Error\Existence',
        'parallel\Events\Input\Error\IllegalValue',
        'parallel\Future',
        'parallel\Future\Error',
        'parallel\Future\Error\Cancelled',
        'parallel\Future\Error\Foreign',
        'parallel\Future\Error\Killed',
        'parallel\Runtime',
        'parallel\Runtime\Bootstrap',
        'parallel\Runtime\Error',
        'parallel\Runtime\Error\Bootstrap',
        'parallel\Runtime\Error\Closed',
        'parallel\Runtime\Error\IllegalFunction',
        'parallel\Runtime\Error\IllegalInstruction',
        'parallel\Runtime\Error\IllegalParameter',
        'parallel\Runtime\Error\IllegalReturn',
    ];

    private const MISSING_FUNCTIONS = [];

    private const MISSING_CONSTANTS = [
        'STDIN',
        'STDOUT',
        'STDERR',
        // Added in PHP 7.4
        'T_BAD_CHARACTER',
        'T_FN',
        'T_COALESCE_EQUAL',
        // Added in PHP 8.0
        'T_NAME_QUALIFIED',
        'T_NAME_FULLY_QUALIFIED',
        'T_NAME_RELATIVE',
        'T_MATCH',
        'T_NULLSAFE_OBJECT_OPERATOR',
        'T_ATTRIBUTE',
    ];

    /**
     * @var array<string,string>|null
     */
    private static ?array $CLASSES = null;

    /**
     * @var array<string,string>|null
     */
    private static ?array $FUNCTIONS = null;

    /**
     * @var array<string,string>|null
     */
    private static ?array $CONSTANTS = null;

    /**
     * @param array<string,string>|null $symbols
     * @param array<string,string>      $source
     * @param string[]                  $missingSymbols
     */
    private static function initSymbolList(?array &$symbols, array $source, array $missingSymbols): void
    {
        if (null !== $symbols) {
            return;
        }

        $symbols = array_fill_keys(
            array_merge(
                array_keys($source),
                $missingSymbols
            ),
            true
        );
    }

    public function __construct()
    {
        self::initSymbolList(self::$CLASSES, PhpStormStubsMap::CLASSES, self::MISSING_CLASSES);
        self::initSymbolList(self::$FUNCTIONS, PhpStormStubsMap::FUNCTIONS, self::MISSING_FUNCTIONS);
        self::initSymbolList(self::$CONSTANTS, PhpStormStubsMap::CONSTANTS, self::MISSING_CONSTANTS);
    }

    public function isClassInternal(string $name): bool
    {
        return isset(self::$CLASSES[$name]);
    }

    public function isFunctionInternal(string $name): bool
    {
        return isset(self::$FUNCTIONS[strtolower($name)]);
    }

    public function isConstantInternal(string $name): bool
    {
        return isset(self::$CONSTANTS[$name]);
    }
}
