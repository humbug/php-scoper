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

namespace Humbug\PhpScoper\Scoper\Symfony;

use Humbug\PhpScoper\Scoper\Scoper;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PhpParser\Node\Name\FullyQualified;
use function array_filter;
use function func_get_args;
use function preg_match as native_preg_match;
use function preg_match_all as native_preg_match_all;
use function str_contains;
use function str_replace;
use function strlen;
use function strpos;
use function substr;

/**
 * Scopes the Symfony XML configuration files.
 */
final readonly class XmlScoper implements Scoper
{
    private const XML_EXTENSION_REGEX = '/\.xml$/i';
    private const NAMESPACE_PATTERN = '/<prototype.*\snamespace="(?:(?<namespace>(?:[^\\\\]+(?<separator>\\\\(?:\\\\)?))))"/';
    private const SINGLE_CLASS_PATTERN = '/(?:(?<singleClass>(?:[\p{L}_\d]+(?<singleSeparator>\\\\(?:\\\\)?))):)|(?<class>(?:[\p{L}_\d]+(?<separator>\\\\(?:\\\\)?)+)+[\p{L}_\d]+)/u';

    public function __construct(
        private Scoper $decoratedScoper,
        private string $prefix,
        private EnrichedReflector $enrichedReflector,
        private SymbolsRegistry $symbolsRegistry,
    ) {
    }

    public function scope(string $filePath, string $contents): string
    {
        if (1 !== native_preg_match(self::XML_EXTENSION_REGEX, $filePath)) {
            return $this->decoratedScoper->scope(...func_get_args());
        }

        $contents = self::scopeClasses(
            $contents,
            $this->prefix,
            $this->enrichedReflector,
            $this->symbolsRegistry,
        );

        return self::scopeNamespaces(
            $contents,
            $this->prefix,
            $this->enrichedReflector,
            $this->symbolsRegistry,
        );
    }

    private static function scopeClasses(
        string $contents,
        string $prefix,
        EnrichedReflector $enrichedReflector,
        SymbolsRegistry $symbolsRegistry
    ): string {
        if (1 > native_preg_match_all(self::SINGLE_CLASS_PATTERN, $contents, $matches)) {
            return $contents;
        }

        $contents = self::replaceClasses(
            array_filter($matches['singleClass']),
            array_filter($matches['singleSeparator']),
            $prefix,
            $contents,
            $enrichedReflector,
            $symbolsRegistry,
        );

        return self::replaceClasses(
            array_filter($matches['class']),
            array_filter($matches['separator']),
            $prefix,
            $contents,
            $enrichedReflector,
            $symbolsRegistry,
        );
    }

    private static function scopeNamespaces(
        string $contents,
        string $prefix,
        EnrichedReflector $enrichedReflector,
        SymbolsRegistry $symbolsRegistry
    ): string {
        if (1 > native_preg_match_all(self::NAMESPACE_PATTERN, $contents, $matches)) {
            return $contents;
        }

        return self::replaceClasses(
            array_filter($matches['namespace']),
            array_filter($matches['separator']),
            $prefix,
            $contents,
            $enrichedReflector,
            $symbolsRegistry,
        );
    }

    /**
     * @param string[] $classes
     * @param string[] $separators
     */
    private static function replaceClasses(
        array $classes,
        array $separators,
        string $prefix,
        string $contents,
        EnrichedReflector $enrichedReflector,
        SymbolsRegistry $symbolsRegistry
    ): string {
        if ([] === $classes) {
            return $contents;
        }

        $scopedContents = '';

        foreach ($classes as $index => $class) {
            $separator = $separators[$index];

            $psr4Service = '"'.$class.$separator.'"';

            if (str_contains($contents, $psr4Service)) {
                $offset = strpos($contents, $psr4Service) + strlen($psr4Service);

                $stringToScope = substr($contents, 0, $offset);
                $contents = substr($contents, $offset);

                $prefixedClass = $prefix.$separator.$class;

                $scopedContents .= $enrichedReflector->belongsToExcludedNamespace($class.$separator.'__UnknownService__')
                    ? $stringToScope
                    : str_replace($class, $prefixedClass, $stringToScope);

                continue;
            }

            $offset = strpos($contents, $class) + strlen($class);

            $stringToScope = substr($contents, 0, $offset);
            $contents = substr($contents, $offset);

            $prefixedClass = $prefix.$separator.$class;

            $scopedContents .= $enrichedReflector->belongsToExcludedNamespace($class)
                ? $stringToScope
                : str_replace($class, $prefixedClass, $stringToScope);

            if ($enrichedReflector->isExposedClass($class)) {
                $symbolsRegistry->recordClass(
                    new FullyQualified($class),
                    new FullyQualified($prefixedClass),
                );
            }
        }

        $scopedContents .= $contents;

        return $scopedContents;
    }
}
