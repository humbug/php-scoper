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
        'title' => 'String value assigned as a class property',
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
    
    class Foo {
        var $x = 'Symfony\\Component\\Yaml\\Ya_1';
        var $x = '\\Symfony\\Component\\Yaml\\Ya_1';
        var $x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        var $x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1';
    }
    
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
        var $x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        var $x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        var $x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        var $x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
    }
    
    PHP,
];
