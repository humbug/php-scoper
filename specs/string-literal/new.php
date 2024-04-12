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
    
    new X('Yaml', ['Yaml']);
    new X('\\Yaml', ['\\Yaml']);
    new X('Closure', ['Closure']);
    new X('\\Closure', ['\\Closure']);
    new X('Symfony\\Component\\Yaml\\Ya_1', ['Symfony\\Component\\Yaml\\Ya_1']);
    new X('\\Symfony\\Component\\Yaml\\Ya_1', ['\\Symfony\\Component\\Yaml\\Ya_1']);
    new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
    new X('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['\\Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
    
    ----
    <?php
    
    namespace Humbug;
    
    new X('Yaml', ['Yaml']);
    new X('\\Yaml', ['\\Yaml']);
    new X('Closure', ['Closure']);
    new X('\\Closure', ['\\Closure']);
    new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
    new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
    new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
    new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
    
    PHP,

    'FQCN string argument on exposed class' => [
        'expose-classes' => ['Symfony\Component\Yaml\Yaml'],
        'payload' => <<<'PHP'
        <?php
        
        new X('Symfony\\Component\\Yaml\\Ya_1l', ['Symfony\\Component\\Yaml\\Ya_1l']);
        new X('Symfony\\Component\\Yaml\\Ya_1', ['Symfony\\Component\\Yaml\\Ya_1']);
        new X('\\Symfony\\Component\\Yaml\\Ya_1', ['\\Symfony\\Component\\Yaml\\Ya_1']);
        new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
        new X('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['\\Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
        
        ----
        <?php
        
        namespace Humbug;
        
        new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1l', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1l']);
        new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
        new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
        new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
        new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
        
        PHP,
    ],

    'FQCN string argument on classes belonging to an excluded namespace' => [
        'exclude-namespaces' => ['Symfony\Component'],
        'payload' => <<<'PHP'
        <?php
        
        new X('Symfony\\Yaml', ['Symfony\\Yaml']);
        new X('Symfony\\Component\\Yaml\\Ya_1', ['Symfony\\Component\\Yaml\\Ya_1']);
        new X('\\Symfony\\Component\\Yaml\\Ya_1', ['\\Symfony\\Component\\Yaml\\Ya_1']);
        new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
        new X('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['\\Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
        
        ----
        <?php
        
        namespace Humbug;
        
        new X('Humbug\\Symfony\\Yaml', ['Humbug\\Symfony\\Yaml']);
        new X('Symfony\\Component\\Yaml\\Ya_1', ['Symfony\\Component\\Yaml\\Ya_1']);
        new X('\\Symfony\\Component\\Yaml\\Ya_1', ['\\Symfony\\Component\\Yaml\\Ya_1']);
        new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
        new X('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['\\Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
        
        PHP,
    ],

    'FQCN string argument formed by concatenated strings' => <<<'PHP'
    <?php
    
    new X('Symfony\\Component' . '\\Yaml\\Ya_1', ['Symfony\\Component' . '\\Yaml\\Ya_1']);
    new X('\\Symfony\\Component' . '\\Yaml\\Ya_1', ['\\Symfony\\Component' . '\\Yaml\\Ya_1']);
    
    ----
    <?php
    
    namespace Humbug;
    
    new X('Symfony\\Component' . '\\Yaml\\Ya_1', ['Symfony\\Component' . '\\Yaml\\Ya_1']);
    new X('\\Symfony\\Component' . '\\Yaml\\Ya_1', ['\\Symfony\\Component' . '\\Yaml\\Ya_1']);
    
    PHP,

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
    
    new X(Symfony\Component\Yaml\Yaml::class, [Symfony\Component\Yaml\Yaml::class]);
    new X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);
    new X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);
    new X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);
    
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
            new X(Symfony\Component\Yaml\Ya_1::class, [Symfony\Component\Yaml\Ya_1::class]);
            new X(\Symfony\Component\Yaml\Ya_1::class, [\Symfony\Component\Yaml\Ya_1::class]);
            new X(Humbug\Symfony\Component\Yaml\Ya_1::class, [Humbug\Symfony\Component\Yaml\Ya_1::class]);
            new X(\Humbug\Symfony\Component\Yaml\Ya_1::class, [\Humbug\Symfony\Component\Yaml\Ya_1::class]);
        }
        ----
        <?php
        
        namespace Humbug\Symfony\Component\Yaml;
        
        class Ya_1
        {
        }
        \class_alias('Humbug\\Symfony\\Component\\Yaml\\Ya_1', 'Symfony\\Component\\Yaml\\Ya_1', \false);
        namespace Humbug;
        
        new X(\Humbug\Symfony\Component\Yaml\Ya_1::class, [\Humbug\Symfony\Component\Yaml\Ya_1::class]);
        new X(\Humbug\Symfony\Component\Yaml\Ya_1::class, [\Humbug\Symfony\Component\Yaml\Ya_1::class]);
        new X(\Humbug\Symfony\Component\Yaml\Ya_1::class, [\Humbug\Symfony\Component\Yaml\Ya_1::class]);
        new X(\Humbug\Symfony\Component\Yaml\Ya_1::class, [\Humbug\Symfony\Component\Yaml\Ya_1::class]);
        
        PHP,
    ],
];
