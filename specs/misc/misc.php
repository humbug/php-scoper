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
        title: 'Miscellaneous',
        exposeGlobalConstants: true,
        exposeGlobalFunctions: true,
    ),

    'Empty file' => <<<'PHP'
        <?php

        ----
        <?php



        PHP,

    'Empty php file with a declare statement' => <<<'PHP'
        <?php declare(strict_types=1);

        ----
        <?php

        declare (strict_types=1);

        PHP,

    'Account for PHP case insentitiveness when resolving FQCNs' => <<<'PHP'
        <?php

        namespace Foo {
            class X {}
            class StdClasS {}
        }

        namespace {
            use FOO\x as Y;
            use Foo\stdClass;

            var_dump(new y());
            var_dump(new STDCLASS());
        }
        ----
        <?php

        namespace Humbug\Foo;

        class X
        {
        }
        class StdClasS
        {
        }
        namespace Humbug;

        use Humbug\FOO\x as Y;
        use Humbug\Foo\stdClass;
        \var_dump(new y());
        \var_dump(new STDCLASS());

        PHP,
];
