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
        title: 'Class constant call of a class imported with an aliased use statement in a namespace',

















    ),

    'Constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
    <?php
    
    namespace {
        class Foo {}
    }
    
    namespace A {
        use Foo as X;
        
        X::MAIN_CONST;
    }
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    namespace Humbug\A;
    
    use Humbug\Foo as X;
    X::MAIN_CONST;
    
    PHP,

    'FQ constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
    <?php
    
    namespace {
        class Foo {}
        class X {}
    }
    
    namespace A {
        use Foo as X;
        
        \X::MAIN_CONST;
    }
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    class X
    {
    }
    namespace Humbug\A;
    
    use Humbug\Foo as X;
    \Humbug\X::MAIN_CONST;
    
    PHP,

    'FQ constant call on an internal class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
    <?php
    
    namespace {
        class X {}
    }
    
    namespace A {
        use Reflector as X;
        
        \X::MAIN_CONST;
    }
    ----
    <?php
    
    namespace Humbug;
    
    class X
    {
    }
    namespace Humbug\A;
    
    use Reflector as X;
    \Humbug\X::MAIN_CONST;
    
    PHP,

    'Constant call on an internal class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
    <?php
    
    namespace A;
    
    use Reflector as X;
    
    X::MAIN_CONST;
    ----
    <?php
    
    namespace Humbug\A;
    
    use Reflector as X;
    X::MAIN_CONST;
    
    PHP,

    'FQ constant call on an exposed class which is imported via an aliased use statement and which belongs to the global namespace' => [
        exposeClasses: ['Foo'],
        'payload' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace A {
            use Foo as X;

            X::MAIN_CONST;
        }
        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        namespace Humbug\A;

        use Humbug\Foo as X;
        X::MAIN_CONST;

        PHP,

    'FQ constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
            class X {}
        }

        namespace A {
            use Foo as X;

            \X::MAIN_CONST;
        }
        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        class X
        {
        }
        namespace Humbug\A;

        use Humbug\Foo as X;
        \Humbug\X::MAIN_CONST;

        PHP,

    'FQ constant call on an internal class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        namespace {
            class X {}
        }

        namespace A {
            use Reflector as X;

            \X::MAIN_CONST;
        }
        ----
        <?php

        namespace Humbug;

        class X
        {
        }
        namespace Humbug\A;

        use Reflector as X;
        \Humbug\X::MAIN_CONST;

        PHP,

    'Constant call on an internal class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        namespace A;

        use Reflector as X;

        X::MAIN_CONST;
        ----
        <?php

        namespace Humbug\A;

        use Reflector as X;
        X::MAIN_CONST;

        PHP,

    'FQ constant call on an exposed class which is imported via an aliased use statement and which belongs to the global namespace' => [
        'expose-classes' => ['Foo'],
        'payload' => <<<'PHP'
            <?php

            namespace {
                class X {}
            }

            namespace A {
                use Foo as X;

                \X::MAIN_CONST;
            }
            ----
            <?php

            namespace Humbug;

            class X
            {
            }
            namespace Humbug\A;

            use Humbug\Foo as X;
            \Humbug\X::MAIN_CONST;

            PHP,
    ],
];
