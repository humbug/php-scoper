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

use Humbug\PhpScoper\Scoper\Spec\Meta;

return [
    'meta' => new Meta(
        title: 'String literal assigned as a class constant',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'FQCN string argument' => <<<'PHP'
    <?php
    
    class Foo {
        const X = 'Symfony\\Component\\Yaml\\Ya_1';
        const X = '\\Symfony\\Component\\Yaml\\Ya_1';
        const X = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        const X = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        
        const X = 'HelloWorld';
    }
    
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
        const X = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        const X = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        const X = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        const X = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        const X = 'HelloWorld';
    }
    
    PHP,
];
