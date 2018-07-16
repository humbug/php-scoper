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
        'whitelist-global-functions' => true,
    ],

    'FQCN string argument' => <<<'PHP'
<?php

define('X', 'Symfony\\Component\\Yaml\\Yaml');
define('X', '\\Symfony\\Component\\Yaml\\Yaml');
define('X', 'Humbug\\Symfony\\Component\\Yaml\\Yaml');
define('X', '\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

----
<?php

namespace Humbug;

\define('X', 'Humbug\\Symfony\\Component\\Yaml\\Yaml');
\define('X', 'Humbug\\Symfony\\Component\\Yaml\\Yaml');
\define('X', 'Humbug\\Symfony\\Component\\Yaml\\Yaml');
\define('X', 'Humbug\\Symfony\\Component\\Yaml\\Yaml');

PHP
    ,
];
