<?php

declare(strict_types=1);

namespace Humbug\PhpScoper;

use InvalidArgumentException;
use function array_key_exists;
use function array_keys;
use function array_values;
use function gettype;
use function is_array;
use function is_bool;
use function is_string;
use function Safe\sprintf;

final class ConfigurationWhitelistFactory
{
    private RegexChecker $regexChecker;

    public function __construct(RegexChecker $regexChecker)
    {
        $this->regexChecker = $regexChecker;
    }

    public function createWhitelist(array $config): Whitelist
    {
        [
            $excludedNamespaceRegexes,
            $excludedNamespaceNames,
        ] = $this->retrieveExcludedNamespaces($config);

        $whitelist = self::retrieveWhitelistValues($config);

        $whitelistGlobalConstants = self::retrieveGlobalWhitelist(
            $config,
            ConfigurationKeys::WHITELIST_GLOBAL_CONSTANTS_KEYWORD,
        );
        $whitelistGlobalClasses = self::retrieveGlobalWhitelist(
            $config,
            ConfigurationKeys::WHITELIST_GLOBAL_CLASSES_KEYWORD,
        );
        $whitelistGlobalFunctions = self::retrieveGlobalWhitelist(
            $config,
            ConfigurationKeys::WHITELIST_GLOBAL_FUNCTIONS_KEYWORD,
        );

        return Whitelist::create(
            $whitelistGlobalConstants,
            $whitelistGlobalClasses,
            $whitelistGlobalFunctions,
            $excludedNamespaceRegexes,
            $excludedNamespaceNames,
            ...$whitelist,
        );
    }

    /**
     * @return array{string[], string[]}
     */
    private function retrieveExcludedNamespaces(array $config): array
    {
        $key = ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD;

        if (!array_key_exists($key, $config)) {
            return [];
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

            if ($this->regexChecker->isRegexLike($regexOrNamespaceName)) {
                $namespaceNames[$regexOrNamespaceName] = null;

                continue;
            }

            $errorMessage = $this->regexChecker->validateRegex($regexOrNamespaceName);

            if (null !== $errorMessage) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Expected "%s" to be an array of valid regexes. The element "%s" with the index "%s" is not: %s.',
                        $key,
                        $regexOrNamespaceName,
                        $index,
                        $errorMessage,
                    ),
                );
            }

            $regexes[$regexOrNamespaceName] = null;
        }

        return [
            array_keys($regexes),
            array_keys($namespaceNames),
        ];
    }

    /**
     * return list<string>
     */
    private static function retrieveWhitelistValues(array $config): array
    {
        $key = ConfigurationKeys::WHITELIST_KEYWORD;

        if (!array_key_exists($key, $config)) {
            return [];
        }

        $whitelist = $config[$key];

        if (!is_array($whitelist)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected whitelist to be an array of strings, found "%s" instead.',
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

    private static function retrieveGlobalWhitelist(array $config, string $key): bool
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
}
