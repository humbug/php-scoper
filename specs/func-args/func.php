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
        'title' => 'Functions for which the arguments should not be affected',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'FQCN string argument: do not touch the arguments' => <<<'PHP'
<?php

var_dump('Symfony\\Component\\Yaml\\Yaml');
var_dump('\\Symfony\\Component\\Yaml\\Yaml');
var_dump('Humbug\\Symfony\\Component\\Yaml\\Yaml');
var_dump('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

----
<?php

\var_dump('Symfony\\Component\\Yaml\\Yaml');
\var_dump('\\Symfony\\Component\\Yaml\\Yaml');
\var_dump('Humbug\\Symfony\\Component\\Yaml\\Yaml');
\var_dump('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

PHP
    ,

    'FQCN string argument on whitelisted class: do not touch the arguments' => [
        'whitelist' => ['Symfony\Component\Yaml\Yaml'],
        'payload' => <<<'PHP'
<?php

var_dump('Symfony\\Component\\Yaml\\Yaml');
var_dump('\\Symfony\\Component\\Yaml\\Yaml');
var_dump('Humbug\\Symfony\\Component\\Yaml\\Yaml');
var_dump('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

----
<?php

\var_dump('Symfony\\Component\\Yaml\\Yaml');
\var_dump('\\Symfony\\Component\\Yaml\\Yaml');
\var_dump('Humbug\\Symfony\\Component\\Yaml\\Yaml');
\var_dump('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

PHP
    ],

    'FQCN string argument formed by concatenated strings: do not touch the arguments' => <<<'PHP'
<?php

var_dump('Symfony\\Component' . '\\Yaml\\Yaml');
var_dump('\\Symfony\\Component' . '\\Yaml\\Yaml');

----
<?php

\var_dump('Symfony\\Component' . '\\Yaml\\Yaml');
\var_dump('\\Symfony\\Component' . '\\Yaml\\Yaml');

PHP
    ,

    'FQC constant call: prefix the arguments as are not string arguments' => <<<'PHP'
<?php

var_dump(Symfony\Component\Yaml\Yaml::class);
var_dump(\Symfony\Component\Yaml\Yaml::class);
var_dump(Humbug\Symfony\Component\Yaml\Yaml::class);
var_dump(\Humbug\Symfony\Component\Yaml\Yaml::class);

----
<?php

\var_dump(\Humbug\Symfony\Component\Yaml\Yaml::class);
\var_dump(\Humbug\Symfony\Component\Yaml\Yaml::class);
\var_dump(\Humbug\Symfony\Component\Yaml\Yaml::class);
\var_dump(\Humbug\Symfony\Component\Yaml\Yaml::class);

PHP
    ,
];
