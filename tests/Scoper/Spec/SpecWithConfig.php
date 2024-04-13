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

namespace Humbug\PhpScoper\Scoper\Spec;

use PHPUnit\Framework\Assert;
use function Safe\preg_split;
use function trim;

final class SpecWithConfig extends SpecConfig
{
    public const SPEC_DELIMITER = "/\n----(?:\n|$)/";

    public static function fromSimpleSpec(string $spec): self
    {
        return self::create($spec);
    }

    public static function create(
        string $spec,
        ?string $prefix = null,
        ?int $minPhpVersion = null,
        ?int $maxPhpVersion = null,
        bool $exposeGlobalConstants = false,
        bool $exposeGlobalClasses = false,
        bool $exposeGlobalFunctions = false,
        array $exposeNamespaces = [],
        array $exposeConstants = [],
        array $exposeClasses = [],
        array $exposeFunctions = [],
        array $excludeNamespaces = [],
        array $excludeConstants = [],
        array $excludeClasses = [],
        array $excludeFunctions = [],
        ?array $expectedRecordedClasses = null,
        ?array $expectedRecordedFunctions = null,
    ): self {
        [$inputCode, $expectedOutputCode] = self::parseSpec($spec);

        return new self(
            $inputCode,
            $expectedOutputCode,
            $prefix,
            $minPhpVersion,
            $maxPhpVersion,
            $exposeGlobalConstants,
            $exposeGlobalClasses,
            $exposeGlobalFunctions,
            $exposeNamespaces,
            $exposeConstants,
            $exposeClasses,
            $exposeFunctions,
            $excludeNamespaces,
            $excludeConstants,
            $excludeClasses,
            $excludeFunctions,
            $expectedRecordedClasses,
            $expectedRecordedFunctions,
        );
    }

    private function __construct(
        public readonly string $inputCode,
        public readonly ?string $expectedOutputCode,
        public readonly ?string $prefix,
        ?int $minPhpVersion,
        ?int $maxPhpVersion,
        bool $exposeGlobalConstants,
        bool $exposeGlobalClasses,
        bool $exposeGlobalFunctions,
        array $exposeNamespaces,
        array $exposeConstants,
        array $exposeClasses,
        array $exposeFunctions,
        array $excludeNamespaces,
        array $excludeConstants,
        array $excludeClasses,
        array $excludeFunctions,
        public readonly ?array $expectedRecordedClasses,
        public readonly ?array $expectedRecordedFunctions,
    ) {
        parent::__construct(
            $minPhpVersion,
            $maxPhpVersion,
            $exposeGlobalConstants,
            $exposeGlobalClasses,
            $exposeGlobalFunctions,
            $exposeNamespaces,
            $exposeConstants,
            $exposeClasses,
            $exposeFunctions,
            $excludeNamespaces,
            $excludeConstants,
            $excludeClasses,
            $excludeFunctions,
        );
    }

    /**
     * @return array{string, string|null}
     */
    private static function parseSpec(string $spec): array
    {
        $parts = preg_split(self::SPEC_DELIMITER, $spec);

        Assert::assertCount(
            2,
            $parts,
            'Expected a specification to contain only two parts. Ensure there is one and only one `----` delimiter surrounded by blank lines.',
        );

        [$inputCode, $expectedOutputCode] = $parts;

        if ('' === trim($expectedOutputCode)) {
            $expectedOutputCode = null;
        }

        return [$inputCode, $expectedOutputCode];
    }
}
