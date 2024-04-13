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
        title: 'Internal symbols defined by the user',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'Known non-internal symbols (sanity test)' => <<<'PHP'
        <?php

        use Foo;
        use const BAR;
        use function baz;

        ----
        <?php

        namespace Humbug;

        use Humbug\Foo;
        use const Humbug\BAR;
        use function Humbug\baz;

        PHP,

    'Known non-internal symbols with global symbols exposed (sanity check)' => [
        exposeGlobalConstants: true,
        exposeGlobalClasses: true,
        exposeGlobalFunctions: true,
        'payload' => <<<'PHP'
            <?php

            use Foo;
            use const BAR;
            use function baz;

            ----
            <?php

            namespace Humbug;

            use Humbug\Foo;
            use const BAR;
            use function Humbug\baz;

            PHP,
    ],

    'Declared internal symbols' => [
        excludeClasses: ['Foo'],
        excludeFunctions: ['baz'],
        excludeConstants: ['BAR'],
        'payload' => <<<'PHP'
            <?php

            use Foo;
            use const BAR;
            use function baz;

            ----
            <?php

            namespace Humbug;

            use Foo;
            use const BAR;
            use function baz;

            PHP,
    ],
];
