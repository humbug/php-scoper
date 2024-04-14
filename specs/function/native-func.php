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
        title: 'Native function calls',
        exposeGlobalConstants: true,
        exposeGlobalFunctions: true,
    ),

    'Internal function in a namespace' => <<<'PHP'
        <?php

        namespace Acme;

        $x = is_array([]);

        ----
        <?php

        namespace Humbug\Acme;

        $x = \is_array([]);

        PHP,

    'Namespaced function having the same name as an internal function' => <<<'PHP'
        <?php

        namespace Acme;

        use function Acme\is_array;

        $x = is_array([]);

        ----
        <?php

        namespace Humbug\Acme;

        use function Humbug\Acme\is_array;
        $x = is_array([]);

        PHP,
];
