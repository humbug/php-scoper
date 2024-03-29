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
        'title' => 'Attributes',
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
    ],

    'FQCN attribute' => <<<'PHP'
    <?php
    
    namespace PhpScoper\Command;

    #[\PhpScoper\Attribute\AsCommand(name: "main")]
    class MainCommand {}
    
    ----
    <?php

    namespace Humbug\PhpScoper\Command;

    #[\Humbug\PhpScoper\Attribute\AsCommand(name: "main")]
    class MainCommand
    {
    }

    PHP,

    'imported attribute' => <<<'PHP'
    <?php
    
    namespace PhpScoper\Command;

    use PhpScoper\Attribute\AsCommand;

    #[AsCommand(name: "main")]
    class MainCommand {}
    
    ----
    <?php

    namespace Humbug\PhpScoper\Command;

    use Humbug\PhpScoper\Attribute\AsCommand;
    #[AsCommand(name: "main")]
    class MainCommand
    {
    }
  
    PHP,
];
