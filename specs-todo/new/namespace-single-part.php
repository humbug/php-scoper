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
        'title' => 'single-part new statements in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'single-part' => <<<'PHP'
<?php

namespace X;

new Foo();
----
<?php

namespace Humbug\X;

new Foo();

PHP
    ,

    'FQ single-part' => <<<'PHP'
<?php

namespace X;

new \Foo();
----
<?php

namespace Humbug\X;

new \Humbug\Foo();

PHP
    ,
];
