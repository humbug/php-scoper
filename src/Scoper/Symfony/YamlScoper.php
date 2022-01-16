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

use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Whitelist;
use PhpParser\Node\Name\FullyQualified;
use function array_filter;
use function func_get_args;
use function preg_match as native_preg_match;
use function preg_match_all as native_preg_match_all;
use function Safe\substr;
use function str_replace;
use function strlen;
use function strpos;

/**
 * Scopes the Symfony YAML configuration files.
 */
final class YamlScoper implements Scoper
{
    private const FILE_PATH_PATTERN = '/\.ya?ml$/i';

    private Scoper $decoratedScoper;
    private string $prefix;
    private Whitelist $whitelist;

    public function __construct(
        Scoper $decoratedScoper,
        string $prefix,
        Whitelist $whitelist
    ) {
        $this->decoratedScoper = $decoratedScoper;
        $this->prefix = $prefix;
        $this->whitelist = $whitelist;
    }

    public function scope(string $filePath, string $contents): string
    {
        if (1 !== native_preg_match(self::FILE_PATH_PATTERN, $filePath)) {
            return $this->decoratedScoper->scope(...func_get_args());
        }

        if (1 > native_preg_match_all('/(?:(?<singleClass>(?:[\p{L}_\d]+(?<singleSeparator>\\\\(?:\\\\)?))):)|(?<class>(?:[\p{L}_\d]+(?<separator>\\\\(?:\\\\)?)+)+[\p{L}_\d]+)/u', $contents, $matches)) {
            return $contents;
        }

        $contents = self::replaceClasses(
            array_filter($matches['singleClass']),
            array_filter($matches['singleSeparator']),
            $this->prefix,
            $contents,
            $this->whitelist
        );

        $contents = self::replaceClasses(
            array_filter($matches['class']),
            array_filter($matches['separator']),
            $this->prefix,
            $contents,
            $this->whitelist
        );

        return $contents;
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
        Whitelist $whitelist
    ): string {
        if ([] === $classes) {
            return $contents;
        }

        $scopedContents = '';

        foreach ($classes as $index => $class) {
            $separator = $separators[$index];

            $psr4Service = $class.$separator.':';

            if (false !== strpos($contents, $psr4Service)) {
                $offset = strpos($contents, $psr4Service) + strlen($psr4Service);

                $stringToScope = substr($contents, 0, $offset);
                $contents = substr($contents, $offset);

                $prefixedClass = $prefix.$separator.$class;

                $scopedContents .= $whitelist->belongsToExcludedNamespace($class.$separator.'__UnknownService__')
                    ? $stringToScope
                    : str_replace($class, $prefixedClass, $stringToScope)
                ;

                continue;
            }

            $offset = strpos($contents, $class) + strlen($class);

            $stringToScope = substr($contents, 0, $offset);
            $contents = substr($contents, $offset);

            $prefixedClass = $prefix.$separator.$class;

            $scopedContents .= $whitelist->belongsToExcludedNamespace($class)
                ? $stringToScope
                : str_replace($class, $prefixedClass, $stringToScope)
            ;

            if ($whitelist->isSymbolExposed($class) || $whitelist->isExposedClassFromGlobalNamespace($class)) {
                $whitelist->recordWhitelistedClass(
                    new FullyQualified($class),
                    new FullyQualified($prefixedClass)
                );
            }
        }

        $scopedContents .= $contents;

        return $scopedContents;
    }
}
