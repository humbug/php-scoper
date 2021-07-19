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
        'maxPhpVersion' => 70499,
        'title' => 'Match',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'exclude-namespaces' => [],
        'expose-global-constants' => false,
        'expose-global-classes' => false,
        'expose-global-functions' => false,
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],
        'registered-classes' => [],
        'registered-functions' => [],
    ],
    'match' => <<<'PHP'
<?php declare(strict_types=1);
    
namespace Acme {
    use Acme\Foo\Match;

    class Foo implements Match {}
}

namespace Acme\Foo {
    interface Match {}
}
    
----
<?php

declare (strict_types=1);
namespace Humbug\Acme;

use Humbug\Acme\Foo\Match;
class Foo implements Match
{
}
namespace Humbug\Acme\Foo;

interface Match
{
}

PHP
];
