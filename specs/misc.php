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
];
