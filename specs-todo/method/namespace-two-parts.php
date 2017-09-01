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
        'title' => 'two-parts static method calls in a namespace scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // As there is nothing in PHP core with more than two-parts, we can safely prefix.
    'two-parts' => <<<'PHP'
<?php

namespace X;

Foo\Bar::main();
----
<?php

namespace Humbug\X;

Foo\Bar::main();

PHP
    ,

    // As there is nothing in PHP core with more than two-parts, we can safely prefix.
    'FQ two-parts' => <<<'PHP'
<?php

namespace X;

\Foo\Bar::main();
----
<?php

namespace Humbug\X;

\Humbug\Foo\Bar::main();

PHP
    ,

    // If is whitelisted is made into a FQCN to avoid autoloading issues
    'whitelisted two-parts' => [
        'whitelist' => ['X\Foo\Bar'],
        'payload' => <<<'PHP'
<?php

namespace X;

Foo\Bar::main();
----
<?php

namespace Humbug\X;

\X\Foo\Bar::main();

PHP
    ],

    'FQ whitelisted two-parts' => [
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

namespace X;

\Foo\Bar::main();
----
<?php

namespace Humbug\X;

\Foo\Bar::main();

PHP
    ],
];
