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
        'title' => 'Interface declaration',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'Declaration in the global namespace: do not do anything.' => <<<'PHP'
<?php

interface A extends C, D {
    public function a();
}
----
<?php

interface A extends C, D
{
    public function a();
}

PHP
    ,

    'Declaration in a namespace: prefix the namespace.' => <<<'PHP'
<?php

namespace Foo;

interface A extends C, D
{
    public function a();
}
----
<?php

namespace Humbug\Foo;

interface A extends C, D
{
    public function a();
}

PHP
    ,

    'Declaration of a whitelisted namespaced interface: do not prefix the namespace.' => [
        'whitelist' => ['Foo\A'],
        'payload' => <<<'PHP'
<?php

namespace Foo;

interface A extends C, D
{
    public function a();
}
----
<?php

namespace Foo;

interface A extends C, D
{
    public function a();
}

PHP
        ],

    'Multiple declarations in different namespaces: prefix each namespace.' => <<<'PHP'
<?php

namespace X {
    interface A extends D, E
    {
        public function a();
    }
}

namespace Y {
    interface B extends D, E
    {
        public function a();
    }
}

namespace Z {
    interface C extends D, E
    {
        public function a();
    }
}
----
<?php

namespace Humbug\X;

interface A extends D, E
{
    public function a();
}
namespace Humbug\Y;

interface B extends D, E
{
    public function a();
}
namespace Humbug\Z;

interface C extends D, E
{
    public function a();
}

PHP
    ,
];
