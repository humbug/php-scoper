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
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Cast variable' => <<<'PHP'
<?php

$x = new stdClass();

(bool) $x;
(int) $x;
(float) $x;
(array) $x;
(object) $x;
----
<?php

namespace Humbug;

$x = new \stdClass();
(bool) $x;
(int) $x;
(float) $x;
(array) $x;
(object) $x;

PHP
    ,
];
