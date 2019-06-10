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
        'title' => 'Nowdoc',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => false,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => false,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'string' => <<<'PHP'
<?php

$x = '
<?php

use Acme\Foo;

';

----
<?php

namespace Humbug;

$x = '
<?php

use Acme\\Foo;

';

PHP
    ,

    'Nowdoc' => <<<'PHP'
<?php

$x = <<<'PHP_NOWDOC'
<?php

use Acme\Foo;

PHP_NOWDOC;

$y = <<<'PHP_NOWDOC'
<?php

use Acme\Foo;
PHP_NOWDOC;

----
<?php

namespace Humbug;

$x = <<<'PHP_NOWDOC'
<?php

namespace Humbug;

use Humbug\Acme\Foo;

PHP_NOWDOC;
$y = <<<'PHP_NOWDOC'
<?php

namespace Humbug;

use Humbug\Acme\Foo;
PHP_NOWDOC
;

PHP
    ,

    'Nowdoc with invalid PHP' => <<<'PHP'
<?php

$x = <<<'PHP_NOWDOC'
Not.php
PHP_NOWDOC;

----
<?php

namespace Humbug;

$x = <<<'PHP_NOWDOC'
Not.php
PHP_NOWDOC
;

PHP
    ,

    'Partial PHP nowdoc' => <<<'PHP'
<?php

$x = <<<'PHP_NOWDOC'
use Acme\Foo;
PHP_NOWDOC;

----
<?php

namespace Humbug;

$x = <<<'PHP_NOWDOC'
use Acme\Foo;
PHP_NOWDOC
;

PHP
    ,

    'Empty nowdoc' => <<<'PHP'
<?php

$x = <<<'PHP_NOWDOC'
PHP_NOWDOC;

----
<?php

namespace Humbug;

$x = <<<'PHP_NOWDOC'
PHP_NOWDOC
;

PHP
    ,

    'Heredoc' => <<<'PHP'
<?php

$x = <<<PHP_HEREDOC
<?php

use Acme\Foo;

PHP_HEREDOC;

----
<?php

namespace Humbug;

$x = <<<PHP_HEREDOC
<?php

use Acme\\Foo;

PHP_HEREDOC
;

PHP
    ,
];
