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
        title: 'String value assigned as a private property initial value',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'FQCN string argument' => <<<'PHP'
        <?php

        class Foo {
            private $x = 'Symfony\\Component\\Yaml\\Ya_1';
            private $x = '\\Symfony\\Component\\Yaml\\Ya_1';
            private $x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
            private $x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        }

        ----
        <?php

        namespace Humbug;

        class Foo
        {
            private $x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
            private $x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
            private $x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
            private $x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        }

        PHP,
];
