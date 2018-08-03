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
        'title' => 'Use statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Use statement of a class belonging to the global scope' => <<<'PHP'
<?php

class Foo {}

use Foo;

----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo;

PHP
    ,

    'FQ use statement of a class belonging to the global scope' => <<<'PHP'
<?php

class Foo {}

use \Foo;

----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo;

PHP
    ,

    'Use statement of an internal class belonging to the global scope' => <<<'PHP'
<?php

use ArrayIterator;

----
<?php

namespace Humbug;

use ArrayIterator;

PHP
    ,

    'Use statement of an internal class belonging to the global scope' => <<<'PHP'
<?php

use \ArrayIterator;

----
<?php

namespace Humbug;

use ArrayIterator;

PHP
    ,

    'Use statement of a non existent class belonging to the global scope' => <<<'PHP'
<?php

use Unknown;

----
<?php

namespace Humbug;

use Humbug\Unknown;

PHP
    ,

    'Use statement of a whitelisted class belonging to the global scope' => [
        'whitelist' => ['Foo'],
        'registered-classes' => [
            ['Foo', 'Humbug\Foo'],
        ],
        'payload' => <<<'PHP'
<?php

class Foo {}

use Foo;

----
<?php

namespace Humbug;

class Foo
{
}
\class_alias('Humbug\\Foo', 'Foo', \false);
use Humbug\Foo;

PHP
    ],

    'Use statement of a class belonging to the global scope which has been whitelisted' => [
        'whitelist' => ['\*'],
        'payload' => <<<'PHP'
<?php

class Foo {}

use Foo;

----
<?php

namespace {
    class Foo
    {
    }
    use Foo;
}

PHP
    ],

    'Use statement of a whitelisted class belonging to the global scope which has been whitelisted' => [
        'whitelist' => ['Foo', '\*'],
        'registered-classes' => [
            ['Foo', 'Humbug\Foo'],
        ],
        'payload' => <<<'PHP'
<?php

class Foo {}

use Foo;

----
<?php

namespace {
    class Foo
    {
    }
    use Foo;
}

PHP
    ],

    'Use statement of two-level class' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    use Foo\Bar;
}

----
<?php

namespace Humbug\Foo;

class Bar
{
}
namespace Humbug;

use Humbug\Foo\Bar;

PHP
    ,

    'Already prefixed use statement of two-level class' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    use Humbug\Foo\Bar;
}

----
<?php

namespace Humbug\Foo;

class Bar
{
}
namespace Humbug;

use Humbug\Foo\Bar;

PHP
    ,

    'Use statement of two-level class which has been whitelisted' => [
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
    use Foo\Bar;
}

----
<?php

namespace Humbug\Foo;

class Bar
{
}
\class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
namespace Humbug;

use Humbug\Foo\Bar;

PHP
    ],

    'Use statement of two-level class belonging to a whitelisted namespace' => [
        'whitelist' => ['Foo\*'],
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    use Foo\Bar;
}

----
<?php

namespace Foo;

class Bar
{
}
namespace Humbug;

use Foo\Bar;

PHP
    ],

    'Use statement of whitelisted two-level class belonging to a whitelisted namespace' => [
        'whitelist' => ['Foo', 'Foo\*'],
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    use Foo\Bar;
}

----
<?php

namespace Foo;

class Bar
{
}
namespace Humbug;

use Foo\Bar;

PHP
    ],
];
