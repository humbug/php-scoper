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

    /**
     * Basically mirrors https://github.com/nikic/PHP-Parser/blob/9aebf377fcdf205b2156cb78c0bd6e7b2003f106/lib/PhpParser/Lexer.php#L430
     */
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
        // Added in PHP 8.1
        'T_ENUM',
        'T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG',
        'T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG',
        'T_READONLY',
    ];

    /**
     * @var array<string,true>
     */
    private array $classes;

    /**
     * @var array<string,true>
     */
    private array $functions;

    /**
     * @var array<string,true>
     */
    private array $constants;

    public static function createWithPhpStormStubs(): self
    {
        return new self(
            self::createSymbolList(
                array_keys(PhpStormStubsMap::CLASSES),
                self::MISSING_CLASSES,
            ),
            self::createSymbolList(
                array_keys(PhpStormStubsMap::FUNCTIONS),
                self::MISSING_FUNCTIONS,
            ),
            self::createSymbolList(
                array_keys(PhpStormStubsMap::CONSTANTS),
                self::MISSING_CONSTANTS,
            ),
        );
    }

    public static function createEmpty(): self
    {
        return new self([], [], []);
    }

    /**
     * @param array<string, true> $classes
     * @param array<string, true> $functions
     * @param array<string, true> $constants
     */
    public function __construct(array $classes, array $functions, array $constants)
    {
        $this->classes = $classes;
        $this->functions = $functions;
        $this->constants = $constants;
    }

    /**
     * @param string[] $classes
     * @param string[] $functions
     * @param string[] $constants
     */
    public function withSymbols(
        array $classes,
        array $functions,
        array $constants
    ): self
    {
        return new self(
            self::createSymbolList(
                array_keys($this->classes),
                $classes,
            ),
            self::createSymbolList(
                array_keys($this->functions),
                $functions,
            ),
            self::createSymbolList(
                array_keys($this->constants),
                $constants,
            ),
        );
    }

    public function isClassInternal(string $name): bool
    {
        return isset($this->classes[$name]);
    }

    public function isFunctionInternal(string $name): bool
    {
        return isset($this->functions[strtolower($name)]);
    }

    public function isConstantInternal(string $name): bool
    {
        return isset($this->constants[$name]);
    }

    /**
     * @param string[][] $sources
     *
     * @return array<string, true>
     */
    private static function createSymbolList(array ...$sources): array
    {
        return array_fill_keys(
            array_merge(...$sources),
            true
        );
    }
}
