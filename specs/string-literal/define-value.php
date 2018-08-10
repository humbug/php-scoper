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
        'title' => 'String literal assigned as a constant declared with `define()`',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'FQCN string argument' => <<<'PHP'
<?php

define('X', 'Symfony\\Component\\Yaml\\Ya_1');
define('X', '\\Symfony\\Component\\Yaml\\Ya_1');
define('X', 'Humbug\\Symfony\\Component\\Yaml\\Ya_1');
define('X', '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1');

----
<?php

namespace Humbug;

\define('X', 'Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\define('X', 'Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\define('X', 'Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\define('X', 'Humbug\\Symfony\\Component\\Yaml\\Ya_1');

PHP
    ,
];
