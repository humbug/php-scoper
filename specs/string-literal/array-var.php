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
    'meta' => [
        'title' => 'Scalar literal assigned as key or value in an array',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
    ],

    'String argument: transform into a FQCN and prefix it' => <<<'PHP'
<?php

$x = [
    'Symfony\\Component\\Yaml\\Yaml' => 'Symfony\\Component\\Yaml\\_Yaml',
    '\\Symfony\\Component\\Yaml\\Yaml' => '\\Symfony\\Component\\Yaml\\_Yaml',
    'Humbug\\Symfony\\Component\\Yaml\\Yaml' => 'Humbug\\Symfony\\Component\\Yaml\\_Yaml',
    '\\Humbug\\Symfony\\Component\\Yaml\\Yaml' => '\\Humbug\\Symfony\\Component\\Yaml\\_Yaml',
];

----
<?php

namespace Humbug;

$x = ['Humbug\\Symfony\\Component\\Yaml\\Yaml' => 'Humbug\\Symfony\\Component\\Yaml\\_Yaml', 'Humbug\\Symfony\\Component\\Yaml\\Yaml' => 'Humbug\\Symfony\\Component\\Yaml\\_Yaml', 'Humbug\\Symfony\\Component\\Yaml\\Yaml' => 'Humbug\\Symfony\\Component\\Yaml\\_Yaml', 'Humbug\\Symfony\\Component\\Yaml\\Yaml' => 'Humbug\\Symfony\\Component\\Yaml\\_Yaml'];

PHP
    ,
];
