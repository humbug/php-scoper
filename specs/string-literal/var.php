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
    ],

    'FQCN string argument: transform into a FQCN and prefix it' => <<<'PHP'
<?php

$x = 'Symfony\\Component\\Yaml\\Yaml';
$x = '\\Symfony\\Component\\Yaml\\Yaml';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';
$x = '\\Humbug\\Symfony\\Component\\Yaml\\Yaml';

----
<?php

namespace Humbug;

$x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';

PHP
    ,

    'Invalid FQCN strings: do not prefix them' => <<<'PHP'
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

    'FQCN string argument on whitelisted class: transform into a FQCN' => [
        'whitelist' => ['Symfony\Component\Yaml\Yaml'],
        'payload' => <<<'PHP'
<?php

$x = 'Symfony\\Component\\Yaml\\Yaml';
$x = '\\Symfony\\Component\\Yaml\\Yaml';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';
$x = '\\Humbug\\Symfony\\Component\\Yaml\\Yaml';

----
<?php

namespace Humbug;

$x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';
$x = 'Humbug\\Symfony\\Component\\Yaml\\Yaml';

PHP
    ],

    'FQCN string argument formed by concatenated strings: do nothing' => <<<'PHP'
<?php

$x = 'Symfony\\Component' . '\\Yaml\\Yaml';
$x = '\\Symfony\\Component' . '\\Yaml\\Yaml';

----
<?php

namespace Humbug;

$x = 'Symfony\\Component' . '\\Yaml\\Yaml';
$x = '\\Symfony\\Component' . '\\Yaml\\Yaml';

PHP
    ,

    'FQC constant call: transform into FQC call and prefix them' => <<<'PHP'
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

    'FQC constant call on whitelisted class: transform into FQC call' => [
        'whitelist' => ['Symfony\Component\Yaml\Yaml'],
        'payload' => <<<'PHP'
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
class_alias('Humbug\\Symfony\\Component\\Yaml\\Yaml', 'Symfony\\Component\\Yaml\\Yaml', \false);
namespace Humbug;

$x = \Humbug\Symfony\Component\Yaml\Yaml::class;
$x = \Humbug\Symfony\Component\Yaml\Yaml::class;
$x = \Humbug\Symfony\Component\Yaml\Yaml::class;
$x = \Humbug\Symfony\Component\Yaml\Yaml::class;

PHP
    ],
];
