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
        'title' => 'Two-parts namespaced constant call in the global scope with a single-level aliased use statement',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => false,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Namespaced constant call with namespace partially imported' => <<<'PHP'
<?php

class Foo {}

use Foo as A;

A\Bar\DUMMY_CONST;
----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo as A;
\Humbug\Foo\Bar\DUMMY_CONST;

PHP
    ,

    'FQ namespaced constant call with namespace partially imported' => <<<'PHP'
<?php

class Foo {}

use Foo as A;

\A\Bar\DUMMY_CONST;
----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo as A;
\Humbug\A\Bar\DUMMY_CONST;

PHP
    ,

    'Whitelisted namespaced constant call with namespace partially imported' => [
        'whitelist' => ['Foo\Bar\DUMMY_CONST'],
        'payload' => <<<'PHP'
<?php

class Foo {}

use Foo as A;

A\Bar\DUMMY_CONST;
----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo as A;
\Foo\Bar\DUMMY_CONST;

PHP
    ],
];
