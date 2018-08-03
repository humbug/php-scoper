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
        'title' => 'String literal used as a new statement argument',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'FQCN string argument' => <<<'PHP'
<?php

new X('Yaml', ['Yaml']);
new X('\\Yaml', ['\\Yaml']);
new X('Closure', ['Closure']);
new X('\\Closure', ['\\Closure']);
new X('Symfony\\Component\\Yaml\\Yaml', ['Symfony\\Component\\Yaml\\Yaml']);
new X('\\Symfony\\Component\\Yaml\\Yaml', ['\\Symfony\\Component\\Yaml\\Yaml']);
new X('Humbug\\Symfony\\Component\\Yaml\\Yaml', ['Humbug\\Symfony\\Component\\Yaml\\Yaml']);
new X('\\Humbug\\Symfony\\Component\\Yaml\\Yaml', ['\\Humbug\\Symfony\\Component\\Yaml\\Yaml']);

----
<?php

namespace Humbug;

new \Humbug\X('Yaml', ['Yaml']);
new \Humbug\X('\\Yaml', ['\\Yaml']);
new \Humbug\X('Closure', ['Closure']);
new \Humbug\X('\\Closure', ['\\Closure']);
new \Humbug\X('Humbug\\Symfony\\Component\\Yaml\\Yaml', ['Humbug\\Symfony\\Component\\Yaml\\Yaml']);
new \Humbug\X('Humbug\\Symfony\\Component\\Yaml\\Yaml', ['Humbug\\Symfony\\Component\\Yaml\\Yaml']);
new \Humbug\X('Humbug\\Symfony\\Component\\Yaml\\Yaml', ['Humbug\\Symfony\\Component\\Yaml\\Yaml']);
new \Humbug\X('Humbug\\Symfony\\Component\\Yaml\\Yaml', ['Humbug\\Symfony\\Component\\Yaml\\Yaml']);

PHP
    ,

    'FQCN string argument on whitelisted class' => [
        'whitelist' => ['Symfony\Component\Yaml\Yaml'],
        'payload' => <<<'PHP'
<?php

new X('Symfony\\Component\\Yaml\\Yamll', ['Symfony\\Component\\Yaml\\Yamll']);
new X('Symfony\\Component\\Yaml\\Yaml', ['Symfony\\Component\\Yaml\\Yaml']);
new X('\\Symfony\\Component\\Yaml\\Yaml', ['\\Symfony\\Component\\Yaml\\Yaml']);
new X('Humbug\\Symfony\\Component\\Yaml\\Yaml', ['Humbug\\Symfony\\Component\\Yaml\\Yaml']);
new X('\\Humbug\\Symfony\\Component\\Yaml\\Yaml', ['\\Humbug\\Symfony\\Component\\Yaml\\Yaml']);

----
<?php

namespace Humbug;

new \Humbug\X('Humbug\\Symfony\\Component\\Yaml\\Yamll', ['Humbug\\Symfony\\Component\\Yaml\\Yamll']);
new \Humbug\X('Humbug\\Symfony\\Component\\Yaml\\Yaml', ['Humbug\\Symfony\\Component\\Yaml\\Yaml']);
new \Humbug\X('Humbug\\Symfony\\Component\\Yaml\\Yaml', ['Humbug\\Symfony\\Component\\Yaml\\Yaml']);
new \Humbug\X('Humbug\\Symfony\\Component\\Yaml\\Yaml', ['Humbug\\Symfony\\Component\\Yaml\\Yaml']);
new \Humbug\X('Humbug\\Symfony\\Component\\Yaml\\Yaml', ['Humbug\\Symfony\\Component\\Yaml\\Yaml']);

PHP
    ],

    'FQCN string argument on classes belonging to a whitelisted namespace' => [
        'whitelist' => ['Symfony\Component\*'],
        'payload' => <<<'PHP'
<?php

new X('Symfony\\Yaml', ['Symfony\\Yaml']);
new X('Symfony\\Component\\Yaml\\Yaml', ['Symfony\\Component\\Yaml\\Yaml']);
new X('\\Symfony\\Component\\Yaml\\Yaml', ['\\Symfony\\Component\\Yaml\\Yaml']);
new X('Humbug\\Symfony\\Component\\Yaml\\Yaml', ['Humbug\\Symfony\\Component\\Yaml\\Yaml']);
new X('\\Humbug\\Symfony\\Component\\Yaml\\Yaml', ['\\Humbug\\Symfony\\Component\\Yaml\\Yaml']);

----
<?php

namespace Humbug;

new \Humbug\X('Humbug\\Symfony\\Yaml', ['Humbug\\Symfony\\Yaml']);
new \Humbug\X('Symfony\\Component\\Yaml\\Yaml', ['Symfony\\Component\\Yaml\\Yaml']);
new \Humbug\X('\\Symfony\\Component\\Yaml\\Yaml', ['\\Symfony\\Component\\Yaml\\Yaml']);
new \Humbug\X('Humbug\\Symfony\\Component\\Yaml\\Yaml', ['Humbug\\Symfony\\Component\\Yaml\\Yaml']);
new \Humbug\X('Humbug\\Symfony\\Component\\Yaml\\Yaml', ['Humbug\\Symfony\\Component\\Yaml\\Yaml']);

PHP
    ],

    'FQCN string argument formed by concatenated strings' => <<<'PHP'
<?php

new X('Symfony\\Component' . '\\Yaml\\Yaml', ['Symfony\\Component' . '\\Yaml\\Yaml']);
new X('\\Symfony\\Component' . '\\Yaml\\Yaml', ['\\Symfony\\Component' . '\\Yaml\\Yaml']);

----
<?php

namespace Humbug;

new \Humbug\X('Symfony\\Component' . '\\Yaml\\Yaml', ['Symfony\\Component' . '\\Yaml\\Yaml']);
new \Humbug\X('\\Symfony\\Component' . '\\Yaml\\Yaml', ['\\Symfony\\Component' . '\\Yaml\\Yaml']);

PHP
    ,

    'FQC constant call' => <<<'PHP'
<?php

namespace Symfony\Component\Yaml {
    class Yaml {}
}

namespace {
    new X(Symfony\Component\Yaml\Yaml::class, [Symfony\Component\Yaml\Yaml::class]);
    new X(\Symfony\Component\Yaml\Yaml::class, [\Symfony\Component\Yaml\Yaml::class]);
    new X(Humbug\Symfony\Component\Yaml\Yaml::class, [Humbug\Symfony\Component\Yaml\Yaml::class]);
    new X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);
}
----
<?php

namespace Humbug\Symfony\Component\Yaml;

class Yaml
{
}
namespace Humbug;

new \Humbug\X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);
new \Humbug\X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);
new \Humbug\X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);
new \Humbug\X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);

PHP
    ,

    'FQC constant call on whitelisted class' => [
        'whitelist' => ['Symfony\Component\Yaml\Yaml'],
        'registered-classes' => [
            ['Symfony\Component\Yaml\Yaml', 'Humbug\Symfony\Component\Yaml\Yaml'],
        ],
        'payload' => <<<'PHP'
<?php

namespace Symfony\Component\Yaml {
    class Yaml {}
}

namespace {
    new X(Symfony\Component\Yaml\Yaml::class, [Symfony\Component\Yaml\Yaml::class]);
    new X(\Symfony\Component\Yaml\Yaml::class, [\Symfony\Component\Yaml\Yaml::class]);
    new X(Humbug\Symfony\Component\Yaml\Yaml::class, [Humbug\Symfony\Component\Yaml\Yaml::class]);
    new X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);
}
----
<?php

namespace Humbug\Symfony\Component\Yaml;

class Yaml
{
}
\class_alias('Humbug\\Symfony\\Component\\Yaml\\Yaml', 'Symfony\\Component\\Yaml\\Yaml', \false);
namespace Humbug;

new \Humbug\X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);
new \Humbug\X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);
new \Humbug\X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);
new \Humbug\X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);

PHP
    ],
];
