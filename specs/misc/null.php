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
        title: 'Null case-sensitivity',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'Usages of null' => <<<'PHP'
    <?php
    
    const LOWERCASE_NULL = null;
    const UPPERCASE_NULL = null;
    
    $lowerCaseNull = null;
    $upperCaseNull = null;
    
    function foo($lowerCaseNull = null, $upperCaseNull = NULL) {}
    
    class X {
        var $lowerCaseNull = null;
        var $upperCaseNull = NULL;
    }
    
    ----
    <?php
    
    namespace Humbug;
    
    const LOWERCASE_NULL = null;
    const UPPERCASE_NULL = null;
    $lowerCaseNull = null;
    $upperCaseNull = null;
    function foo($lowerCaseNull = null, $upperCaseNull = NULL)
    {
    }
    class X
    {
        var $lowerCaseNull = null;
        var $upperCaseNull = NULL;
    }
    
    PHP,
];
