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

use function array_filter;
use function array_map;
use function array_unique;
use function explode;
use const Humbug\PhpScoper\CLASS_NAME_PATTERN;
use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Whitelist;
use function func_get_args;
use function implode;
use function preg_match_all;
use function preg_replace;
use const PREG_UNMATCHED_AS_NULL;
use const SORT_STRING;
use function sprintf;
use function str_replace;
use function strlen;
use function strpos;
use function substr;

/**
 * Scopes the Symfony YAML configuration files.
 */
final class YamlScoper implements Scoper
{
    private const FILE_PATH_PATTERN = '/.*\.ya?ml$/i';

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

        if (1 > preg_match_all('/(?:(?<singleClass>(?:[\p{L}_\d]+(?<singleSeparator>\\\\(?:\\\\)?){1})):)|(?<class>(?:[\p{L}_\d]+(?<separator>\\\\(?:\\\\)?)+)+[\p{L}_\d]+)/u', $contents, $matches)) {
            return $contents;
        }

        $contents = $this->replaceClasses(
            array_filter($matches['singleClass']),
            array_filter($matches['singleSeparator']),
            $prefix,
            $contents
        );

        $contents = $this->replaceClasses(
            array_filter($matches['class']),
            array_filter($matches['separator']),
            $prefix,
            $contents
        );

        return $contents;
    }


    private function replaceClasses(array $classes, array $separators, string $prefix, string $contents): string
    {
        if ([] === $classes) {
            return $contents;
        }

        $scopedContents = '';

        foreach ($classes as $index => $class) {
            $offset = strpos($contents, $class) + strlen($class);

            $stringToScope = substr($contents, 0, $offset);
            $contents = substr($contents, $offset);

            $scopedContents .= str_replace($class, $prefix.$separators[$index].$class, $stringToScope);
        }

        $scopedContents .= $contents;

        return $scopedContents;
    }
}
