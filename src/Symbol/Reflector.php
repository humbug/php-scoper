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
        // https://youtrack.jetbrains.com/issue/WI-29503
        'MongoInsertBatch',
        'MongoDeleteBatch',
    ];

    private const MISSING_FUNCTIONS = [
        // https://youtrack.jetbrains.com/issue/WI-53323
        'tideways_xhprof_enable',
        'tideways_xhprof_disable',

        // https://youtrack.jetbrains.com/issue/WI-29503
        'bson_encode',
        'bson_decode',
    ];

    /**
     * Basically mirrors https://github.com/nikic/PHP-Parser/blob/9aebf377fcdf205b2156cb78c0bd6e7b2003f106/lib/PhpParser/Lexer.php#L430.
     */
    private const MISSING_CONSTANTS = [
        'STDIN',
        'STDOUT',
        'STDERR',

        // https://github.com/humbug/php-scoper/issues/618
        'true',
        'TRUE',
        'false',
        'FALSE',
        'null',
        'NULL',

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

        // https://youtrack.jetbrains.com/issue/WI-53323
        'TIDEWAYS_XHPROF_FLAGS_MEMORY',
        'TIDEWAYS_XHPROF_FLAGS_MEMORY_MU',
        'TIDEWAYS_XHPROF_FLAGS_MEMORY_PMU',
        'TIDEWAYS_XHPROF_FLAGS_CPU',
        'TIDEWAYS_XHPROF_FLAGS_NO_BUILTINS',
        'TIDEWAYS_XHPROF_FLAGS_MEMORY_ALLOC',
        'TIDEWAYS_XHPROF_FLAGS_MEMORY_ALLOC_AS_MU',

        // https://youtrack.jetbrains.com/issue/WI-29503
        'MONGODB_VERSION',
        'MONGODB_STABILITY',
    ];

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
        private SymbolRegistry $classes,
        private SymbolRegistry $functions,
        private SymbolRegistry $constants,
    ) {
    }

    public function withAdditionalSymbols(
        SymbolRegistry $classNames,
        SymbolRegistry $functionNames,
        SymbolRegistry $constantNames
    ): self {
        return new self(
            $this->classes->merge($classNames),
            $this->functions->merge($functionNames),
            $this->constants->merge($constantNames),
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
