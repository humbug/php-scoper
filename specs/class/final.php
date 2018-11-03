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
        'title' => 'Final class declaration',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Declaration in the global namespace' => <<<'PHP'
<?php

final class A {}
----
<?php

namespace Humbug;

final class A
{
}

PHP
    ,

    'Declaration in the global namespace with global classes whitelisted' => [
        'whitelist-global-classes' => true,
        'registered-classes' => [
            ['A', 'Humbug\A'],
        ],
        'payload' => <<<'PHP'
<?php

final class A {}
----
<?php

namespace Humbug;

final class A
{
}
\class_alias('Humbug\\A', 'A', \false);

PHP
    ],

    'Declaration in a namespace' => <<<'PHP'
<?php

namespace Foo;

final class A {}
----
<?php

namespace Humbug\Foo;

final class A
{
}

PHP
    ,

    'Declaration in a namespace with global classes whitelisted' => [
        'whitelist-global-classes' => true,
        'payload' => <<<'PHP'
<?php

namespace Foo;

final class A {}
----
<?php

namespace Humbug\Foo;

final class A
{
}

PHP
    ],

    'Declaration of a whitelisted final class' => [
        'whitelist' => ['Foo\A'],
        'registered-classes' => [
            ['Foo\A', 'Humbug\Foo\A'],
        ],
        'payload' => <<<'PHP'
<?php

namespace Foo;

final class A {}
----
<?php

namespace Humbug\Foo;

final class A
{
}
\class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);

PHP
        ],

    'Multiple declarations in different namespaces' => <<<'PHP'
<?php

namespace X {
    final class A {}
}

namespace Y {
    final class B {}
}

namespace Z {
    final class C {}
}
----
<?php

namespace Humbug\X;

final class A
{
}
namespace Humbug\Y;

final class B
{
}
namespace Humbug\Z;

final class C
{
}

PHP
    ,
];
