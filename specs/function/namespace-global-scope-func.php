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
        title: 'global function call in a namespace',
        

        exposeGlobalConstants: true,
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    // We don't do anything as there is no ways to distinguish between a namespaced function call
    // from the same namespace and a function registered in the global scope
    'single-part' => <<<'PHP'
        <?php

        namespace X;

        main();
        ----
        <?php

        namespace Humbug\X;

        main();

        PHP,

    'FQ single-part' => <<<'PHP'
        <?php

        namespace X;

        \main();
        ----
        <?php

        namespace Humbug\X;

        \Humbug\main();

        PHP,

    // In theory this case CAN be wrong. There is however a very high chance it
    // is not as it implies having both A\foo() and foo() in the
    // codebase with only foo() exposed.
    'Exposed constant call in a namespace' => [
        exposeFunctions: ['foo'],
        'payload' => <<<'PHP'
            <?php

            namespace A;

            foo();
            ----
            <?php

            namespace Humbug\A;

            \Humbug\foo();

            PHP,
    ],
];
