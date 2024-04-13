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
        title: 'Class constant call of a class imported with a use statement in the global scope',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'Constant call on a class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        class Command {}

        use Command;

        Command::MAIN_CONST;
        ----
        <?php

        namespace Humbug;

        class Command
        {
        }
        use Humbug\Command;
        Command::MAIN_CONST;

        PHP,

    'FQ constant call on a class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        class Command {}

        use Command;

        \Command::MAIN_CONST;
        ----
        <?php

        namespace Humbug;

        class Command
        {
        }
        use Humbug\Command;
        \Humbug\Command::MAIN_CONST;

        PHP,

    'Constant call on an internal class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        use Reflector;

        Reflector::MAIN_CONST;
        ----
        <?php

        namespace Humbug;

        use Reflector;
        Reflector::MAIN_CONST;

        PHP,

    'FQ constant call on an internal class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        use Reflector;

        \Reflector::MAIN_CONST;
        ----
        <?php

        namespace Humbug;

        use Reflector;
        \Reflector::MAIN_CONST;

        PHP,

    'Constant call on an exposed class which is imported via a use statement and which belongs to the global namespace' => [
        exposeClasses: ['Foo'],
        'payload' => <<<'PHP'
            <?php

            use Foo;

            Foo::MAIN_CONST;
            ----
            <?php

            namespace Humbug;

            use Humbug\Foo;
            Foo::MAIN_CONST;

            PHP,
    ],

    'FQ constant call on an exposed class which is imported via a use statement and which belongs to the global namespace' => [
        exposeClasses: ['Foo'],
        'payload' => <<<'PHP'
            <?php

            use Foo;

            \Foo::MAIN_CONST;
            ----
            <?php

            namespace Humbug;

            use Humbug\Foo;
            \Humbug\Foo::MAIN_CONST;

            PHP,
    ],
];
