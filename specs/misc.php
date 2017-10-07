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
        'title' => 'Miscellaneous',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'Empty file: do nothing' => <<<'PHP'
<?php

----
<?php



PHP
    ,

    'Empty php file with a declare statement: do nothing' => <<<'PHP'
<?php declare(strict_types=1);

----
<?php

declare (strict_types=1);

PHP
    ,

    [
        'spec' => <<<'SPEC'
When resolving fully qualified class names, keep in mind that classes are case insensitive in PHP.
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class X {}
}

namespace {
    use FOO\x as Y;
    use Foo\stdClass;
    
    var_dump(new y());
    var_dump(new STDCLASS());
}
----
<?php

namespace Humbug\Foo {
    class X
    {
    }
}
namespace {
    use Humbug\FOO\x as Y;
    use Humbug\Foo\stdClass;
    \var_dump(new \Humbug\FOO\x());
    \var_dump(new \Humbug\Foo\stdClass());
}

PHP
    ],
];
