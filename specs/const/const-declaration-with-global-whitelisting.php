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
        'title' => 'Global constant declaration & usage in the global scope with the global constants whitelisted',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],

        'expose-global-constants' => true,
        'expose-global-classes' => false,
        'expose-global-functions' => false,
        'expose-namespaces' => [],
        'expose-constants' => [],
        'expose-classes' => [],
        'expose-functions' => [],

        'exclude-namespaces' => [],
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],

        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Constants declaration in the global namespace' => [
        'payload' => <<<'PHP'
<?php

const FOO_CONST = foo();
define('BAR_CONST', foo());
define('Acme\BAR_CONST', foo());
define(FOO_CONST, foo());
define(\FOO_CONST, foo());
define(\Acme\BAR_CONST, foo());
----
<?php

namespace Humbug;

\define('FOO_CONST', foo());
\define('BAR_CONST', foo());
\define('Humbug\\Acme\\BAR_CONST', foo());
\define(\FOO_CONST, foo());
\define(\FOO_CONST, foo());
\define(\Humbug\Acme\BAR_CONST, foo());

PHP
    ],

    'Constants declaration in the global namespace which is whitelisted' => [
        'whitelist' => ['*'],
        'payload' => <<<'PHP'
<?php

const FOO_CONST = foo();
define('BAR_CONST', foo());
define('Acme\BAR_CONST', foo());
define(FOO_CONST, foo());
define(\FOO_CONST, foo());
define(\Acme\BAR_CONST, foo());
----
<?php

namespace {
    \define('FOO_CONST', \foo());
    \define('BAR_CONST', \foo());
    \define('Acme\\BAR_CONST', \foo());
    \define(\FOO_CONST, \foo());
    \define(\FOO_CONST, \foo());
    \define(\Acme\BAR_CONST, \foo());
}

PHP
    ],

    'Whitelisted constants declaration in the global namespace' => [
        'whitelist' => ['FOO_CONST', 'BAR_CONST', 'Acme\BAR_CONST'],
        'payload' => <<<'PHP'
<?php

const FOO_CONST = foo();
define('BAR_CONST', foo());
define('Acme\BAR_CONST', foo());
define(FOO_CONST, foo());
define(\FOO_CONST, foo());
define(\Acme\BAR_CONST, foo());
----
<?php

namespace Humbug;

\define('FOO_CONST', foo());
\define('BAR_CONST', foo());
\define('Acme\\BAR_CONST', foo());
\define(\FOO_CONST, foo());
\define(\FOO_CONST, foo());
\define(\Acme\BAR_CONST, foo());

PHP
    ],

    'Constants declaration in a namespace' => [
        'payload' => <<<'PHP'
<?php

namespace Acme;

const FOO_CONST = foo();
define('BAR_CONST', foo());
define('Acme\BAR_CONST', foo());
define(FOO_CONST, foo());
define(\FOO_CONST, foo());
define(\Acme\FOO_CONST, foo());
----
<?php

namespace Humbug\Acme;

const FOO_CONST = foo();
\define('BAR_CONST', foo());
\define('Humbug\\Acme\\BAR_CONST', foo());
\define(FOO_CONST, foo());
\define(\FOO_CONST, foo());
\define(\Humbug\Acme\FOO_CONST, foo());

PHP
    ],

    'Constants declaration in a whitelisted namespace' => [
        'whitelist' => ['Acme\*'],
        'payload' => <<<'PHP'
<?php

namespace Acme;

const FOO_CONST = foo();
define('BAR_CONST', foo());
define('Acme\BAR_CONST', foo());
define(FOO_CONST, foo());
define(\FOO_CONST, foo());
define(\Acme\BAR_CONST, foo());
----
<?php

namespace Acme;

const FOO_CONST = foo();
\define('BAR_CONST', foo());
\define('Acme\\BAR_CONST', foo());
\define(FOO_CONST, foo());
\define(\FOO_CONST, foo());
\define(\Acme\BAR_CONST, foo());

PHP
    ],

    'Whitelisted constants declaration in a namespace' => [
        'whitelist' => ['Acme\BAR_CONST'],
        'payload' => <<<'PHP'
<?php

namespace Acme;

const FOO_CONST = foo();
define('BAR_CONST', foo());
define('Acme\BAR_CONST', foo());
define(FOO_CONST, foo());
define(\FOO_CONST, foo());
define(\Acme\BAR_CONST, foo());
----
<?php

namespace Humbug\Acme;

const FOO_CONST = foo();
\define('BAR_CONST', foo());
\define('Acme\\BAR_CONST', foo());
\define(FOO_CONST, foo());
\define(\FOO_CONST, foo());
\define(\Acme\BAR_CONST, foo());

PHP
    ],
];
