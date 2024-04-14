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
        title: 'String with unicode',
        exposeGlobalConstants: true,
        exposeGlobalFunctions: true,
    ),

    // https://github.com/humbug/php-scoper/issues/464
    'String with unicode' => <<<'PHP'
        <?php

        namespace MaxMind\Db;

        class Reader {
            private static $UNICODE = "äßæ";
            private static $EMOJIS = "👾 🤖";
        }

        ----
        <?php

        namespace Humbug\MaxMind\Db;

        class Reader
        {
            private static $UNICODE = "äßæ";
            private static $EMOJIS = "👾 🤖";
        }

        PHP,
];
