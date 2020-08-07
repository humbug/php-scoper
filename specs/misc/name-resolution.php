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
        'title' => 'Name resolution',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Global namespace class & function with the same name' => [
        'registered-functions' => [
            ['foo', 'Humbug\foo'],
        ],
        'payload' => <<<'PHP'
<?php

use function foo;

class Acme {
    function __construct() {
        foo();
    }
}

----
<?php

namespace Humbug;

use function Humbug\foo;
class Acme
{
    function __construct()
    {
        \Humbug\foo();
    }
}

PHP
    ],

    'Namespaced class & function with the same name' => [
        'registered-functions' => [],
        'payload' => <<<'PHP'
<?php

namespace Acme;

use function Bar\foo;

class Acme {
    function __construct() {
        foo();
    }
}

----
<?php

namespace Humbug\Acme;

use function Humbug\Bar\foo;
class Acme
{
    function __construct()
    {
        \Humbug\Bar\foo();
    }
}

PHP
    ],

    'Internal class & function with the same name' => [
        'registered-functions' => [],
        'payload' => <<<'PHP'
<?php

namespace PHPUnit\Framework;

use function assert;

abstract class TestCase extends Assert {
    function __construct() {
        \assert();
    }
}

----
<?php

namespace Humbug\PHPUnit\Framework;

use function assert;
abstract class TestCase extends \Humbug\PHPUnit\Framework\Assert
{
    function __construct()
    {
        \assert();
    }
}

PHP
    ],
];
