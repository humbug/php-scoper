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
        title: 'Two-levels namespaced constant call in the global scope',

















    ),

    'Namespaced constant call' => <<<'PHP'
    <?php
    
    PHPUnit\Command\DUMMY_CONST;
    ----
    <?php

    namespace Humbug;

    \Humbug\PHPUnit\Command\DUMMY_CONST;

    PHP,

    'FQ namespaced constant call' => <<<'PHP'
    <?php
    
    \PHPUnit\Command\DUMMY_CONST;
    ----
    <?php
    
    namespace Humbug;
    
    \Humbug\PHPUnit\Command\DUMMY_CONST;
    
    PHP,

    'Namespaced constant call on an exposed constant' => [
        exposeConstants: ['PHPUnit\Command\DUMMY_CONST'],
        'payload' => <<<'PHP'
        <?php
        
        PHPUnit\Command\DUMMY_CONST;
        ----
        <?php

        namespace Humbug;

        \PHPUnit\Command\DUMMY_CONST;

        PHP,
    ],
];
