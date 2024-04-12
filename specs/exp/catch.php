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
        title: 'Catch expressions',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'Catch an internal class' => <<<'PHP'
    <?php
    
    try {
        echo "foo";
    } catch (Throwable $t) {
    }
    ----
    <?php
    
    namespace Humbug;
    
    try {
        echo "foo";
    } catch (\Throwable $t) {
    }
    
    PHP,

    'Catch an internal class in a namespace' => <<<'PHP'
    <?php
    
    namespace Acme;
    
    try {
        echo "foo";
    } catch (\Throwable $t) {
    }
    ----
    <?php
    
    namespace Humbug\Acme;
    
    try {
        echo "foo";
    } catch (\Throwable $t) {
    }
    
    PHP,

    'Catch a custom exception class' => <<<'PHP'
    <?php
    
    try {
        echo "foo";
    } catch (FooException $t) {
    }
    ----
    <?php
    
    namespace Humbug;
    
    try {
        echo "foo";
    } catch (FooException $t) {
    }
    
    PHP,

    'Catch an exposed custom exception class' => [
        exposeClasses: ['FooException'],
        'payload' => <<<'PHP'
        <?php
        
        try {
            echo "foo";
        } catch (FooException $t) {
        }
        ----
        <?php
        
        namespace Humbug;
        
        try {
            echo "foo";
        } catch (\Humbug\FooException $t) {
        }
        
        PHP,
    ],

    'Catch a custom exception class which belongs to the excluded root namespace' => [
        excludeNamespaces: ['/^$/'],
        'payload' => <<<'PHP'
        <?php
        
        try {
            echo "foo";
        } catch (FooException $t) {
        }
        ----
        <?php
        
        namespace {
            try {
                echo "foo";
            } catch (\FooException $t) {
            }
        }
        
        PHP,
    ],

    'Catch a custom exception class in a namespace' => <<<'PHP'
    <?php
    
    namespace Acme;
    
    try {
        echo "foo";
    } catch (FooException $t) {
    }
    ----
    <?php
    
    namespace Humbug\Acme;
    
    try {
        echo "foo";
    } catch (FooException $t) {
    }
    
    PHP,

    'Catch an exposed custom exception class in a namespace' => [
        exposeClasses: ['Acme\FooException'],
        'payload' => <<<'PHP'
        <?php
        
        namespace Acme;
        
        try {
            echo "foo";
        } catch (FooException $t) {
        }
        ----
        <?php
        
        namespace Humbug\Acme;
        
        try {
            echo "foo";
        } catch (FooException $t) {
        }
        
        PHP,
    ],

    // TODO: should not be made into FQ here
    'Catch a custom exception class in an excluded namespace' => [
        excludeNamespaces: ['Acme'],
        'payload' => <<<'PHP'
        <?php
        
        namespace Acme;
        
        try {
            echo "foo";
        } catch (FooException $t) {
        }
        ----
        <?php
        
        namespace Acme;
        
        try {
            echo "foo";
        } catch (\Acme\FooException $t) {
        }
        
        PHP,
    ],

    'Catch an custom exception class in a namespace imported with a use statement' => <<<'PHP'
    <?php
    
    namespace Acme;
    
    use X\FooException;
    
    try {
        echo "foo";
    } catch (FooException $t) {
    }
    ----
    <?php
    
    namespace Humbug\Acme;
    
    use Humbug\X\FooException;
    try {
        echo "foo";
    } catch (FooException $t) {
    }
    
    PHP,

    'Multiple catch statement' => <<<'PHP'
    <?php
    
    namespace Acme;
    
    use X\FooException;
    
    try {
        echo "foo";
    } catch (FooException | \Throwable $t) {
    }
    ----
    <?php
    
    namespace Humbug\Acme;
    
    use Humbug\X\FooException;
    try {
        echo "foo";
    } catch (FooException|\Throwable $t) {
    }
    
    PHP,

    'catch with special keywords' => <<<'PHP'
    <?php
    
    namespace Acme;
    
    try {
        echo "foo";
    } catch (self | parent $t) {
    }
    ----
    <?php
    
    namespace Humbug\Acme;
    
    try {
        echo "foo";
    } catch (self|parent $t) {
    }
    
    PHP,
];
