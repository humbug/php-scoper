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
        'title' => 'single-part class constant references in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'single-part' => <<<'PHP'
<?php

namespace X;

Foo::MAIN_CONST;
----
<?php

namespace Humbug\X;

Foo::MAIN_CONST;

PHP
    ,

    'FQ single-part' => <<<'PHP'
<?php

namespace X;

\Foo::MAIN_CONST;
----
<?php

namespace Humbug\X;

\Humbug\Foo::MAIN_CONST;

PHP
    ,
];
