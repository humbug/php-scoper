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
        'title' => 'String literal used as a function argument of a user-defined function',
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
        'expected-recorded-ambiguous-functions' => [],
    ],

    'FQCN string argument' => <<<'PHP'
    <?php
    
    foo('Symfony\\Component\\Yaml\\Ya_1');
    foo('\\Symfony\\Component\\Yaml\\Ya_1');
    foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
    foo('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1');
    
    foo('DateTime');
    foo('\\DateTime');
    foo('Swift');
    foo('\\Swift');
    
    foo(['DateTime', 'autoload']);
    foo(['\\DateTime', 'autoload']);
    foo(['Swift', 'autoload']);
    foo(['\\Swift', 'autoload']);
    
    ----
    <?php
    
    namespace Humbug;
    
    foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
    foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
    foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
    foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
    foo('DateTime');
    foo('\\DateTime');
    foo('Swift');
    foo('\\Swift');
    foo(['DateTime', 'autoload']);
    foo(['\\DateTime', 'autoload']);
    foo(['Swift', 'autoload']);
    foo(['\\Swift', 'autoload']);
    
    PHP,

    'FQCN string argument on exposed class' => [
        'expose-classes' => ['Symfony\Component\Yaml\Yaml', 'Swift'],
        'payload' => <<<'PHP'
        <?php
        
        foo('Symfony\\Component\\Yaml\\Ya_1');
        foo('\\Symfony\\Component\\Yaml\\Ya_1');
        foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
        foo('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1');
        
        foo('DateTime');
        foo('Swift');
        foo(['DateTime', 'autoload']);
        foo(['Swift', 'autoload']);
        
        ----
        <?php
        
        namespace Humbug;
        
        foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
        foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
        foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
        foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
        foo('DateTime');
        foo('Swift');
        foo(['DateTime', 'autoload']);
        foo(['Swift', 'autoload']);
        
        PHP
    ],

    'FQCN string argument on class from global namespace with classes from global namespace exposed' => [
        'expose-global-classes' => true,
        'payload' => <<<'PHP'
        <?php
        
        foo('DateTime');
        foo('Swift');
        foo(['DateTime', 'autoload']);
        foo(['Swift', 'autoload']);
        
        ----
        <?php
        
        namespace Humbug;
        
        foo('DateTime');
        foo('Swift');
        foo(['DateTime', 'autoload']);
        foo(['Swift', 'autoload']);
        
        PHP
    ],

    'FQCN string argument on class from an excluded namespace' => [
        'exclude-namespaces' => [
            'Symfony\Component\Yaml',
            '/^$/',
        ],
        'payload' => <<<'PHP'
        <?php
        
        foo('Symfony\\Component\\Yaml\\Ya_1');
        foo('\\Symfony\\Component\\Yaml\\Ya_1');
        foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
        foo('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1');
        
        foo('DateTime');
        foo('Swift');
        foo(['DateTime', 'autoload']);
        foo(['Swift', 'autoload']);
        
        ----
        <?php
        
        namespace {
            \foo('Symfony\\Component\\Yaml\\Ya_1');
            \foo('\\Symfony\\Component\\Yaml\\Ya_1');
            \foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
            \foo('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1');
            \foo('DateTime');
            \foo('Swift');
            \foo(['DateTime', 'autoload']);
            \foo(['Swift', 'autoload']);
        }
        
        PHP
    ],

    'FQCN string argument formed by concatenated strings' => <<<'PHP'
    <?php
    
    foo('Symfony\\Component' . '\\Yaml\\Ya_1');
    foo('\\Symfony\\Component' . '\\Yaml\\Ya_1');
    
    foo('Swift'.'');
    
    ----
    <?php
    
    namespace Humbug;
    
    foo('Symfony\\Component' . '\\Yaml\\Ya_1');
    foo('\\Symfony\\Component' . '\\Yaml\\Ya_1');
    foo('Swift' . '');
    
    PHP,

    'FQC class constant call' => <<<'PHP'
    <?php
    
    namespace Symfony\Component\Yaml {
        class Yaml {}
    }
    
    namespace {
        foo(Symfony\Component\Yaml\Yaml::class);
        foo(\Symfony\Component\Yaml\Yaml::class);
        foo(Humbug\Symfony\Component\Yaml\Yaml::class);
        foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
        
        foo(\DateTime::class);
        foo(\Swift::class);
        foo([\DateTime::class, 'autoload']);
        foo([\Swift::class, 'autoload']);
    }
    ----
    <?php
    
    namespace Humbug\Symfony\Component\Yaml;
    
    class Yaml
    {
    }
    namespace Humbug;
    
    foo(Symfony\Component\Yaml\Yaml::class);
    foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
    foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
    foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
    foo(\DateTime::class);
    foo(\Humbug\Swift::class);
    foo([\DateTime::class, 'autoload']);
    foo([\Humbug\Swift::class, 'autoload']);
    
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
            foo(Symfony\Component\Yaml\Ya_1::class);
            foo(\Symfony\Component\Yaml\Ya_1::class);
            foo(Humbug\Symfony\Component\Yaml\Ya_1::class);
            foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);
        }
        ----
        <?php
        
        namespace Humbug\Symfony\Component\Yaml;
        
        class Ya_1
        {
        }
        \class_alias('Humbug\\Symfony\\Component\\Yaml\\Ya_1', 'Symfony\\Component\\Yaml\\Ya_1', \false);
        namespace Humbug;
        
        foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);
        foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);
        foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);
        foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);
        
        PHP
    ],
];
