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

use Fidry\PhpCsFixerConfig\FidryConfig;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        'composer-root-version-checker/bin',
        'composer-root-version-checker/src',
        'composer-root-version-checker/tests',
        'res',
        'src',
        'tests',
    ])
    ->append([
        'bin/php-scoper',
        '.php-cs-fixer.dist.php',
        'scoper.inc.php',
    ]);

$overriddenRules = [
    'header_comment' => [
        'header' => <<<'EOF'
            This file is part of the humbug/php-scoper package.

            Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
                               Pádraic Brady <padraic.brady@gmail.com>

            For the full copyright and license information, please view the LICENSE
            file that was distributed with this source code.
            EOF,
        'location' => 'after_declare_strict',
    ],
    'mb_str_functions' => false,
    'no_unneeded_control_parentheses' => false,
    'yoda_style' => false,
];

$config = new FidryConfig('', 81_000);
$config->addRules($overriddenRules);
$config->setCacheFile(__DIR__.'/build/.php-cs-fixer.cache');

return $config->setFinder($finder);
