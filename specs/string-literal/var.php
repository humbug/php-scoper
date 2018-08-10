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
        'title' => 'String literal assigned to a variable',
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

$x = 'Yaml';
$x = '\\Yaml';
$x = 'Closure';
$x = '\\Closure';
$x = 'Symfony\\Component\\Yaml\\Ya_1';
$x = '\\Symfony\\Component\\Yaml\\Ya_1';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
$x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1';

----
<?php

namespace Humbug;

$x = 'Yaml';
$x = '\\Yaml';
$x = 'Closure';
$x = '\\Closure';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';

PHP
    ,

    'Invalid FQCN strings' => <<<'PHP'
<?php

$regex = '%if \(defined\(\$name = \'PhpParser\\\\\\\\Parser\\\\\\\\Tokens%';
$shortcuts = preg_split('{(\|)-?}', ltrim($shortcut, '-'));

----
<?php

namespace Humbug;

$regex = '%if \\(defined\\(\\$name = \'PhpParser\\\\\\\\Parser\\\\\\\\Tokens%';
$shortcuts = \preg_split('{(\\|)-?}', \ltrim($shortcut, '-'));

PHP
    ,

    'FQCN string argument on whitelisted class' => [
        'whitelist' => ['Symfony\Component\Yaml\Yaml'],
        'payload' => <<<'PHP'
<?php

$x = 'Symfony\\Component\\Yaml\\Ya_1l';
$x = 'Symfony\\Component\\Yaml\\Ya_1';
$x = '\\Symfony\\Component\\Yaml\\Ya_1';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
$x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1';

----
<?php

namespace Humbug;

$x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1l';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';

PHP
    ],

    'FQCN string argument on classes belonging to a whitelisted namespace' => [
        'whitelist' => ['Symfony\Component\*'],
        'payload' => <<<'PHP'
<?php

$x = 'Symfony\\Yaml';
$x = 'Symfony\\Component\\Yaml\\Ya_1';
$x = '\\Symfony\\Component\\Yaml\\Ya_1';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
$x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1';

----
<?php

namespace Humbug;

$x = 'Humbug\\Symfony\\Yaml';
$x = 'Symfony\\Component\\Yaml\\Ya_1';
$x = '\\Symfony\\Component\\Yaml\\Ya_1';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';

PHP
    ],

    'FQCN string argument formed by concatenated strings' => <<<'PHP'
<?php

$x = 'Symfony\\Component' . '\\Yaml\\Ya_1';
$x = '\\Symfony\\Component' . '\\Yaml\\Ya_1';

----
<?php

namespace Humbug;

$x = 'Symfony\\Component' . '\\Yaml\\Ya_1';
$x = '\\Symfony\\Component' . '\\Yaml\\Ya_1';

PHP
    ,

    'FQC constant call' => <<<'PHP'
<?php

namespace Symfony\Component\Yaml {
    class Yaml {}
}

namespace {
    $x = Symfony\Component\Yaml\Yaml::class;
    $x = \Symfony\Component\Yaml\Yaml::class;
    $x = Humbug\Symfony\Component\Yaml\Yaml::class;
    $x = \Humbug\Symfony\Component\Yaml\Yaml::class;
}
----
<?php

namespace Humbug\Symfony\Component\Yaml;

class Yaml
{
}
namespace Humbug;

$x = \Humbug\Symfony\Component\Yaml\Yaml::class;
$x = \Humbug\Symfony\Component\Yaml\Yaml::class;
$x = \Humbug\Symfony\Component\Yaml\Yaml::class;
$x = \Humbug\Symfony\Component\Yaml\Yaml::class;

PHP
    ,

    'FQC constant call on whitelisted class' => [
        'whitelist' => ['Symfony\Component\Yaml\Ya_1'],
        'registered-classes' => [
            ['Symfony\Component\Yaml\Ya_1', 'Humbug\Symfony\Component\Yaml\Ya_1'],
        ],
        'payload' => <<<'PHP'
<?php

namespace Symfony\Component\Yaml {
    class Ya_1 {}
}

namespace {
    $x = Symfony\Component\Yaml\Ya_1::class;
    $x = \Symfony\Component\Yaml\Ya_1::class;
    $x = Humbug\Symfony\Component\Yaml\Ya_1::class;
    $x = \Humbug\Symfony\Component\Yaml\Ya_1::class;
}
----
<?php

namespace Humbug\Symfony\Component\Yaml;

class Ya_1
{
}
\class_alias('Humbug\\Symfony\\Component\\Yaml\\Ya_1', 'Symfony\\Component\\Yaml\\Ya_1', \false);
namespace Humbug;

$x = \Humbug\Symfony\Component\Yaml\Ya_1::class;
$x = \Humbug\Symfony\Component\Yaml\Ya_1::class;
$x = \Humbug\Symfony\Component\Yaml\Ya_1::class;
$x = \Humbug\Symfony\Component\Yaml\Ya_1::class;

PHP
    ],
];
