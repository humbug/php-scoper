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
        'title' => 'Constant assignment',
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

    'Constant assignment in the global namespace' => <<<'PHP'
        <?php

        $x = DIRECTORY_SEPARATOR;
        $x = Client::class;
        $x = Client::VERSION;
        $x = \Client::class;
        $x = \Client::VERSION;
        $x = Guzzle\Client::class;
        $x = Guzzle\Client::VERSION;
        $x = \Guzzle\Client::class;
        $x = \Guzzle\Client::VERSION;

        ----
        <?php

        namespace Humbug;

        $x = \DIRECTORY_SEPARATOR;
        $x = Client::class;
        $x = Client::VERSION;
        $x = \Humbug\Client::class;
        $x = \Humbug\Client::VERSION;
        $x = Guzzle\Client::class;
        $x = Guzzle\Client::VERSION;
        $x = \Humbug\Guzzle\Client::class;
        $x = \Humbug\Guzzle\Client::VERSION;

        PHP,

    'Constant assignment in a namespace' => <<<'PHP'
        <?php

        namespace Acme;

        $x = DIRECTORY_SEPARATOR;
        $x = Client::class;
        $x = Client::VERSION;
        $x = \Client::class;
        $x = \Client::VERSION;
        $x = Guzzle\Client::class;
        $x = Guzzle\Client::VERSION;
        $x = \Guzzle\Client::class;
        $x = \Guzzle\Client::VERSION;

        ----
        <?php

        namespace Humbug\Acme;

        $x = \DIRECTORY_SEPARATOR;
        $x = Client::class;
        $x = Client::VERSION;
        $x = \Humbug\Client::class;
        $x = \Humbug\Client::VERSION;
        $x = Guzzle\Client::class;
        $x = Guzzle\Client::VERSION;
        $x = \Humbug\Guzzle\Client::class;
        $x = \Humbug\Guzzle\Client::VERSION;

        PHP,
];
