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
        title: 'Namespaced function call statement in the global scope',

















    ),

    'Namespaced function call' => <<<'PHP'
    <?php
    
    PHPUnit\main();
    ----
    <?php

    namespace Humbug;

    PHPUnit\main();

    PHP,

    'FQ namespaced function call' => <<<'PHP'
    <?php
    
    \PHPUnit\main();
    ----
    <?php
    
    namespace Humbug;
    
    \Humbug\PHPUnit\main();
    
    PHP,

    'Exposed namespaced function call' => [
        exposeFunctions: ['PHPUnit\main'],
        expectedRecordedFunctions: [
            ['PHPUnit\main', 'Humbug\PHPUnit\main'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        PHPUnit\main();
        ----
        <?php

        namespace Humbug;

        \Humbug\PHPUnit\main();

        PHP,
    ],

    'FQ exposed namespaced function call' => [
        exposeFunctions: ['PHPUnit\main'],
        expectedRecordedFunctions: [
            ['PHPUnit\main', 'Humbug\PHPUnit\main'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        \PHPUnit\main();
        ----
        <?php
        
        namespace Humbug;
        
        \Humbug\PHPUnit\main();
        
        PHP,
    ],
];
