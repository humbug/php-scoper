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

namespace Humbug\PhpScoper\Configuration;

use Humbug\PhpScoper\Symbol\NamespaceRegistry;
use Humbug\PhpScoper\Symbol\SymbolRegistry;
use InvalidArgumentException;
use function array_key_exists;
use function array_keys;
use function get_debug_type;
use function gettype;
use function is_array;
use function is_bool;
use function is_string;
use function sprintf;
use function str_contains;
use function strrpos;
use function substr;

final readonly class SymbolsConfigurationFactory
{
    public function __construct(private RegexChecker $regexChecker)
    {
    }

    /**
     * @param array<array-key, mixed> $config
     */
    public function createSymbolsConfiguration(array $config): SymbolsConfiguration
    {
        [
            $excludedNamespaceNames,
            $excludedNamespaceRegexes,
        ] = $this->retrieveElements(
            $config,
            ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD,
        );

        [
            $exposedNamespaceNames,
            $exposedNamespaceRegexes,
        ] = $this->retrieveElements(
            $config,
            ConfigurationKeys::EXPOSE_NAMESPACES_KEYWORD,
        );

        $exposeGlobalConstants = self::retrieveExposeGlobalSymbol(
            $config,
            ConfigurationKeys::EXPOSE_GLOBAL_CONSTANTS_KEYWORD,
        );
        $exposeGlobalClasses = self::retrieveExposeGlobalSymbol(
            $config,
            ConfigurationKeys::EXPOSE_GLOBAL_CLASSES_KEYWORD,
        );
        $exposeGlobalFunctions = self::retrieveExposeGlobalSymbol(
            $config,
            ConfigurationKeys::EXPOSE_GLOBAL_FUNCTIONS_KEYWORD,
        );

        [$exposedClassNames, $exposedClassRegexes] = $this->retrieveElements(
            $config,
            ConfigurationKeys::EXPOSE_CLASSES_SYMBOLS_KEYWORD,
        );

        [$exposedFunctionNames, $exposedFunctionRegexes] = $this->retrieveElements(
            $config,
            ConfigurationKeys::EXPOSE_FUNCTIONS_SYMBOLS_KEYWORD,
        );

        [$exposedConstantNames, $exposedConstantRegexes] = $this->retrieveElements(
            $config,
            ConfigurationKeys::EXPOSE_CONSTANTS_SYMBOLS_KEYWORD,
        );

        $excludedClasses = SymbolRegistry::create(
            ...$this->retrieveElements(
                $config,
                ConfigurationKeys::CLASSES_INTERNAL_SYMBOLS_KEYWORD,
            ),
        );

        $excludedFunctions = SymbolRegistry::create(
            ...$this->retrieveElements(
                $config,
                ConfigurationKeys::FUNCTIONS_INTERNAL_SYMBOLS_KEYWORD,
            ),
        );

        $excludedConstants = SymbolRegistry::createForConstants(
            ...$this->retrieveElements(
                $config,
                ConfigurationKeys::CONSTANTS_INTERNAL_SYMBOLS_KEYWORD,
            ),
        );

        return SymbolsConfiguration::create(
            $exposeGlobalConstants,
            $exposeGlobalClasses,
            $exposeGlobalFunctions,
            NamespaceRegistry::create(
                $excludedNamespaceNames,
                $excludedNamespaceRegexes,
            ),
            NamespaceRegistry::create(
                $exposedNamespaceNames,
                $exposedNamespaceRegexes,
            ),
            SymbolRegistry::create(
                $exposedClassNames,
                $exposedClassRegexes,
            ),
            SymbolRegistry::create(
                $exposedFunctionNames,
                $exposedFunctionRegexes,
            ),
            SymbolRegistry::createForConstants(
                $exposedConstantNames,
                $exposedConstantRegexes,
            ),
            $excludedClasses,
            $excludedFunctions,
            $excludedConstants,
        );
    }

    /**
     * @param array<array-key, mixed> $config
     */
    private static function retrieveExposeGlobalSymbol(array $config, string $key): bool
    {
        if (!array_key_exists($key, $config)) {
            return true;
        }

        $value = $config[$key];

        if (!is_bool($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected %s to be a boolean, found "%s" instead.',
                    $key,
                    gettype($value),
                ),
            );
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>           $config
     * @return array{list<string>, list<string>}
     */
    private function retrieveElements(array $config, string $key): array
    {
        if (!array_key_exists($key, $config)) {
            return [[], []];
        }

        $symbolNamesAndRegexes = $config[$key];

        self::assertIsArrayOfStrings($symbolNamesAndRegexes, $key);

        // Store the strings in the keys for avoiding a unique check later on
        $names = [];
        $regexes = [];

        foreach ($symbolNamesAndRegexes as $index => $nameOrRegex) {
            if (!$this->regexChecker->isRegexLike($nameOrRegex)) {
                $names[$nameOrRegex] = null;

                continue;
            }

            $regex = $this->getRegex($nameOrRegex, $key, $index);

            $regexes[$regex] = null;
        }

        return [
            array_keys($names),
            array_keys($regexes),
        ];
    }

    private function getRegex(string $regex, string $key, int|string $index): string
    {
        $this->assertValidRegex($regex, $key, (string) $index);

        $errorMessage = $this->regexChecker->validateRegex($regex);

        if (null !== $errorMessage) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected "%s" to be an array of valid regexes. The element "%s" with the index "%s" is not: %s.',
                    $key,
                    $regex,
                    $index,
                    $errorMessage,
                ),
            );
        }

        $flags = self::getRegexFlags($regex);

        if (!str_contains($flags, 'i')) {
            // Ensure namespace comparisons are always case-insensitive
            $regex .= 'i';
        }

        return $regex;
    }

    /**
     * @param non-empty-string $regex
     */
    private static function getRegexFlags(string $regex): string
    {
        $separator = $regex[0];
        $lastSeparatorPosition = strrpos($regex, $separator);

        if (false === $lastSeparatorPosition) {
            return '';
        }

        return substr($regex, $lastSeparatorPosition);
    }

    /**
     * @phpstan-assert string[] $value
     */
    private static function assertIsArrayOfStrings(mixed $value, string $key): void
    {
        if (!is_array($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected "%s" to be an array of strings, found "%s" instead.',
                    $key,
                    get_debug_type($value),
                ),
            );
        }

        foreach ($value as $index => $element) {
            if (is_string($element)) {
                continue;
            }

            throw new InvalidArgumentException(
                sprintf(
                    'Expected "%s" to be an array of strings, found "%s" for the element with the index "%s".',
                    $key,
                    get_debug_type($element),
                    $index,
                ),
            );
        }
    }

    /**
     * @phpstan-assert non-empty-string $regex
     */
    private function assertValidRegex(string $regex, string $key, string $index): void
    {
        $errorMessage = $this->regexChecker->validateRegex($regex);

        if (null !== $errorMessage) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected "%s" to be an array of valid regexes. The element "%s" with the index "%s" is not: %s.',
                    $key,
                    $regex,
                    $index,
                    $errorMessage,
                ),
            );
        }
    }
}
