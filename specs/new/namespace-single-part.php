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
use Humbug\PhpScoper\Scoper\Spec\SpecWithConfig;

return [
    'meta' => new Meta(
        title: 'New statement call in a namespace',
        exposeGlobalConstants: true,
        exposeGlobalFunctions: true,
    ),

    'New statement call of a class' => SpecWithConfig::create(
        spec: <<<'PHP'
            <?php

            namespace A;

            class Foo {}

            new Foo();
            ----
            <?php

            namespace Humbug\A;

            class Foo
            {
            }
            new Foo();

            PHP,
    ),

    'FQ new statement call of a class belonging to the global namespace' => SpecWithConfig::create(
        spec: <<<'PHP'
            <?php

            namespace {
                class Foo {}
            }

            namespace A {
                new \Foo();
            }
            ----
            <?php

            namespace Humbug;

            class Foo
            {
            }
            namespace Humbug\A;

            new \Humbug\Foo();

            PHP,
    ),
];
