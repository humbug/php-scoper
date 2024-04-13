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
        title: 'Class static property call of a namespaced class imported with a use statement in the global scope',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'Constant call on a namespaced class partially imported with a use statement' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace Foo {
            class Bar {}
        }

        namespace {
            use Foo;

            Foo\Bar::$mainStaticProp;
        }
        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        namespace Humbug\Foo;

        class Bar
        {
        }
        namespace Humbug;

        use Humbug\Foo;
        Foo\Bar::$mainStaticProp;

        PHP,

    'Constant call on a namespaced class imported with a use statement' => <<<'PHP'
        <?php

        namespace Foo {
            class Bar {}
        }

        namespace Foo\Bar {
            class X {}
        }

        namespace {
            use Foo\Bar;

            Bar\X::$mainStaticProp;
        }
        ----
        <?php

        namespace Humbug\Foo;

        class Bar
        {
        }
        namespace Humbug\Foo\Bar;

        class X
        {
        }
        namespace Humbug;

        use Humbug\Foo\Bar;
        Bar\X::$mainStaticProp;

        PHP,

    'FQ constant call on a namespaced class imported with a use statement' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace Foo {
            class Bar {}
        }

        namespace {
            use Foo;

            \Foo\Bar::$mainStaticProp;
        }
        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        namespace Humbug\Foo;

        class Bar
        {
        }
        namespace Humbug;

        use Humbug\Foo;
        \Humbug\Foo\Bar::$mainStaticProp;

        PHP,

    'FQ Constant call on an exposed namespaced class partially imported with a use statement' => [
        exposeClasses: ['Foo\Bar'],
        expectedRecordedClasses: [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
            <?php

            namespace {
                class Foo {}
            }

            namespace Foo {
                class Bar {}
            }

            namespace {
                use Foo;

                Foo\Bar::$mainStaticProp;
            }
            ----
            <?php

            namespace Humbug;

            class Foo
            {
            }
            namespace Humbug\Foo;

            class Bar
            {
            }
            \class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
            namespace Humbug;

            use Humbug\Foo;
            Foo\Bar::$mainStaticProp;

            PHP,
    ],

    'FQ constant call on an exposed namespaced class imported with a use statement' => [
        exposeClasses: ['Foo\Bar'],
        expectedRecordedClasses: [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
            <?php

            namespace {
                class Foo {}
            }

            namespace Foo {
                class Bar {}
            }

            namespace {
                use Foo;

                \Foo\Bar::$mainStaticProp;
            }
            ----
            <?php

            namespace Humbug;

            class Foo
            {
            }
            namespace Humbug\Foo;

            class Bar
            {
            }
            \class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
            namespace Humbug;

            use Humbug\Foo;
            \Humbug\Foo\Bar::$mainStaticProp;

            PHP,
    ],
];
