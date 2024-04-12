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
        'Global function call imported with a use statement in the global scope',
    ),

    'Global function call imported with a use statement in the global scope' => <<<'PHP'
    <?php
    
    use function main;
    
    main();
    ----
    <?php
    
    namespace Humbug;
    
    use function Humbug\main;
    main();
    
    PHP,

    'Uppercase global function call imported with a use statement in the global scope' => <<<'PHP'
    <?php
    
    use function main;
    
    MAIN();
    ----
    <?php
    
    namespace Humbug;
    
    use function Humbug\main;
    MAIN();
    
    PHP,

    'Global function call imported with a use statement in the global scope with global functions exposed' => [
        exposeGlobalFunctions: true,
        expectedRecordedFunctions: [
            ['main', 'Humbug\main'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        use function main;
        
        main();
        ----
        <?php
        
        namespace Humbug;
        
        use function Humbug\main;
        main();
        
        PHP,
    ],

    'Global FQ function call imported with a use statement in the global scope' => <<<'PHP'
    <?php
    
    use function main;
    
    \main();
    ----
    <?php
    
    namespace Humbug;
    
    use function Humbug\main;
    \Humbug\main();
    
    PHP,

    'Global FQ function call imported with a use statement in the global scope with global functions exposed' => [
        exposeGlobalFunctions: true,
        expectedRecordedFunctions: [
            ['main', 'Humbug\main'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        use function main;
        
        \main();
        ----
        <?php
        
        namespace Humbug;
        
        use function Humbug\main;
        \Humbug\main();
        
        PHP,
    ],

    'Uppercase global FQ function call imported with a use statement in the global scope with global functions exposed' => [
        exposeGlobalFunctions: true,
        expectedRecordedFunctions: [
            ['MAIN', 'Humbug\MAIN'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        use function main;
        
        \MAIN();
        ----
        <?php
        
        namespace Humbug;
        
        use function Humbug\main;
        \Humbug\MAIN();
        
        PHP,
    ],
];
