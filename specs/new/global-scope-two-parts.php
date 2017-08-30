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
        'title' => 'two-parts new statements in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // As there is nothing in PHP core with more than two-parts, we can safely prefix.
    'two-parts' => <<<'PHP'
<?php

new Foo\Bar();
----
<?php

new \Humbug\Foo\Bar();

PHP
    ,

    // As there is nothing in PHP core with more than two-parts, we can safely prefix.
    'FQ two-parts' => <<<'PHP'
<?php

new \Foo\Bar();
----
<?php

new \Humbug\Foo\Bar();

PHP
    ,

    // If is whitelisted is made into a FQCN to avoid autoloading issues
    'whitelisted two-parts' => [
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

new Foo\Bar();
----
<?php

new \Foo\Bar();

PHP
    ],

    'FQ whitelisted two-parts' => [
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

new \Foo\Bar();
----
<?php

new \Foo\Bar();

PHP
    ],
];
