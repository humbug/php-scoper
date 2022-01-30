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

        'expose-global-constants' => false,
        'expose-global-classes' => false,
        'expose-global-functions' => false,
        'expose-namespaces' => [],
        'expose-constants' => [],
        'expose-classes' => [],
        'expose-functions' => [],

        'exclude-namespaces' => [],
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],

        'expected-recorded-classes' => [],
        'expected-recorded-functions' => [],
    ],

    'FQCN string argument' => <<<'PHP'
    <?php
    
    class Foo {
        function foo($x = 'Symfony\\Component\\Yaml\\Ya_1', $y = 'Foo') {}
    }
    
    (new X())->foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1', $y = 'Foo');
    
    $x = new X();
    
    $x->foo()('Humbug\\Symfony\\Component\\Yaml\\Ya_1', $y = 'Foo');
    
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
        function foo($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1', $y = 'Foo')
        {
        }
    }
    (new X())->foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1', $y = 'Foo');
    $x = new X();
    $x->foo()('Humbug\\Symfony\\Component\\Yaml\\Ya_1', $y = 'Foo');
    
    PHP,

    'FQCN string argument with a static method' => <<<'PHP'
    <?php
    
    class Foo {
        static function foo($x = 'Symfony\\Component\\Yaml\\Ya_1', $y = 'Foo') {}
    }
    
    X::foo('Symfony\\Component\\Yaml\\Ya_1', $y = 'Foo');
    
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
        static function foo($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1', $y = 'Foo')
        {
        }
    }
    X::foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1', $y = 'Foo');
    
    PHP,
];
