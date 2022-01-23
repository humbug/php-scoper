<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Configuration;

use Humbug\PhpScoper\RegexChecker;
use Humbug\PhpScoper\Symbol\NamespaceRegistry;
use InvalidArgumentException;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_pop;
use function array_values;
use function explode;
use function gettype;
use function implode;
use function is_array;
use function is_bool;
use function is_string;
use function Safe\preg_match as native_preg_match;
use function Safe\sprintf;
use function Safe\substr;
use function str_replace;

final class ConfigurationWhitelistFactory
{
    private RegexChecker $regexChecker;

    public function __construct(RegexChecker $regexChecker)
    {
        $this->regexChecker = $regexChecker;
    }

    public function createSymbolsConfiguration(array $config): SymbolsConfiguration
    {
        [
            $excludedNamespaceRegexes,
            $excludedNamespaceNames,
        ] = $this->retrieveExcludedNamespaces($config);

        $legacyExposedElements = self::retrieveLegacyExposedElements($config);

        [
            $legacyExposedSymbols,
            $legacyExposedSymbolsPatterns,
            $legacyExposedConstants,
            $excludedNamespaceNames,
        ] = self::parseLegacyExposedElements($legacyExposedElements, $excludedNamespaceNames);

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

        return SymbolsConfiguration::create(
            $exposeGlobalConstants,
            $exposeGlobalClasses,
            $exposeGlobalFunctions,
            NamespaceRegistry::create(
                $excludedNamespaceRegexes,
                $excludedNamespaceNames,
            ),
            null,
            $legacyExposedSymbols,
            $legacyExposedSymbolsPatterns,
            $legacyExposedSymbols,
            $legacyExposedSymbolsPatterns,
            $legacyExposedConstants,
            $legacyExposedSymbolsPatterns,
        );
    }

    /**
     * @return array{string[], string[]}
     */
    private function retrieveExcludedNamespaces(array $config): array
    {
        $key = ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD;

        if (!array_key_exists($key, $config)) {
            return [[], []];
        }

        $regexesAndNamespaceNames = $config[$key];

        if (!is_array($regexesAndNamespaceNames)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected "%s" to be an array of strings, got "%s" instead.',
                    $key,
                    gettype($regexesAndNamespaceNames),
                ),
            );
        }

        // Store the strings in the keys for avoiding a unique check later on
        $regexes = [];
        $namespaceNames = [];

        foreach ($regexesAndNamespaceNames as $index => $regexOrNamespaceName) {
            if (!is_string($regexOrNamespaceName)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Expected "%s" to be an array of strings, got "%s" for the element with the index "%s".',
                        $key,
                        gettype($regexOrNamespaceName),
                        $index,
                    ),
                );
            }

            if (!$this->regexChecker->isRegexLike($regexOrNamespaceName)) {
                $namespaceNames[$regexOrNamespaceName] = null;

                continue;
            }

            $excludeNamespaceRegex = $regexOrNamespaceName;

            $errorMessage = $this->regexChecker->validateRegex($excludeNamespaceRegex);

            if (null !== $errorMessage) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Expected "%s" to be an array of valid regexes. The element "%s" with the index "%s" is not: %s.',
                        $key,
                        $excludeNamespaceRegex,
                        $index,
                        $errorMessage,
                    ),
                );
            }

            // Ensure namespace comparisons are always case-insensitive
            // TODO: double check that we are not adding it twice or that adding it twice does not break anything
            $excludeNamespaceRegex .= 'i';
            $regexes[$excludeNamespaceRegex] = null;
        }

        return [
            array_keys($regexes),
            array_keys($namespaceNames),
        ];
    }

    /**
     * return list<string>
     */
    private static function retrieveLegacyExposedElements(array $config): array
    {
        $key = ConfigurationKeys::WHITELIST_KEYWORD;

        if (!array_key_exists($key, $config)) {
            return [];
        }

        $whitelist = $config[$key];

        if (!is_array($whitelist)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected "%s" to be an array of strings, found "%s" instead.',
                    $key,
                    gettype($whitelist),
                ),
            );
        }

        foreach ($whitelist as $index => $className) {
            if (is_string($className)) {
                continue;
            }

            throw new InvalidArgumentException(
                sprintf(
                    'Expected whitelist to be an array of string, the "%d" element is not.',
                    $index,
                ),
            );
        }

        return array_values($whitelist);
    }

    /**
     * @param list<string> $elements
     * @param list<string> $excludedNamespaceNames
     */
    private static function parseLegacyExposedElements(array $elements, array $excludedNamespaceNames): array
    {
        $exposedSymbols = [];
        $exposedConstants = [];
        $exposedSymbolsPatterns = [];
        $excludedNamespaceNames = array_map('strtolower', $excludedNamespaceNames);

        foreach ($elements as $element) {
            $element = ltrim(trim($element), '\\');

            self::assertValidElement($element);

            if ('\*' === substr($element, -2)) {
                $excludedNamespaceNames[] = strtolower(substr($element, 0, -2));
            } elseif ('*' === $element) {
                $excludedNamespaceNames[] = '';
            } elseif (false !== strpos($element, '*')) {
                $exposedSymbolsPatterns[] = self::createExposePattern($element);
            } else {
                $exposedSymbols[] = strtolower($element);
                $exposedConstants[] = self::lowerCaseConstantName($element);
            }
        }

        return [
            $exposedSymbols,
            $exposedSymbolsPatterns,
            $exposedConstants,
            $excludedNamespaceNames,
        ];
    }

    private static function assertValidElement(string $element): void
    {
        if ('' !== $element) {
            return;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Invalid whitelist element "%s": cannot accept an empty string',
                $element,
            ),
        );
    }

    private static function createExposePattern(string $element): string
    {
        self::assertValidPattern($element);

        return sprintf(
            '/^%s$/u',
            str_replace(
                '\\',
                '\\\\',
                str_replace(
                    '*',
                    '.*',
                    $element,
                ),
            ),
        );
    }

    private static function assertValidPattern(string $element): void
    {
        if (1 !== native_preg_match('/^(([\p{L}_]+\\\\)+)?[\p{L}_]*\*$/u', $element)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid whitelist pattern "%s".',
                    $element,
                ),
            );
        }
    }

    /**
     * Transforms the constant FQ name "Acme\Foo\X" to "acme\foo\X" since the namespace remains case insensitive for
     * constants regardless of whether or not constants actually are case insensitive.
     */
    private static function lowerCaseConstantName(string $name): string
    {
        $parts = explode('\\', $name);

        $lastPart = array_pop($parts);

        $parts = array_map('strtolower', $parts);

        $parts[] = $lastPart;

        return implode('\\', $parts);
    }

    private static function retrieveExposeGlobalSymbol(array $config, string $key): bool
    {
        if (!array_key_exists($key, $config)) {
            return false;
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
}
