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
        maxPhpVersion: 70_499,
        title: 'Match',
    ),

    'match' => <<<'PHP'
        <?php declare(strict_types=1);

        namespace Acme {
            use Acme\Foo\Match;

            class Foo implements Match {}
        }

        namespace Acme\Foo {
            interface Match {}
        }

        ----
        <?php

        declare (strict_types=1);
        namespace Humbug\Acme;

        use Humbug\Acme\Foo\Match;
        class Foo implements Match
        {
        }
        namespace Humbug\Acme\Foo;

        interface Match
        {
        }

        PHP,
];
