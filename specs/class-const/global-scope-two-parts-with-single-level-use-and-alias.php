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
        'title' => 'two-parts class constant references in the global scope with a single-level use statement and an alias',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // See tests for the use statements as to why we don't touch the use statement.
    // The use statement cannot be prefixed, but as the class is in two-parts this
    // is enough to know we can prefix it.
    'two-parts' => <<<'PHP'
<?php

use Foo as X;

X\Bar::MAIN_CONST;
----
<?php

use Foo as X;

\Humbug\Foo\Bar::MAIN_CONST;

PHP
    ,

    // See tests for the use statements as to why we don't touch the use statement.
    // As there is nothing in PHP core with more than two-parts, we can safely prefix.
    'FQ two-parts' => <<<'PHP'
<?php

use Foo as X;

\X\Bar::MAIN_CONST;
----
<?php

use Foo as X;

\Humbug\Foo\Bar::MAIN_CONST;

PHP
    ,

    // See tests for the use statements as to why we don't touch the use statement.
    // If is whitelisted is made into a FQCN to avoid autoloading issues
    'whitelisted two-parts' => [
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

use Foo as X;

X\Bar::MAIN_CONST;
----
<?php

use Foo as X;

\Foo\Bar::MAIN_CONST;

PHP
    ],

    // See tests for the use statements as to why we don't touch the use statement.
    'FQ whitelisted two-parts' => [
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

use Foo as X;

\X\Bar::MAIN_CONST;
----
<?php

use Foo as X;

\Foo\Bar::MAIN_CONST;

PHP
    ],
];
