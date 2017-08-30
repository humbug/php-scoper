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
    ],

    // Won't do anything here. This use statement could very well be for a class belonging to the
    // global namespace e.g. `\Closure` in which case it shouldn't do anyting or a legitimate use
    // statement of either a class registered in the global namespace or of a partial namespace
    // e.g. `Foo\Bar::DUMMY_CONST`.
    'one level' => <<<'PHP'
<?php

use Foo;

----
<?php

use Foo;

PHP
    ,

    // Won't do anything here. This use statement could very well be for a class belonging to the
    // global namespace e.g. `\Closure` in which case it shouldn't do anyting or a legitimate use
    // statement of either a class registered in the global namespace or of a partial namespace
    // e.g. `Foo\Bar::DUMMY_CONST`.
    'absolute one level' => <<<'PHP'
<?php

use \Foo;

----
<?php

use Foo;

PHP
    ,

    // See `scope.inc.php` for the built-in global whitelisted classes
    '(global) whitelisted - one level' => <<<'PHP'
<?php

use AppKernel;

----
<?php

use Humbug\AppKernel;

PHP
    ,

    // See `scope.inc.php` for the built-in global whitelisted classes
    '(global) whitelisted - absolute one level' => <<<'PHP'
<?php

use \AppKernel;

----
<?php

use Humbug\AppKernel;

PHP
    ,

    // See `scope.inc.php` for the built-in global whitelisted classes
    '(global) whitelisted + already prefixed - one level' => <<<'PHP'
<?php

use Humbug\AppKernel;

----
<?php

use Humbug\AppKernel;

PHP
    ,

    // See `scope.inc.php` for the built-in global whitelisted classes
    '(global) whitelisted + already prefixed - absolute one level' => <<<'PHP'
<?php

use \Humbug\AppKernel;

----
<?php

use Humbug\AppKernel;

PHP
    ,

    // Case: use statement with global whitelisted class with a whitelisted class
    // this should not happen as there is a validation upstream to prevent that
    // scenario. Indeed a class form the global namespace is whitelisted by default
    // and as such allowing to whitelist it would only make things confusing.

    'two levels' => <<<'PHP'
<?php

use Foo\Bar;

----
<?php

use Humbug\Foo\Bar;

PHP
    ,

    'absolute two levels' => <<<'PHP'
<?php

use \Foo\Bar;

----
<?php

use Humbug\Foo\Bar;

PHP
    ,

    'already prefixed two levels' => <<<'PHP'
<?php

use Humbug\Foo\Bar;

----
<?php

use Humbug\Foo\Bar;

PHP
    ,

    'already prefixed absolute two levels' => <<<'PHP'
<?php

use Humbug\Foo\Bar;

----
<?php

use Humbug\Foo\Bar;

PHP
    ,

    // The use statement is still prefixed as usual. The usages of that statement
    // will however be transformed into FQC
    'whitelisted two levels' => [
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

use Foo\Bar;

----
<?php

use Humbug\Foo\Bar;

PHP
    ],

    // The use statement is still prefixed as usual. The usages of that statement
    // will however be transformed into FQC
    'whitelisted absolute two levels' => [
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

use \Foo\Bar;

----
<?php

use Humbug\Foo\Bar;

PHP
    ],
];
