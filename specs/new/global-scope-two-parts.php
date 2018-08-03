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
        'title' => 'New statement call of a namespaced class in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'New statement call of a namespaced class' => [
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    new Foo\Bar();
}
----
<?php

namespace Humbug\Foo;

class Bar
{
}
namespace Humbug;

new \Humbug\Foo\Bar();

PHP
    ],

    'FQ new statement call of a namespaced class' => [
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    new \Foo\Bar();
}
----
<?php

namespace Humbug\Foo;

class Bar
{
}
namespace Humbug;

new \Humbug\Foo\Bar();

PHP
    ],

    'New statement call of a whitelisted namespaced class' => [
        'whitelist' => ['Foo\Bar'],
        'registered-classes' => [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    new Foo\Bar();
}
----
<?php

namespace Humbug\Foo;

class Bar
{
}
\class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
namespace Humbug;

new \Humbug\Foo\Bar();

PHP
    ],

    'FQ new statement call of a whitelisted namespaced class' => [
        'whitelist' => ['Foo\Bar'],
        'registered-classes' => [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    new \Foo\Bar();
}
----
<?php

namespace Humbug\Foo;

class Bar
{
}
\class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
namespace Humbug;

new \Humbug\Foo\Bar();

PHP
    ],
];
