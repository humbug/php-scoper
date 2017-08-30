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
        'title' => 'Functions for which the arguments are whitelisted',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'FQCN string argument' => <<<'PHP'
<?php

class_exists('Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Symfony\\Component\\Yaml\\Yaml');
class_exists('Humbug\\Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

----
<?php

class_exists('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

PHP
    ,

    'FQCN string argument on whitelisted class' => [
        'whitelist' => ['Symfony\Component\Yaml\Yaml'],
        'payload' => <<<'PHP'
<?php

class_exists('Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Symfony\\Component\\Yaml\\Yaml');
class_exists('Humbug\\Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

----
<?php

class_exists('\\Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

PHP
    ],

    // Nothing changes as we don't try to scope evaluated the values
    'FQCN string argument formed by concatened strings' => <<<'PHP'
<?php

class_exists('Symfony\\Component' . '\\Yaml\\Yaml');
class_exists('\\Symfony\\Component' . '\\Yaml\\Yaml');

----
<?php

class_exists('Symfony\\Component' . '\\Yaml\\Yaml');
class_exists('\\Symfony\\Component' . '\\Yaml\\Yaml');

PHP
    ,

    'FQC constant call' => <<<'PHP'
<?php

class_exists(Symfony\Component\Yaml\Yaml::class);
class_exists(\Symfony\Component\Yaml\Yaml::class);
class_exists(Humbug\Symfony\Component\Yaml\Yaml::class);
class_exists(\Humbug\Symfony\Component\Yaml\Yaml::class);

----
<?php

class_exists(\Humbug\Symfony\Component\Yaml\Yaml::class);
class_exists(\Humbug\Symfony\Component\Yaml\Yaml::class);
class_exists(\Humbug\Symfony\Component\Yaml\Yaml::class);
class_exists(\Humbug\Symfony\Component\Yaml\Yaml::class);

PHP
    ,

    'FQC constant call on whitelisted class' => [
        'whitelist' => ['Symfony\Component\Yaml\Yaml'],
        'payload' => <<<'PHP'
<?php

class_exists(Symfony\Component\Yaml\Yaml::class);
class_exists(\Symfony\Component\Yaml\Yaml::class);
class_exists(Humbug\Symfony\Component\Yaml\Yaml::class);
class_exists(\Humbug\Symfony\Component\Yaml\Yaml::class);

----
<?php

class_exists(\Symfony\Component\Yaml\Yaml::class);
class_exists(\Symfony\Component\Yaml\Yaml::class);
class_exists(\Humbug\Symfony\Component\Yaml\Yaml::class);
class_exists(\Humbug\Symfony\Component\Yaml\Yaml::class);

PHP
    ],
];
