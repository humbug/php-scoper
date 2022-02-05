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

namespace Humbug\PhpScoper\Symbol;

use JetBrains\PHPStormStub\PhpStormStubsMap;
use function array_keys;
use function array_merge;

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

    private SymbolRegistry $classes;
    private SymbolRegistry $functions;
    private SymbolRegistry $constants;

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
            self::createConstantSymbolList(
                array_keys(PhpStormStubsMap::CONSTANTS),
                self::MISSING_CONSTANTS,
            ),
        );
    }

    public static function createEmpty(): self
    {
        return new self(
            SymbolRegistry::create(),
            SymbolRegistry::create(),
            SymbolRegistry::createForConstants(),
        );
    }

    private function __construct(
        SymbolRegistry $classes,
        SymbolRegistry $functions,
        SymbolRegistry $constants
    ) {
        $this->classes = $classes;
        $this->functions = $functions;
        $this->constants = $constants;
    }

    /**
     * @param string[] $classNames
     * @param string[] $functionNames
     * @param string[] $constantNames
     */
    public function withSymbols(
        array $classNames,
        array $functionNames,
        array $constantNames
    ): self
    {
        return new self(
            $this->classes->withAdditionalSymbols($classNames),
            $this->functions->withAdditionalSymbols($functionNames),
            $this->constants->withAdditionalSymbols($constantNames),
        );
    }

    public function isClassInternal(string $name): bool
    {
        return $this->classes->matches($name);
    }

    public function isFunctionInternal(string $name): bool
    {
        return $this->functions->matches($name);
    }

    public function isConstantInternal(string $name): bool
    {
        return $this->constants->matches($name);
    }

    /**
     * @param string[] $sources
     */
    private static function createSymbolList(array ...$sources): SymbolRegistry
    {
        return SymbolRegistry::create(
            array_merge(...$sources),
        );
    }

    /**
     * @param string[] $sources
     */
    private static function createConstantSymbolList(array ...$sources): SymbolRegistry
    {
        return SymbolRegistry::createForConstants(
            array_merge(...$sources),
        );
    }
}
