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
        'title' => 'String value assigned as a class property',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'FQCN string argument: transform into a FQCN and prefix it' => <<<'PHP'
<?php

class Foo {
    var $x = 'Symfony\\Component\\Yaml\\Yaml';
    var $x = '\\Symfony\\Component\\Yaml\\Yaml';
    var $x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';
    var $x = '\\Humbug\\Symfony\\Component\\Yaml\\Yaml';
}

----
<?php

namespace Humbug;

class Foo
{
    var $x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';
    var $x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';
    var $x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';
    var $x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';
}

PHP
    ,
];
