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
        'title' => 'Catch expressions',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Catch an internal class' => <<<'PHP'
<?php

try {
    echo "foo";
} catch (Throwable $t) {
}
----
<?php

namespace Humbug;

try {
    echo "foo";
} catch (\Throwable $t) {
}

PHP
    ,

    'Catch an internal class in a namespace' => <<<'PHP'
<?php

namespace Acme;

try {
    echo "foo";
} catch (\Throwable $t) {
}
----
<?php

namespace Humbug\Acme;

try {
    echo "foo";
} catch (\Throwable $t) {
}

PHP
    ,

    'Catch a custom exception class' => <<<'PHP'
<?php

try {
    echo "foo";
} catch (FooException $t) {
}
----
<?php

namespace Humbug;

try {
    echo "foo";
} catch (\Humbug\FooException $t) {
}

PHP
    ,

    'Catch a whitelisted custom exception class' => [
        'whitelist' => ['FooException'],
        'payload' => <<<'PHP'
<?php

try {
    echo "foo";
} catch (FooException $t) {
}
----
<?php

namespace Humbug;

try {
    echo "foo";
} catch (\Humbug\FooException $t) {
}

PHP
    ],

    'Catch a custom exception class which belongs to the whitelisted root namespace' => [
        'whitelist' => ['\*'],
        'payload' => <<<'PHP'
<?php

try {
    echo "foo";
} catch (FooException $t) {
}
----
<?php

namespace {
    try {
        echo "foo";
    } catch (\FooException $t) {
    }
}

PHP
    ],

    'Catch a custom exception class in a namespace' => <<<'PHP'
<?php

namespace Acme;

try {
    echo "foo";
} catch (FooException $t) {
}
----
<?php

namespace Humbug\Acme;

try {
    echo "foo";
} catch (\Humbug\Acme\FooException $t) {
}

PHP
    ,

    'Catch a whitelisted custom exception class in a namespace' => [
        'whitelist' => ['Acme\FooException'],
        'payload' => <<<'PHP'
<?php

namespace Acme;

try {
    echo "foo";
} catch (FooException $t) {
}
----
<?php

namespace Humbug\Acme;

try {
    echo "foo";
} catch (\Humbug\Acme\FooException $t) {
}

PHP
    ],

    'Catch a custom exception class in a whitelisted namespace' => [
        'whitelist' => ['Acme\*'],
        'payload' => <<<'PHP'
<?php

namespace Acme;

try {
    echo "foo";
} catch (FooException $t) {
}
----
<?php

namespace Acme;

try {
    echo "foo";
} catch (\Acme\FooException $t) {
}

PHP
    ],

    'Catch an custom exception class in a namespace imported with a use statement' => <<<'PHP'
<?php

namespace Acme;

use X\FooException;

try {
    echo "foo";
} catch (FooException $t) {
}
----
<?php

namespace Humbug\Acme;

use Humbug\X\FooException;
try {
    echo "foo";
} catch (\Humbug\X\FooException $t) {
}

PHP
    ,

    'Multiple catch statement' => <<<'PHP'
<?php

namespace Acme;

use X\FooException;

try {
    echo "foo";
} catch (FooException | \Throwable $t) {
}
----
<?php

namespace Humbug\Acme;

use Humbug\X\FooException;
try {
    echo "foo";
} catch (\Humbug\X\FooException|\Throwable $t) {
}

PHP
    ,

    'catch with special keywords' => <<<'PHP'
<?php

namespace Acme;

try {
    echo "foo";
} catch (self | parent $t) {
}
----
<?php

namespace Humbug\Acme;

try {
    echo "foo";
} catch (self|parent $t) {
}

PHP
    ,
];
