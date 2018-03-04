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
        'title' => 'String literal used as a method argument',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'FQCN string argument: transform into a FQCN and prefix it' => <<<'PHP'
<?php

class Foo {
    function foo($x = 'Symfony\\Component\\Yaml\\Yaml') {}
}

(new X())->foo('Symfony\\Component\\Yaml\\Yaml');

----
<?php

namespace Humbug;

class Foo
{
    function foo($x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml')
    {
    }
}
(new \Humbug\X())->foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');

PHP
    ,

    'FQCN string argument with a static method: transform into a FQCN and prefix it' => <<<'PHP'
<?php

class Foo {
    static function foo($x = 'Symfony\\Component\\Yaml\\Yaml') {}
}

X::foo('Symfony\\Component\\Yaml\\Yaml');

----
<?php

namespace Humbug;

class Foo
{
    static function foo($x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml')
    {
    }
}
\Humbug\X::foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');

PHP
    ,
];
