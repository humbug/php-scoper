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
        title: 'Namespace declarations with braces',
    ),

    'One level namespace' => <<<'PHP'
        <?php

        namespace Foo;

        ----
        <?php

        namespace Humbug\Foo;


        PHP,

    'Two levels namespace' => <<<'PHP'
        <?php

        namespace Foo\Bar;

        ----
        <?php

        namespace Humbug\Foo\Bar;


        PHP,
];
