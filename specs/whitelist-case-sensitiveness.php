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
        'title' => 'Class declaration',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => false,
        'whitelist-global-functions' => true,
    ],

    'Class whitelists are case insensitive' => [
        'whitelist' => ['acme\foo'],
        'payload' => <<<'PHP'
<?php

namespace Acme;

class Foo {
    public function foo() {}
}
----
<?php

namespace Humbug\Acme;

class Foo
{
    public function foo()
    {
    }
}
\class_alias('Humbug\\Acme\\Foo', 'Acme\\Foo', \false);

PHP
    ],

    'Constant whitelists are case sensitive' => [
        'whitelist' => ['Acme\Foo', 'Acme\Bar'],
        'payload' => <<<'PHP'
<?php

namespace Acme;

const FOO = 'foo';
define('Acme\BAR', 'bar');
----
<?php

namespace Humbug\Acme;

const FOO = 'foo';
\define('Humbug\\Acme\\BAR', 'bar');

PHP
    ],

    'The namespace of constant whitelists are case insensitive' => [
        'whitelist' => ['acme\FOO', 'acme\BAR'],
        'payload' => <<<'PHP'
<?php

namespace Acme;

const FOO = 'foo';
define('Acme\BAR', 'bar');
----
<?php

namespace Humbug\Acme;

\define('Acme\\FOO', 'foo');
\define('Acme\\BAR', 'bar');

PHP
    ],

    'Namespace whitelists are case insensitive' => [
        'whitelist' => ['acme\*'],
        'payload' => <<<'PHP'
<?php

namespace Acme;

class Foo {
    public function foo() {}
}

const FOO = 'foo';
define('Acme\BAR', 'bar');

namespace Bar;

use Acme\Foo;
use const Acme\FOO;
use const Acme\BAR;

new Foo();
new \acmE\Foo();

FOO;
\acmE\FOO;

BAR;
\acmE\BAR;
----
<?php

namespace Acme;

class Foo
{
    public function foo()
    {
    }
}
const FOO = 'foo';
\define('Acme\\BAR', 'bar');
namespace Humbug\Bar;

use Acme\Foo;
use const Acme\FOO;
use const Acme\BAR;
new \Acme\Foo();
new \acmE\Foo();
\Acme\FOO;
\acmE\FOO;
\Acme\BAR;
\acmE\BAR;

PHP
    ],

    'Use statement whitelists are case insensitive' => [
        'whitelist' => ['acme\*'],
        'payload' => <<<'PHP'
<?php

use Acme\Foo;
use const Acme\FOO;
use const Acme\BAR;
----
<?php

namespace Humbug;

use Acme\Foo;
use const Acme\FOO;
use const Acme\BAR;

PHP
    ],
];
