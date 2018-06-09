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
        'title' => 'String literal used as a function argument',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
    ],

    'FQCN string argument: transform into a FQCN and prefix it' => <<<'PHP'
<?php

foo('Symfony\\Component\\Yaml\\Yaml');
foo('\\Symfony\\Component\\Yaml\\Yaml');
foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
foo('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

----
<?php

namespace Humbug;

\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');

PHP
    ,

    'FQCN string argument on whitelisted class: transform into a FQCN' => [
        'whitelist' => ['Symfony\Component\Yaml\Yaml'],
        'payload' => <<<'PHP'
<?php

foo('Symfony\\Component\\Yaml\\Yaml');
foo('\\Symfony\\Component\\Yaml\\Yaml');
foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
foo('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

----
<?php

namespace Humbug;

\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');

PHP
    ],

    'FQCN string argument formed by concatenated strings: do nothing' => <<<'PHP'
<?php

foo('Symfony\\Component' . '\\Yaml\\Yaml');
foo('\\Symfony\\Component' . '\\Yaml\\Yaml');

----
<?php

namespace Humbug;

\Humbug\foo('Symfony\\Component' . '\\Yaml\\Yaml');
\Humbug\foo('\\Symfony\\Component' . '\\Yaml\\Yaml');

PHP
    ,

    'FQC constant call: transform into FQC call and prefix them' => <<<'PHP'
<?php

namespace Symfony\Component\Yaml {
    class Yaml {}
}

namespace {
    foo(Symfony\Component\Yaml\Yaml::class);
    foo(\Symfony\Component\Yaml\Yaml::class);
    foo(Humbug\Symfony\Component\Yaml\Yaml::class);
    foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
}
----
<?php

namespace Humbug\Symfony\Component\Yaml;

class Yaml
{
}
namespace Humbug;

\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);

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
    foo(Symfony\Component\Yaml\Yaml::class);
    foo(\Symfony\Component\Yaml\Yaml::class);
    foo(Humbug\Symfony\Component\Yaml\Yaml::class);
    foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
}
----
<?php

namespace Humbug\Symfony\Component\Yaml;

class Yaml
{
}
\class_alias('Humbug\\Symfony\\Component\\Yaml\\Yaml', 'Symfony\\Component\\Yaml\\Yaml', \false);
namespace Humbug;

\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);

PHP
    ],
];
