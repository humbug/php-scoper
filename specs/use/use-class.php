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

use Humbug\PhpScoper\Scoper\Spec\Meta;

return [
    'meta' => new Meta(
        title: 'Use statements',
        

        exposeGlobalConstants: true,
        
        exposeGlobalFunctions: true,
        
        
       
       

        
        
        
       

        
       
    ),

    'Use statement of a class belonging to the global scope' => <<<'PHP'
        <?php

        class Foo {}

        use Foo;

        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        use Humbug\Foo;

        PHP,

    'FQ use statement of a class belonging to the global scope' => <<<'PHP'
        <?php

        class Foo {}

        use \Foo;

        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        use Humbug\Foo;

        PHP,

    'Use statement of an internal class belonging to the global scope' => <<<'PHP'
        <?php

        use ArrayIterator;

        ----
        <?php

        namespace Humbug;

        use ArrayIterator;

        PHP,

    'Use statement of an internal class belonging to the global scope' => <<<'PHP'
        <?php

        use \ArrayIterator;

        ----
        <?php

        namespace Humbug;

        use ArrayIterator;

        PHP,

    'Use statement of a non existent class belonging to the global scope' => <<<'PHP'
        <?php

        use Unknown;

        ----
        <?php

        namespace Humbug;

        use Humbug\Unknown;

        PHP,

    'Use statement of an exposed class belonging to the global scope' => [
        exposeClasses: ['Foo'],
        expectedRecordedClasses: [
            ['Foo', 'Humbug\Foo'],
        ],
        'payload' => <<<'PHP'
            <?php

            class Foo {}

            use Foo;

            ----
            <?php

            namespace Humbug;

            class Foo
            {
            }
            \class_alias('Humbug\\Foo', 'Foo', \false);
            use Humbug\Foo;

            PHP,
    ],

    'Use statement of a class belonging to the global scope which has been excluded' => [
        excludeNamespaces: [''],
        expectedRecordedClasses: [
            ['Foo', 'Humbug\Foo'],
        ],
        'payload' => <<<'PHP'
            <?php

            class Foo {}

            use Foo;

            ----
            <?php

            namespace {
                class Foo
                {
                }
                \class_alias('Humbug\\Foo', 'Foo', \false);
                use Foo;
            }

            PHP,
    ],

    'Use statement of an exposed class belonging to the global scope which has been excluded' => [
        excludeNamespaces: [''],
        exposeClasses: ['Foo'],
        expectedRecordedClasses: [
            ['Foo', 'Humbug\Foo'],
        ],
        'payload' => <<<'PHP'
            <?php

            class Foo {}

            use Foo;

            ----
            <?php

            namespace {
                class Foo
                {
                }
                \class_alias('Humbug\\Foo', 'Foo', \false);
                use Foo;
            }

            PHP,
    ],

    'Use statement of two-level class' => <<<'PHP'
        <?php

        namespace Foo {
            class Bar {}
        }

        namespace {
            use Foo\Bar;
        }

        ----
        <?php

        namespace Humbug\Foo;

        class Bar
        {
        }
        namespace Humbug;

        use Humbug\Foo\Bar;

        PHP,

    'Already prefixed use statement of two-level class' => <<<'PHP'
        <?php

        namespace Foo {
            class Bar {}
        }

        namespace {
            use Humbug\Foo\Bar;
        }

        ----
        <?php

        namespace Humbug\Foo;

        class Bar
        {
        }
        namespace Humbug;

        use Humbug\Foo\Bar;

        PHP,

    'Use statement of two-level class which has been exposed' => [
        exposeClasses: ['Foo\Bar'],
        expectedRecordedClasses: [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
            <?php

            namespace Foo {
                class Bar {}
            }

            namespace {
                use Foo\Bar;
            }

            ----
            <?php

            namespace Humbug\Foo;

            class Bar
            {
            }
            \class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
            namespace Humbug;

            use Humbug\Foo\Bar;

            PHP,
    ],

    'Use statement of two-level class belonging to a excluded namespace' => [
        excludeNamespaces: ['Foo'],
        'payload' => <<<'PHP'
            <?php

            namespace Foo {
                class Bar {}
            }

            namespace {
                use Foo\Bar;
            }

            ----
            <?php

            namespace Foo;

            class Bar
            {
            }
            namespace Humbug;

            use Foo\Bar;

            PHP,
    ],

    'Use statement of exposed two-level class belonging to a excluded namespace' => [
        excludeNamespaces: ['Foo'],
        exposeClasses: ['Foo'],
        'payload' => <<<'PHP'
            <?php

            namespace Foo {
                class Bar {}
            }

            namespace {
                use Foo\Bar;
            }

            ----
            <?php

            namespace Foo;

            class Bar
            {
            }
            namespace Humbug;

            use Foo\Bar;

            PHP,
    ],
];
