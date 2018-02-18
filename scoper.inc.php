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

use Isolated\Symfony\Component\Finder\Finder;

return [
    'finders' => [
        Finder::create()->files()->in('src'),
        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->notName('/LICENSE|.*\\.md|.*\\.dist|Makefile|composer\\.json|composer\\.lock/')
            ->exclude([
                'doc',
                'test',
                'test_old',
                'tests',
                'Tests',
                'vendor-bin',
            ])
            ->in('vendor'),
        Finder::create()->append([
            'bin/php-scoper',
            'composer.json',
            'box.json',
        ]),
    ],
    'whitelist' => [
        Finder::class,
    ],
    'patchers' => [
        function (string $filePath, string $prefix, string $contents): string {
            //
            // PHP-Parser patch
            //

            if ($filePath === __DIR__.'/vendor/nikic/php-parser/lib/PhpParser/Lexer.php') {
                return preg_replace(
                    '%if \(defined\(\$name = \'PhpParser\\\\\\\\Parser\\\\\\\\Tokens%',
                    'if (defined($name = \''.$prefix.'\\\\\\\\PhpParser\\\\\\\\Parser\\\\\\\\Tokens',
                    $contents
                );
            }

            if ($filePath === realpath(__DIR__.'/vendor/nikic/php-parser/lib/PhpParser/NodeAbstract.php')) {
                $length = 15 + strlen($prefix) + 1;

                return preg_replace(
                    '%rtrim\(get_class\(\$this\), \'_\'\), 15\)%',
                    sprintf('rtrim(get_class($this), \'_\'), %d)', $length),
                    $contents
                );
            }

            return $contents;
        },
        function (string $filePath, string $prefix, string $contents): string {
            $finderClass = sprintf('\%s\%s', $prefix, Finder::class);

            return str_replace($finderClass, '\\'.Finder::class, $contents);
        },
    ],
];
