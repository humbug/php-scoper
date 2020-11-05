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
use function preg_match;
use function preg_match_all;
use function str_replace;
use function strlen;
use function strpos;
use function substr;

/**
 * Scopes the Symfony XML configuration files.
 */
final class XmlScoper implements Scoper
{
    private const FILE_PATH_PATTERN = '/\.xml$/i';

    private $decoratedScoper;

    public function __construct(Scoper $decoratedScoper)
    {
        $this->decoratedScoper = $decoratedScoper;
    }

    /**
     * {@inheritdoc}
     */
    public function scope(string $filePath, string $contents, string $prefix, array $patchers, Whitelist $whitelist): string
    {
        if (1 !== preg_match(self::FILE_PATH_PATTERN, $filePath)) {
            return $this->decoratedScoper->scope(...func_get_args());
        }

        $contents = $this->scopeClasses($contents, $prefix, $whitelist);
        $contents = $this->scopeNamespaces($contents, $prefix, $whitelist);

        return $contents;
    }

    private function scopeClasses(string $contents, string $prefix, Whitelist $whitelist): string
    {
        if (1 > preg_match_all('/(?:(?<singleClass>(?:[\p{L}_\d]+(?<singleSeparator>\\\\(?:\\\\)?))):)|(?<class>(?:[\p{L}_\d]+(?<separator>\\\\(?:\\\\)?)+)+[\p{L}_\d]+)/u', $contents, $matches)) {
            return $contents;
        }

        $contents = $this->replaceClasses(
            array_filter($matches['singleClass']),
            array_filter($matches['singleSeparator']),
            $prefix,
            $contents,
            $whitelist
        );

        $contents = $this->replaceClasses(
            array_filter($matches['class']),
            array_filter($matches['separator']),
            $prefix,
            $contents,
            $whitelist
        );

        return $contents;
    }

    private function scopeNamespaces(string $contents, string $prefix, Whitelist $whitelist): string
    {
        if (1 > preg_match_all('/<prototype.*\snamespace="(?:(?<namespace>(?:[^\\\\]+(?<separator>\\\\(?:\\\\)?))))"/', $contents, $matches)) {
            return $contents;
        }

        return $this->replaceClasses(
            array_filter($matches['namespace']),
            array_filter($matches['separator']),
            $prefix,
            $contents,
            $whitelist
        );
    }

    /**
     * @param string[] $classes
     * @param string[] $separators
     */
    private function replaceClasses(
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

            $psr4Service = '"'.$class.$separator.'"';

            if (false !== strpos($contents, $psr4Service)) {
                $offset = strpos($contents, $psr4Service) + strlen($psr4Service);

                $stringToScope = substr($contents, 0, $offset);
                $contents = substr($contents, $offset);

                $prefixedClass = $prefix.$separator.$class;

                $scopedContents .= $whitelist->belongsToWhitelistedNamespace($class.$separator.'__UnknownService__')
                    ? $stringToScope
                    : str_replace($class, $prefixedClass, $stringToScope)
                ;

                continue;
            }

            $offset = strpos($contents, $class) + strlen($class);

            $stringToScope = substr($contents, 0, $offset);
            $contents = substr($contents, $offset);

            $prefixedClass = $prefix.$separator.$class;

            $scopedContents .= $whitelist->belongsToWhitelistedNamespace($class)
                ? $stringToScope
                : str_replace($class, $prefixedClass, $stringToScope)
            ;

            if ($whitelist->isSymbolWhitelisted($class) || $whitelist->isGlobalWhitelistedClass($class)) {
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
