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

namespace Humbug\PhpScoper\SpecFramework\Config;

use Humbug\PhpScoper\Configuration\ConfigurationKeys;
use PHPUnit\Framework\Assert;
use function Safe\preg_split;
use function trim;

final readonly class SpecWithConfig implements DeclaresSymbolsConfiguration
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
        ?bool $exposeGlobalConstants = null,
        ?bool $exposeGlobalClasses = null,
        ?bool $exposeGlobalFunctions = null,
        ?array $exposeNamespaces = null,
        ?array $exposeConstants = null,
        ?array $exposeClasses = null,
        ?array $exposeFunctions = null,
        ?array $excludeNamespaces = null,
        ?array $excludeConstants = null,
        ?array $excludeClasses = null,
        ?array $excludeFunctions = null,
        ?array $expectedRecordedClasses = null,
        ?array $expectedRecordedFunctions = null,
        ?array $expectedRecordedAmbiguousFunctions = null,
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
            $expectedRecordedAmbiguousFunctions,
        );
    }

    private function __construct(
        public string $inputCode,
        public ?string $expectedOutputCode,
        public ?string $prefix,
        public ?int $minPhpVersion,
        public ?int $maxPhpVersion,
        public ?bool $exposeGlobalConstants,
        public ?bool $exposeGlobalClasses,
        public ?bool $exposeGlobalFunctions,
        public ?array $exposeNamespaces,
        public ?array $exposeConstants,
        public ?array $exposeClasses,
        public ?array $exposeFunctions,
        public ?array $excludeNamespaces,
        public ?array $excludeConstants,
        public ?array $excludeClasses,
        public ?array $excludeFunctions,
        public ?array $expectedRecordedClasses,
        public ?array $expectedRecordedFunctions,
        public ?array $expectedRecordedAmbiguousFunctions,
    ) {
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

    public function getSymbolsConfig(): array
    {
        return array_filter(
            [
                ConfigurationKeys::EXPOSE_GLOBAL_CONSTANTS_KEYWORD => $this->exposeGlobalConstants,
                ConfigurationKeys::EXPOSE_GLOBAL_CLASSES_KEYWORD => $this->exposeGlobalClasses,
                ConfigurationKeys::EXPOSE_GLOBAL_FUNCTIONS_KEYWORD => $this->exposeGlobalFunctions,
                ConfigurationKeys::EXPOSE_NAMESPACES_KEYWORD => $this->exposeNamespaces,
                ConfigurationKeys::EXPOSE_CONSTANTS_SYMBOLS_KEYWORD => $this->exposeConstants,
                ConfigurationKeys::EXPOSE_FUNCTIONS_SYMBOLS_KEYWORD => $this->exposeFunctions,
                ConfigurationKeys::EXPOSE_CLASSES_SYMBOLS_KEYWORD => $this->exposeClasses,
                ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD => $this->excludeNamespaces,
                ConfigurationKeys::CONSTANTS_INTERNAL_SYMBOLS_KEYWORD => $this->excludeConstants,
                ConfigurationKeys::CLASSES_INTERNAL_SYMBOLS_KEYWORD => $this->excludeClasses,
                ConfigurationKeys::FUNCTIONS_INTERNAL_SYMBOLS_KEYWORD => $this->excludeFunctions,
            ],
            static fn (mixed $value) => null !== $value,
        );
    }
}
