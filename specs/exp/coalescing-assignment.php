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
        'minPhpVersion' => 70400,
        title: 'Null coalescing assignment operator',

















    ),

    'Instance of an internal class' => <<<'PHP'
    <?php
    
    $x ??= new stdClass();
    ----
    <?php
    
    namespace Humbug;
    
    $x ??= new \stdClass();
    
    PHP,

    'Instance of an internal class in a namespace' => <<<'PHP'
    <?php
    
    namespace Acme;
    
    use stdClass;
    
    $x ??= new stdClass();
    
    ----
    <?php
    
    namespace Humbug\Acme;
    
    use stdClass;
    $x ??= new stdClass();
    
    PHP,

    'Instance of a custom exception class' => <<<'PHP'
    <?php
    
    $x ??= new Foo();
    
    ----
    <?php
    
    namespace Humbug;
    
    $x ??= new Foo();
    
    PHP,

    'Instance of a custom exception class in a namespace' => <<<'PHP'
    <?php
    
    namespace Acme;
    
    $x ??= new Foo();
    
    ----
    <?php
    
    namespace Humbug\Acme;
    
    $x ??= new Foo();
    
    PHP
    ,
];
