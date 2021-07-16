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
        'title' => 'Aliased use statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'expose-global-constants' => true,
        'expose-global-classes' => false,
        'expose-global-functions' => true,
        'excluded-constants' => [],
        'excluded-classes' => [],
        'excluded-functions' => [],
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Use statement of a class belonging to the global scope' => <<<'PHP'
<?php

class Foo {}

use Foo as A;

----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo as A;

PHP
    ,

    'FQ use statement of a class belonging to the global scope' => <<<'PHP'
<?php

class Foo {}

use \Foo as A;

----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo as A;

PHP
    ,

    'Use statement of an internal class belonging to the global scope' => <<<'PHP'
<?php

use ArrayIterator as A;

----
<?php

namespace Humbug;

use ArrayIterator as A;

PHP
    ,

    'FQ use statement of an internal class belonging to the global scope' => <<<'PHP'
<?php

use \ArrayIterator as A;

----
<?php

namespace Humbug;

use ArrayIterator as A;

PHP
    ,

    'Use statement of two-level class' => <<<'PHP'
<?php

use Foo\Bar as A;

----
<?php

namespace Humbug;

use Humbug\Foo\Bar as A;

PHP
    ,

    'Use statement of two-level class which has been already prefixed' => <<<'PHP'
<?php

use Humbug\Foo\Bar as A;

----
<?php

namespace Humbug;

use Humbug\Foo\Bar as A;

PHP
    ,

    'Use statement of two-level class which has been whitelisted' => [
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

use Foo\Bar as A;

----
<?php

namespace Humbug;

use Humbug\Foo\Bar as A;

PHP
    ],
];
