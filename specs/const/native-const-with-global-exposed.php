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

use Humbug\PhpScoper\SpecFramework\Config\Meta;

return [
    'meta' => new Meta(
        title: 'Native constant calls with the global constants exposed',
        exposeGlobalConstants: true,
        exposeGlobalFunctions: true,
    ),

    'Internal function in a namespace' => <<<'PHP'
        <?php

        namespace Acme;

        $x = DIRECTORY_SEPARATOR;

        ----
        <?php

        namespace Humbug\Acme;

        $x = \DIRECTORY_SEPARATOR;

        PHP,

    'Namespaced function having the same name as an internal function' => <<<'PHP'
        <?php

        namespace Acme;

        use const Acme\DIRECTORY_SEPARATOR;

        $x = DIRECTORY_SEPARATOR;

        ----
        <?php

        namespace Humbug\Acme;

        use const Humbug\Acme\DIRECTORY_SEPARATOR;
        $x = DIRECTORY_SEPARATOR;

        PHP,
];
