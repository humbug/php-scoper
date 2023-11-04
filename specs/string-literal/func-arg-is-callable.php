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
        'title' => 'String literal used as a function argument of an is_callable call',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',

        'expose-global-constants' => false,
        'expose-global-classes' => false,
        'expose-global-functions' => false,
        'expose-namespaces' => [],
        'expose-constants' => [],
        'expose-classes' => [],
        'expose-functions' => [],

        'exclude-namespaces' => [],
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],

        'expected-recorded-classes' => [],
        'expected-recorded-functions' => [],
    ],

    'FQCN string argument' => <<<'PHP'
    <?php
    
    is_callable('sodiumCompatAutoloader');
    is_callable('Sodium\compatAutoloader');
    is_callable(['Swift', 'autoload']);
    is_callable(['\Swift', 'autoload']);
    is_callable(['Humbug\\Swift', 'autoload']);
    is_callable(['\\Humbug\\Swift', 'autoload']);
    is_callable(['\\Humbug\\Swift', 'autoload']);
    is_callable(['DateTime', 'autoload']);
    is_callable(['\\DateTime', 'autoload']);
    
    ----
    <?php
    
    namespace Humbug;
    
    \is_callable('Humbug\\sodiumCompatAutoloader');
    \is_callable('Humbug\\Sodium\\compatAutoloader');
    \is_callable(['Humbug\\Swift', 'autoload']);
    \is_callable(['Humbug\\Swift', 'autoload']);
    \is_callable(['Humbug\\Swift', 'autoload']);
    \is_callable(['Humbug\\Swift', 'autoload']);
    \is_callable(['Humbug\\Swift', 'autoload']);
    \is_callable(['DateTime', 'autoload']);
    \is_callable(['\\DateTime', 'autoload']);
    
    PHP,

    'FQCN string argument on exposed class' => [
        'expose-classes' => ['Symfony\Component\Yaml\Yaml', 'Swift'],
        'payload' => <<<'PHP'
        <?php
        
        is_callable(['Swift', 'autoload']);
        is_callable(['Humbug\\Swift', 'autoload']);
        is_callable(['\\Humbug\\Swift', 'autoload']);
        is_callable(['DateTime', 'autoload']);
        
        ----
        <?php
        
        namespace Humbug;
        
        \is_callable(['Humbug\\Swift', 'autoload']);
        \is_callable(['Humbug\\Swift', 'autoload']);
        \is_callable(['Humbug\\Swift', 'autoload']);
        \is_callable(['DateTime', 'autoload']);
        
        PHP,
    ],

    'FQCN string argument on exposed function' => [
        'expose-functions' => ['sodiumCompatAutoloader'],
        'payload' => <<<'PHP'
        <?php
        
        is_callable('sodiumCompatAutoloader');
        
        ----
        <?php
        
        namespace Humbug;
        
        \is_callable('Humbug\\sodiumCompatAutoloader');
        
        PHP,
    ],

    'FQCN string argument on class from an excluded namespace' => [
        'exclude-namespaces' => [
            'Symfony\Component\Yaml',
            '/^$/',
        ],
        'payload' => <<<'PHP'
        <?php
        
        is_callable(['Swift', 'autoload']);
        is_callable(['Humbug\\Swift', 'autoload']);
        is_callable(['\\Humbug\\Swift', 'autoload']);
        is_callable(['DateTime', 'autoload']);
        
        ----
        <?php
        
        namespace {
            \is_callable(['Swift', 'autoload']);
            \is_callable(['Humbug\\Swift', 'autoload']);
            \is_callable(['Humbug\\Swift', 'autoload']);
            \is_callable(['DateTime', 'autoload']);
        }
        
        PHP,
    ],

    'FQCN string argument on function from an excluded namespace' => [
        'exclude-namespaces' => [
            'Sodium',
            '/^$/',
        ],
        'payload' => <<<'PHP'
        <?php
        
        is_callable('Sodium\CompatAutoloader');
        
        ----
        <?php
        
        namespace {
            \is_callable('Sodium\\CompatAutoloader');
        }
        
        PHP,
    ],

    'FQCN string argument with global functions not exposed' => [
        'expose-global-functions' => false,
        'payload' => <<<'PHP'
        <?php
        
        is_callable(['Swift', 'autoload']);
        is_callable(['Humbug\\Swift', 'autoload']);
        is_callable(['\\Humbug\\Swift', 'autoload']);
        is_callable(['DateTime', 'autoload']);
        
        ----
        <?php
        
        namespace Humbug;
        
        \is_callable(['Humbug\\Swift', 'autoload']);
        \is_callable(['Humbug\\Swift', 'autoload']);
        \is_callable(['Humbug\\Swift', 'autoload']);
        \is_callable(['DateTime', 'autoload']);
        
        PHP,
    ],

    'FQCN string argument formed by concatenated strings' => <<<'PHP'
    <?php
    
    is_callable(['Swift'.'', 'autoload']);
    
    ----
    <?php
    
    namespace Humbug;
    
    \is_callable(['Swift' . '', 'autoload']);
    
    PHP,

    'FQC constant call' => <<<'PHP'
    <?php
    
    namespace Symfony\Component\Yaml {
        class Yaml {}
    }
    
    namespace {
        is_callable([\Swift::class, 'autoload']);
        is_callable([\Humbug\Swift::class, 'autoload']);
        is_callable([\DateTime::class, 'autoload']);
    }
    ----
    <?php
    
    namespace Humbug\Symfony\Component\Yaml;
    
    class Yaml
    {
    }
    namespace Humbug;
    
    \is_callable([\Humbug\Swift::class, 'autoload']);
    \is_callable([\Humbug\Swift::class, 'autoload']);
    \is_callable([\DateTime::class, 'autoload']);
    
    PHP,

    'FQC constant call on exposed class' => [
        'expose-classes' => ['Symfony\Component\Yaml\Ya_1'],
        'expected-recorded-classes' => [
            ['Symfony\Component\Yaml\Ya_1', 'Humbug\Symfony\Component\Yaml\Ya_1'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace Symfony\Component\Yaml {
            class Ya_1 {}
        }
        
        namespace {
            is_callable([Symfony\Component\Yaml\Ya_1::class, 'autoload']);
            is_callable([\Symfony\Component\Yaml\Ya_1::class, 'autoload']);
            is_callable([Humbug\Symfony\Component\Yaml\Ya_1::class, 'autoload']);
            is_callable([\Humbug\Symfony\Component\Yaml\Ya_1::class, 'autoload']);
        }
        ----
        <?php
        
        namespace Humbug\Symfony\Component\Yaml;
        
        class Ya_1
        {
        }
        \class_alias('Humbug\\Symfony\\Component\\Yaml\\Ya_1', 'Symfony\\Component\\Yaml\\Ya_1', \false);
        namespace Humbug;
        
        \is_callable([\Humbug\Symfony\Component\Yaml\Ya_1::class, 'autoload']);
        \is_callable([\Humbug\Symfony\Component\Yaml\Ya_1::class, 'autoload']);
        \is_callable([\Humbug\Symfony\Component\Yaml\Ya_1::class, 'autoload']);
        \is_callable([\Humbug\Symfony\Component\Yaml\Ya_1::class, 'autoload']);
        
        PHP
    ],
];
