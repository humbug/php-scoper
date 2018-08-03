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
        'title' => 'Class static property call of a class imported with a use statement in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Constant call on a class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace X {
    use Foo;
    
    Foo::$mainStaticProp;
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\X;

use Humbug\Foo;
\Humbug\Foo::$mainStaticProp;

PHP
    ,

    'FQ constant call on a class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
<?php

namespace {
    class Command {}
}

namespace X {
    use Command;
    
    \Command::$mainStaticProp;
}
----
<?php

namespace Humbug;

class Command
{
}
namespace Humbug\X;

use Humbug\Command;
\Humbug\Command::$mainStaticProp;

PHP
    ,

    'Constant call on a whitelisted class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
<?php

namespace X;

use Reflector;

Reflector::$mainStaticProp;
----
<?php

namespace Humbug\X;

use Reflector;
\Reflector::$mainStaticProp;

PHP
    ,

    'FQ constant call on a whitelisted class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
<?php

namespace X;

use Reflector;

\Reflector::$mainStaticProp;
----
<?php

namespace Humbug\X;

use Reflector;
\Reflector::$mainStaticProp;

PHP
    ,
];
