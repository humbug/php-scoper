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
        'title' => 'String literal assigned as a key or value in a class constant',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'FQCN string argument: transform into a FQCN and prefix it' => <<<'PHP'
<?php

class Foo {
    const X = [
        'Symfony\\Component\\Yaml\\Yaml' => 'Symfony\\Component\\Yaml\\Yaml',
        '\\Symfony\\Component\\Yaml\\Yaml' => '\\Symfony\\Component\\Yaml\\Yaml',
        'Humbug\\Symfony\\Component\\Yaml\\Yaml' => 'Humbug\\Symfony\\Component\\Yaml\\Yaml',
        '\\Humbug\\Symfony\\Component\\Yaml\\Yaml' => '\\Humbug\\Symfony\\Component\\Yaml\\Yaml',
    ];
}

----
<?php

namespace Humbug;

class Foo
{
    const X = ['Humbug\\Symfony\\Component\\Yaml\\Yaml' => 'Humbug\\Symfony\\Component\\Yaml\\Yaml', 'Humbug\\Symfony\\Component\\Yaml\\Yaml' => 'Humbug\\Symfony\\Component\\Yaml\\Yaml', 'Humbug\\Symfony\\Component\\Yaml\\Yaml' => 'Humbug\\Symfony\\Component\\Yaml\\Yaml', 'Humbug\\Symfony\\Component\\Yaml\\Yaml' => 'Humbug\\Symfony\\Component\\Yaml\\Yaml'];
}

PHP
    ,
];
