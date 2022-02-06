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
        'title' => 'String literal used as a function argument of an anonymous function',
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
    
    (function($x = 'Symfony\\Component\\Yaml\\Ya_1') {})();
    (function($x = '\\Symfony\\Component\\Yaml\\Ya_1') {})();
    (function($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {})();
    (function($x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1') {})();
    
    (function($x = 'DateTime') {})();
    (function($x = 'Swift') {})();
    (function($x = ['DateTime', 'autoload']) {})();
    (function($x = ['Swift', 'autoload']) {})();
    
    (static function($x = 'Symfony\\Component\\Yaml\\Ya_1') {})();
    (static function($x = '\\Symfony\\Component\\Yaml\\Ya_1') {})();
    (static function($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {})();
    (static function($x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1') {})();
    
    (static function($x = 'DateTime') {})();
    (static function($x = 'Swift') {})();
    (static function($x = ['DateTime', 'autoload']) {})();
    (static function($x = ['Swift', 'autoload']) {})();
    
    (fn ($x = 'Symfony\\Component\\Yaml\\Ya_1') => null)();
    (fn ($x = '\\Symfony\\Component\\Yaml\\Ya_1') => null)();
    (fn ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') => null)();
    (fn ($x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1') => null)();
    
    (fn($x = 'DateTime') => null)();
    (fn($x = 'Swift') => null)();
    (fn($x = ['DateTime', 'autoload']) => null)();
    (fn($x = ['Swift', 'autoload']) => null)();
    
    (static fn($x = 'Symfony\\Component\\Yaml\\Ya_1') => null)();
    (static fn($x = '\\Symfony\\Component\\Yaml\\Ya_1') => null)();
    (static fn($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') => null)();
    (static fn($x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1') => null)();
    
    (static fn($x = 'DateTime') => null)();
    (static fn($x = 'Swift') => null)();
    (static fn($x = ['DateTime', 'autoload']) => null)();
    (static fn($x = ['Swift', 'autoload']) => null)();
    
    ($this->colorize)('fg-green', '✔');
    ($this->colorize)(['Soft', 'autoload']);
    ($this->colorize)(['\\Soft', 'autoload']);
    
    ----
    <?php
    
    namespace Humbug;
    
    (function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
    })();
    (function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
    })();
    (function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
    })();
    (function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
    })();
    (function ($x = 'DateTime') {
    })();
    (function ($x = 'Swift') {
    })();
    (function ($x = ['DateTime', 'autoload']) {
    })();
    (function ($x = ['Swift', 'autoload']) {
    })();
    (static function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
    })();
    (static function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
    })();
    (static function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
    })();
    (static function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
    })();
    (static function ($x = 'DateTime') {
    })();
    (static function ($x = 'Swift') {
    })();
    (static function ($x = ['DateTime', 'autoload']) {
    })();
    (static function ($x = ['Swift', 'autoload']) {
    })();
    (fn($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') => null)();
    (fn($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') => null)();
    (fn($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') => null)();
    (fn($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') => null)();
    (fn($x = 'DateTime') => null)();
    (fn($x = 'Swift') => null)();
    (fn($x = ['DateTime', 'autoload']) => null)();
    (fn($x = ['Swift', 'autoload']) => null)();
    (static fn($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') => null)();
    (static fn($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') => null)();
    (static fn($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') => null)();
    (static fn($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') => null)();
    (static fn($x = 'DateTime') => null)();
    (static fn($x = 'Swift') => null)();
    (static fn($x = ['DateTime', 'autoload']) => null)();
    (static fn($x = ['Swift', 'autoload']) => null)();
    ($this->colorize)('fg-green', '✔');
    ($this->colorize)(['Soft', 'autoload']);
    ($this->colorize)(['\\Soft', 'autoload']);
    
    PHP,

    'FQCN string argument on exposed class' => [
        'expose-classes' => ['Symfony\Component\Yaml\Yaml', 'Swift'],
        'payload' => <<<'PHP'
        <?php
        
        (function($x = 'Symfony\\Component\\Yaml\\Ya_1') {})();
        (function($x = '\\Symfony\\Component\\Yaml\\Ya_1') {})();
        (function($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {})();
        (function($x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1') {})();
        
        (function($x = 'DateTime') {})();
        (function($x = 'Swift') {})();
        (function($x = ['DateTime', 'autoload']) {})();
        (function($x = ['Swift', 'autoload']) {})();
        
        ----
        <?php
        
        namespace Humbug;
        
        (function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
        })();
        (function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
        })();
        (function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
        })();
        (function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
        })();
        (function ($x = 'DateTime') {
        })();
        (function ($x = 'Swift') {
        })();
        (function ($x = ['DateTime', 'autoload']) {
        })();
        (function ($x = ['Swift', 'autoload']) {
        })();
        
        PHP
    ],

    'FQCN string argument on class from global namespace with classes from global namespace exposed' => [
        'expose-global-classes' => true,
        'payload' => <<<'PHP'
        <?php
        
        (function($x = 'DateTime') {})();
        (function($x = 'Swift') {})();
        (function($x = ['DateTime', 'autoload']) {})();
        (function($x = ['Swift', 'autoload']) {})();
        
        ----
        <?php
        
        namespace Humbug;
        
        (function ($x = 'DateTime') {
        })();
        (function ($x = 'Swift') {
        })();
        (function ($x = ['DateTime', 'autoload']) {
        })();
        (function ($x = ['Swift', 'autoload']) {
        })();
        
        PHP
    ],

    'FQCN string argument on class from an excluded namespace' => [
        'exclude-namespaces' => [
            'Symfony\Component\Yaml',
            '/^$/',
        ],
        'payload' => <<<'PHP'
        <?php
        
        (function($x = 'Symfony\\Component\\Yaml\\Ya_1') {})();
        (function($x = '\\Symfony\\Component\\Yaml\\Ya_1') {})();
        (function($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {})();
        (function($x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1') {})();
        
        (function($x = 'DateTime') {})();
        (function($x = 'Swift') {})();
        (function($x = ['DateTime', 'autoload']) {})();
        (function($x = ['Swift', 'autoload']) {})();
        
        ----
        <?php
        
        namespace {
            (function ($x = 'Symfony\\Component\\Yaml\\Ya_1') {
            })();
            (function ($x = '\\Symfony\\Component\\Yaml\\Ya_1') {
            })();
            (function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
            })();
            (function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
            })();
            (function ($x = 'DateTime') {
            })();
            (function ($x = 'Swift') {
            })();
            (function ($x = ['DateTime', 'autoload']) {
            })();
            (function ($x = ['Swift', 'autoload']) {
            })();
        }
        
        PHP
    ],

    'FQC constant call' => <<<'PHP'
    <?php
    
    namespace Symfony\Component\Yaml {
        class Yaml {}
    }
    
    namespace {
        (function($x = Symfony\Component\Yaml\Yaml::class) {})();
        (function($x = \Symfony\Component\Yaml\Yaml::class) {})();
        (function($x = Humbug\Symfony\Component\Yaml\Yaml::class) {})();
        (function($x = \Humbug\Symfony\Component\Yaml\Yaml::class) {})();
        
        (function($x = \DateTime::class) {})();
        (function($x = \Swift::class) {})();
        (function($x = [\DateTime::class, 'autoload']) {})();
        (function($x = [\Swift::class, 'autoload']) {})();
    }
    ----
    <?php
    
    namespace Humbug\Symfony\Component\Yaml;
    
    class Yaml
    {
    }
    namespace Humbug;
    
    (function ($x = Symfony\Component\Yaml\Yaml::class) {
    })();
    (function ($x = \Humbug\Symfony\Component\Yaml\Yaml::class) {
    })();
    (function ($x = \Humbug\Symfony\Component\Yaml\Yaml::class) {
    })();
    (function ($x = \Humbug\Symfony\Component\Yaml\Yaml::class) {
    })();
    (function ($x = \DateTime::class) {
    })();
    (function ($x = \Humbug\Swift::class) {
    })();
    (function ($x = [\DateTime::class, 'autoload']) {
    })();
    (function ($x = [\Humbug\Swift::class, 'autoload']) {
    })();

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
            (function ($x = Symfony\Component\Yaml\Ya_1::class) {})();
            (function ($x = \Symfony\Component\Yaml\Ya_1::class) {})();
            (function ($x = Humbug\Symfony\Component\Yaml\Ya_1::class) {})();
            (function ($x = \Humbug\Symfony\Component\Yaml\Ya_1::class) {})();
        }
        ----
        <?php
        
        namespace Humbug\Symfony\Component\Yaml;
        
        class Ya_1
        {
        }
        \class_alias('Humbug\\Symfony\\Component\\Yaml\\Ya_1', 'Symfony\\Component\\Yaml\\Ya_1', \false);
        namespace Humbug;
        
        (function ($x = \Humbug\Symfony\Component\Yaml\Ya_1::class) {
        })();
        (function ($x = \Humbug\Symfony\Component\Yaml\Ya_1::class) {
        })();
        (function ($x = \Humbug\Symfony\Component\Yaml\Ya_1::class) {
        })();
        (function ($x = \Humbug\Symfony\Component\Yaml\Ya_1::class) {
        })();

        PHP
    ],
];
