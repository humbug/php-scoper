<?php

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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

$config = new FidryConfig(
    <<<'EOF'
        This file is part of the humbug/php-scoper package.

        Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
                           Pádraic Brady <padraic.brady@gmail.com>

        For the full copyright and license information, please view the LICENSE
        file that was distributed with this source code.
        EOF,
    74000,
);

return $config->setFinder($finder);
