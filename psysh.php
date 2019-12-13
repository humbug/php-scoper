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
        'title' => 'Internal symbols',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Empty file' => <<<'PHP'
<?php

namespace Psy\Reflection;

class ReflectionClassConstant implements \Reflector
{
    public static function create($class, $name)
    {
        if (\class_exists('\\ReflectionClassConstant')) {
            return new \ReflectionClassConstant($class, $name);
        }
        return new self($class, $name);
    }
}

----
<?php

namespace Humbug\Psy\Reflection;

class ReflectionClassConstant implements \Reflector
{
    public static function create($class, $name)
    {
        if (\class_exists('Humbug\\ReflectionClassConstant')) {
            return new \ReflectionClassConstant($class, $name);
        }
        return new self($class, $name);
    }
}

PHP
    ,
];
