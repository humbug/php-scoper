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
        'title' => 'New statement call in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'New statement call of a class belonging to the global namespace' => [
        'payload' => <<<'PHP'
<?php

new Foo();
----
<?php

namespace Humbug;

new \Humbug\Foo();

PHP
    ],

    'New statement call of an internal class belonging to the global namespace' => [
        'payload' => <<<'PHP'
<?php

new ArrayIterator([]);
----
<?php

namespace Humbug;

new \ArrayIterator([]);

PHP
    ],

    'FQ new statement call of a class belonging to the global namespace' => [
        'payload' => <<<'PHP'
<?php

new \Foo();
----
<?php

namespace Humbug;

new \Humbug\Foo();

PHP
    ],

    'New statement call of an unknown class belonging to the global namespace' => [
        'payload' => <<<'PHP'
<?php

new Unknown();
----
<?php

namespace Humbug;

new \Humbug\Unknown();

PHP
    ],
];
