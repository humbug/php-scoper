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

return [
    'global_namespace_whitelist' => [
        'AppKernel',
        function (string $className): bool {
            return 'PHPUnit' === substr($className, 0, 6);
        },
    ],
    'patchers' => [
        function (string $filePath, string $prefix, string $content): string {
            //
            // PHP-Parser patch
            //

            if ($filePath === __DIR__.'/vendor/nikic/php-parser/lib/PhpParser/Lexer.php') {
                return preg_replace(
                    '%if \(defined\(\$name = \'PhpParser\\\\\\\\Parser\\\\\\\\Tokens%',
                    'if (defined($name = \''.$prefix . '\\\\\\\\PhpParser\\\\\\\\Parser\\\\\\\\Tokens',
                    $content
                );
            }

            if ($filePath === realpath(__DIR__.'/vendor/nikic/php-parser/lib/PhpParser/NodeAbstract.php')) {
                return preg_replace(
                    '%rtrim\(get_class\(\$this\), \'_\'\), 15\)%',
                    'rtrim(get_class($this), \'_\'), 15+23)',
                    $content
                );
            }

            return $content;
        },
    ],
];
