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
        'title' => 'Exposing symbols case sensitiveness',
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
        'expected-recorded-ambiguous-functions' => [],
    ],

    'Classes marked as exposed are case insensitive' => [
        'expose-classes' => ['acme\foo'],
        'expected-recorded-classes' => [
            ['Acme\Foo', 'Humbug\Acme\Foo'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace Acme;
        
        class Foo {
            public function foo() {}
        }
        ----
        <?php
        
        namespace Humbug\Acme;
        
        class Foo
        {
            public function foo()
            {
            }
        }
        \class_alias('Humbug\\Acme\\Foo', 'Acme\\Foo', \false);
        
        PHP,
    ],

    'Constants marked as exposed are case sensitive' => [
        'expose-constants' => ['Acme\Foo', 'Acme\Bar'],
        'payload' => <<<'PHP'
        <?php
        
        namespace Acme;
        
        const FOO = 'foo';
        define('Acme\BAR', 'bar');
        echo \Acme\BAR;
        ----
        <?php
        
        namespace Humbug\Acme;
        
        const FOO = 'foo';
        \define('Humbug\\Acme\\BAR', 'bar');
        echo \Humbug\Acme\BAR;
        
        PHP,
    ],

    'The namespace of constant exposed are case insensitive' => [
        'expose-constants' => ['acme\FOO', 'acme\BAR'],
        'payload' => <<<'PHP'
        <?php
        
        namespace Acme;
        
        const FOO = 'foo';
        define('Acme\BAR', 'bar');
        ----
        <?php
        
        namespace Humbug\Acme;
        
        \define('Acme\\FOO', 'foo');
        \define('Acme\\BAR', 'bar');
        
        PHP,
    ],

    'Namespaces excluded are case insensitive' => [
        'exclude-namespaces' => ['acme'],
        'payload' => <<<'PHP'
        <?php
        
        namespace Acme;
        
        class Foo {
            public function foo() {}
        }
        
        const FOO = 'foo';
        define('Acme\BAR', 'bar');
        
        namespace Bar;
        
        use Acme\Foo;
        use const Acme\FOO;
        use const Acme\BAR;
        
        new Foo();
        new \acmE\Foo();
        
        FOO;
        \acmE\FOO;
        
        BAR;
        \acmE\BAR;
        ----
        <?php
        
        namespace Acme;
        
        class Foo
        {
            public function foo()
            {
            }
        }
        const FOO = 'foo';
        \define('Acme\\BAR', 'bar');
        namespace Humbug\Bar;
        
        use Acme\Foo;
        use const Acme\FOO;
        use const Acme\BAR;
        new Foo();
        new \acmE\Foo();
        FOO;
        \acmE\FOO;
        BAR;
        \acmE\BAR;
        
        PHP,
    ],

    'Use statements of excluded namespaces are case insensitive' => [
        'exclude-namespaces' => ['acme'],
        'payload' => <<<'PHP'
        <?php
        
        use Acme\Foo;
        use const Acme\FOO;
        use const Acme\BAR;
        ----
        <?php
        
        namespace Humbug;
        
        use Acme\Foo;
        use const Acme\FOO;
        use const Acme\BAR;
        
        PHP,
    ],
];
