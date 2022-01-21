<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Symbol;

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Reflector;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_pop;
use function array_unique;
use function explode;
use function implode;
use function preg_match as native_preg_match;
use function Safe\array_flip;
use function strpos;
use function strtolower;

/**
 * Combines the API or the "traditional" reflector which is about to tell
 * if a symbol is internal or not with the more PHP-Scoper specific exposed
 * API.
 */
final class EnrichedReflector
{
    private Reflector $reflector;
    private SymbolsConfiguration $symbolsConfiguration;

    public function __construct(
        Reflector $reflector,
        SymbolsConfiguration $symbolsConfiguration
    ) {
        $this->reflector = $reflector;
        $this->symbolsConfiguration = $symbolsConfiguration;
    }

    public function belongsToExcludedNamespace(string $name): bool
    {
        return $this->symbolsConfiguration
            ->getExcludedNamespaces()
            ->belongsToRegisteredNamespace($name);
    }

    public function isFunctionInternal(string $name): bool
    {
        return $this->reflector->isFunctionInternal($name);
    }

    public function isFunctionExcluded(string $name): bool
    {
        return $this->reflector->isFunctionInternal($name)
            || $this->belongsToExcludedNamespace($name);
    }

    public function isClassInternal(string $name): bool
    {
        return $this->reflector->isClassInternal($name);
    }

    public function isClassExcluded(string $name): bool
    {
        return $this->reflector->isClassInternal($name)
            || $this->belongsToExcludedNamespace($name);
    }

    public function isConstantInternal(string $name): bool
    {
        return $this->reflector->isConstantInternal($name);
    }

    public function isConstantExcluded(string $name): bool
    {
        // TODO: double check not sure that internal should mean excluded for constants
        return $this->reflector->isConstantInternal($name)
            || $this->belongsToExcludedNamespace($name);
    }

    public function isExposedFunction(string $resolvedName): bool
    {
        return !$this->belongsToExcludedNamespace($resolvedName)
            && !$this->reflector->isFunctionInternal($resolvedName)
            && (
                $this->_isExposedFunctionFromGlobalNamespace($resolvedName)
                || $this->isSymbolExposed($resolvedName)
            );
    }

    public function isExposedFunctionFromGlobalNamespace(string $resolvedName): bool
    {
        return !$this->belongsToExcludedNamespace($resolvedName)
            && !$this->reflector->isFunctionInternal($resolvedName)
            && $this->_isExposedFunctionFromGlobalNamespace($resolvedName);
    }

    public function isExposedClass(string $resolvedName): bool
    {
        return !$this->belongsToExcludedNamespace($resolvedName)
            && !$this->reflector->isClassInternal($resolvedName)
            && (
                $this->_isExposedClassFromGlobalNamespace($resolvedName)
                || $this->isSymbolExposed($resolvedName)
            );
    }

    public function isExposedClassFromGlobalNamespace(string $resolvedName): bool
    {
        return !$this->belongsToExcludedNamespace($resolvedName)
            && !$this->reflector->isClassInternal($resolvedName)
            && $this->_isExposedClassFromGlobalNamespace($resolvedName);
    }

    public function isExposedConstant(string $name): bool
    {
        // Special case: internal constants must be treated as exposed symbols.
        //
        // Example: when declaring a new internal constant for compatibility
        // reasons, it must remain un-prefixed.
        return !$this->belongsToExcludedNamespace($name)
            && (
                $this->reflector->isConstantInternal($name)
                || $this->isExposedConstantFromGlobalNamespace($name)
                || $this->isSymbolExposed($name, true)
            );
    }

    public function isExposedConstantFromGlobalNamespace(string $constantName): bool
    {
        return $this->symbolsConfiguration->shouldExposeGlobalConstants() && !strpos($constantName, '\\');
    }

    public function isExcludedNamespace(string $name): bool
    {
        return $this->symbolsConfiguration
            ->getExcludedNamespaces()
            ->isRegisteredNamespace($name);
    }

    private function _isExposedFunctionFromGlobalNamespace(string $functionName): bool
    {
        return $this->symbolsConfiguration->shouldExposeGlobalFunctions() && !strpos($functionName, '\\');
    }

    public function _isExposedClassFromGlobalNamespace(string $className): bool
    {
        return $this->symbolsConfiguration->shouldExposeGlobalClasses() && !strpos($className, '\\');
    }

    /**
     * Tells if a given symbol is exposed. Note however that it does not account for when:
     *
     * - The symbol belongs to the global namespace and the symbols of the global namespace of this type are exposed
     * - Belongs to an excluded namespace
     *
     * @param bool $constant Unlike other symbols, constants _can_ be case insensitive but 99% are not so we leave out
     *                       the case where they are not case sensitive.
     */
    private function isSymbolExposed(string $name, bool $constant = false): bool
    {
        $exposedSymbols = array_flip(
            array_unique([
                ...$this->symbolsConfiguration->getExposedClassNames(),
                ...$this->symbolsConfiguration->getExposedFunctionNames(),
                ...$this->symbolsConfiguration->getExposedConstantNames(),
            ]),
        );

        $exposedConstants = array_flip(
            array_unique(
                array_map(
                    static fn (string $symbolName) => self::lowerCaseConstantName($symbolName),
                    array_keys($exposedSymbols),
                ),
            ),
        );

        $exposedSymbolsPatterns = array_unique([
            ...$this->symbolsConfiguration->getExposedClassRegexes(),
            ...$this->symbolsConfiguration->getExposedFunctionRegexes(),
            ...$this->symbolsConfiguration->getExposedConstantRegexes(),
        ]);

        if (!$constant && array_key_exists(strtolower($name), $exposedSymbols)) {
            return true;
        }

        if ($constant && array_key_exists(self::lowerCaseConstantName($name), $exposedConstants)) {
            return true;
        }

        foreach ($exposedSymbolsPatterns as $pattern) {
            $pattern = !$constant ? $pattern.'i' : $pattern;

            if (1 === native_preg_match($pattern, $name)) {
                return true;
            }
        }

        return false;
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
}
