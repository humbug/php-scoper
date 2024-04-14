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
        title: 'Excerpts of code used for executable PHP files (e.g. for PHPUnit)',
    ),

    'Some statements made directly in the global namespace' => <<<'PHP'
        <?php declare(strict_types=1);

        if (\true) {
            echo "yo";
        }

        if (\false) {
            echo "oy";
        }

        ----
        <?php

        declare (strict_types=1);
        namespace Humbug;

        if (\true) {
            echo "yo";
        }
        if (\false) {
            echo "oy";
        }

        PHP,

    'Some statements made directly in the global namespace with a shebang' => <<<'PHP'
        #!/usr/bin/env php
        <?php declare(strict_types=1);

        if (\true) {
            echo "yo";
        }

        if (\false) {
            echo "oy";
        }

        ----
        #!/usr/bin/env php
        <?php
        declare (strict_types=1);
        namespace Humbug;

        if (\true) {
            echo "yo";
        }
        if (\false) {
            echo "oy";
        }

        PHP,
];
