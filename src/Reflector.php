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

namespace Humbug\PhpScoper;

use JetBrains\PHPStormStub\PhpStormStubsMap;
use function array_diff;
use function array_fill_keys;
use function array_filter;
use function array_keys;
use function array_merge;
use function strtolower;
use const PHP_VERSION_ID;

/**
 * @private
 */
final class Reflector
{
    private const MISSING_CLASSES = [
        // https://github.com/JetBrains/phpstorm-stubs/pull/594
        'parallel\Channel' => 0,
        'parallel\Channel\Error' => 0,
        'parallel\Channel\Error\Closed' => 0,
        'parallel\Channel\Error\Existence' => 0,
        'parallel\Channel\Error\IllegalValue' => 0,
        'parallel\Error' => 0,
        'parallel\Events' => 0,
        'parallel\Events\Error' => 0,
        'parallel\Events\Error\Existence' => 0,
        'parallel\Events\Error\Timeout' => 0,
        'parallel\Events\Event' => 0,
        'parallel\Events\Event\Type' => 0,
        'parallel\Events\Input' => 0,
        'parallel\Events\Input\Error' => 0,
        'parallel\Events\Input\Error\Existence' => 0,
        'parallel\Events\Input\Error\IllegalValue' => 0,
        'parallel\Future' => 0,
        'parallel\Future\Error' => 0,
        'parallel\Future\Error\Cancelled' => 0,
        'parallel\Future\Error\Foreign' => 0,
        'parallel\Future\Error\Killed' => 0,
        'parallel\Runtime' => 0,
        'parallel\Runtime\Bootstrap' => 0,
        'parallel\Runtime\Error' => 0,
        'parallel\Runtime\Error\Bootstrap' => 0,
        'parallel\Runtime\Error\Closed' => 0,
        'parallel\Runtime\Error\IllegalFunction' => 0,
        'parallel\Runtime\Error\IllegalInstruction' => 0,
        'parallel\Runtime\Error\IllegalParameter' => 0,
        'parallel\Runtime\Error\IllegalReturn' => 0,
    ];

    private const MISSING_FUNCTIONS = [];

    private const MISSING_CONSTANTS = [
        'STDIN' => 0,
        'STDOUT' => 0,
        'STDERR' => 0,
        // Added in PHP 7.4
        'T_BAD_CHARACTER' => 70400,
        'T_FN' => 70400,
        'T_COALESCE_EQUAL' => 70400,
        // Added in PHP 8.0
        'T_NAME_QUALIFIED' => 80000,
        'T_NAME_FULLY_QUALIFIED' => 80000,
        'T_NAME_RELATIVE' => 80000,
        'T_MATCH' => 80000,
        'T_NULLSAFE_OBJECT_OPERATOR' => 80000,
        'T_ATTRIBUTE' => 80000,
    ];

    private static $CLASSES;

    private static $FUNCTIONS;

    private static $CONSTANTS;

    /**
     * @param array<string,string>|null $symbols
     * @param array<string,string>      $source
     * @param array<string, int>        $missingSymbols
     */
    private static function initSymbolList(?array &$symbols, array $source, array $missingSymbols): void
    {
        if (null !== $symbols) {
            return;
        }

        $excludingSymbols = array_keys(
            array_filter(
                $missingSymbols,
                static function ($version) {
                    return PHP_VERSION_ID < $version;
                }
            )
        );

        $symbols = array_fill_keys(
            array_diff(
                array_merge(
                    array_keys($source),
                    array_keys($missingSymbols)
                ),
                $excludingSymbols
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
