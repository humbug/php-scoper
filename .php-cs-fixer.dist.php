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
        'src',
        'tests',
    ])
    ->append([
        'bin/check-composer-root-version.php',
        'bin/dump-composer-root-version.php',
        'bin/php-scoper',
        'bin/root-version.php',
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

$config = new FidryConfig('', 74000);
$config->addRules($overriddenRules);

return $config->setFinder($finder);
