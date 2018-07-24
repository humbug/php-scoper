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
        'title' => 'Global constant declaration in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Constants declaration in the global namespace' => [
        'payload' => <<<'PHP'
<?php

const FOO_CONST = foo();
const X = 'x', Y = '';
define('BAR_CONST', foo());
define('Acme\BAR_CONST', foo());
define(FOO_CONST, foo());
define(\FOO_CONST, foo());
define(\Acme\BAR_CONST, foo());
----
<?php

namespace Humbug;

const FOO_CONST = \Humbug\foo();
const X = 'x', Y = '';
\define('Humbug\\BAR_CONST', \Humbug\foo());
\define('Humbug\\Acme\\BAR_CONST', \Humbug\foo());
\define(\Humbug\FOO_CONST, \Humbug\foo());
\define(\Humbug\FOO_CONST, \Humbug\foo());
\define(\Humbug\Acme\BAR_CONST, \Humbug\foo());

PHP
    ],

    'Constant declarations in the global namespace which is whitelisted' => [
        'whitelist' => ['*'],
        'payload' => <<<'PHP'
<?php

const FOO_CONST = foo();
const X = 'x', Y = '';
define('BAR_CONST', foo());
define('Acme\BAR_CONST', foo());
define(FOO_CONST, foo());
define(\FOO_CONST, foo());
define(\Acme\BAR_CONST, foo());
----
<?php

namespace {
    const FOO_CONST = \foo();
    const X = 'x', Y = '';
    \define('BAR_CONST', \foo());
    \define('Acme\\BAR_CONST', \foo());
    \define(\FOO_CONST, \foo());
    \define(\FOO_CONST, \foo());
    \define(\Acme\BAR_CONST, \foo());
}

PHP
    ],

    'Whitelisted constant declarations in the global namespace' => [
        'whitelist' => ['FOO_CONST', 'BAR_CONST', 'Acme\BAR_CONST', 'BAZ*', 'Emca\BAZ*'],
        'payload' => <<<'PHP'
<?php

const FOO_CONST = foo();
const X = 'x', Y = '';
define('BAR_CONST', foo());
define('BAP_CONST', foo());
define('Acme\BAR_CONST', foo());
define(FOO_CONST, foo());
define(\FOO_CONST, foo());
define(\Acme\BAR_CONST, foo());

define('BAZ', 'baz');
define('Emca\BAZ', 'baz');
----
<?php

namespace Humbug;

\define('FOO_CONST', \Humbug\foo());
const X = 'x', Y = '';
\define('BAR_CONST', \Humbug\foo());
\define('Humbug\\BAP_CONST', \Humbug\foo());
\define('Acme\\BAR_CONST', \Humbug\foo());
\define(\FOO_CONST, \Humbug\foo());
\define(\FOO_CONST, \Humbug\foo());
\define(\Acme\BAR_CONST, \Humbug\foo());
\define('BAZ', 'baz');
\define('Emca\\BAZ', 'baz');

PHP
    ],

    'Whitelisted grouped constants declaration in the global namespace' => [
        'whitelist' => ['X'],
        'payload' => <<<'PHP'
<?php

const X = 'x', Y = '';
----
PHP
    ],

    'Constants declaration in a namespace' => [
        'payload' => <<<'PHP'
<?php

namespace Acme;

const FOO_CONST = foo();
const X = 'x', Y = '';
define('BAR_CONST', foo());
define('Acme\BAR_CONST', foo());
define(FOO_CONST, foo());
define(\FOO_CONST, foo());
define(\Acme\FOO_CONST, foo());
----
<?php

namespace Humbug\Acme;

const FOO_CONST = foo();
const X = 'x', Y = '';
\define('Humbug\\BAR_CONST', foo());
\define('Humbug\\Acme\\BAR_CONST', foo());
\define(FOO_CONST, foo());
\define(\Humbug\FOO_CONST, foo());
\define(\Humbug\Acme\FOO_CONST, foo());

PHP
    ],

    'Constants declaration in a whitelisted namespace' => [
        'whitelist' => ['Acme\*'],
        'payload' => <<<'PHP'
<?php

namespace Acme;

const FOO_CONST = foo();
const X = 'x', Y = '';
define('BAR_CONST', foo());
define('Acme\BAR_CONST', foo());
define(FOO_CONST, foo());
define(\FOO_CONST, foo());
define(\Acme\BAR_CONST, foo());
----
<?php

namespace Acme;

const FOO_CONST = foo();
const X = 'x', Y = '';
\define('Humbug\\BAR_CONST', foo());
\define('Acme\\BAR_CONST', foo());
\define(FOO_CONST, foo());
\define(\Humbug\FOO_CONST, foo());
\define(\Acme\BAR_CONST, foo());

PHP
    ],

    'Whitelisted constants declaration in a namespace' => [
        'whitelist' => ['Acme\BAR_CONST'],
        'payload' => <<<'PHP'
<?php

namespace Acme;

const FOO_CONST = foo();
const X = 'x', Y = '';
define('BAR_CONST', foo());
define('Acme\BAR_CONST', foo());
define(FOO_CONST, foo());
define(\FOO_CONST, foo());
define(\Acme\BAR_CONST, foo());
----
<?php

namespace Humbug\Acme;

const FOO_CONST = foo();
const X = 'x', Y = '';
\define('Humbug\\BAR_CONST', foo());
\define('Acme\\BAR_CONST', foo());
\define(FOO_CONST, foo());
\define(\Humbug\FOO_CONST, foo());
\define(\Acme\BAR_CONST, foo());

PHP
    ],
];
