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
        title: 'Namespace declarations after a hashbang',
    ),

    <<<'PHP'
        #!/usr/bin/env php
        <?php

        namespace Foo;

        ----
        #!/usr/bin/env php
        <?php
        namespace Humbug\Foo;


        PHP,
];
