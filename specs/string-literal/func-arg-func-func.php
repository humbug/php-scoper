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
        title: 'String literal used as a function argument of function-related functions',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'FQFN string argument' => <<<'PHP'
    <?php
    
    function_exists('Acme\foo');
    function_exists('\\Acme\\foo');
    function_exists('Humbug\\Acme\\foo');
    function_exists('\\Humbug\\Acme\\foo');
    
    function_exists('dump');
    function_exists('\\dump');
    function_exists('Humbug\\dump');
    function_exists('\\Humbug\\dump');
    
    function_exists('var_dump');
    function_exists('\\var_dump');
    function_exists('Humbug\\var_dump');
    function_exists('\\Humbug\\var_dump');
    
    ----
    <?php
    
    namespace Humbug;
    
    \function_exists('Humbug\\Acme\\foo');
    \function_exists('Humbug\\Acme\\foo');
    \function_exists('Humbug\\Acme\\foo');
    \function_exists('Humbug\\Acme\\foo');
    \function_exists('Humbug\\dump');
    \function_exists('Humbug\\dump');
    \function_exists('Humbug\\dump');
    \function_exists('Humbug\\dump');
    \function_exists('var_dump');
    \function_exists('\\var_dump');
    \function_exists('Humbug\\var_dump');
    \function_exists('Humbug\\var_dump');
    
    PHP,

    'FQFN string argument on exposed function' => [
        exposeFunctions: ['Acme\foo', 'dump'],
        expectedRecordedFunctions: [
            ['Acme\foo', 'Humbug\Acme\foo'],
            ['dump', 'Humbug\dump'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        function_exists('Acme\foo');
        function_exists('\\Acme\\foo');
        function_exists('Humbug\\Acme\\foo');
        function_exists('\\Humbug\\Acme\\foo');
        
        function_exists('dump');
        function_exists('\\dump');
        function_exists('Humbug\\dump');
        function_exists('\\Humbug\\dump');
        
        function_exists('var_dump');
        function_exists('\\var_dump');
        function_exists('Humbug\\var_dump');
        function_exists('\\Humbug\\var_dump');
        
        ----
        <?php
        
        namespace Humbug;
        
        \function_exists('Humbug\\Acme\\foo');
        \function_exists('Humbug\\Acme\\foo');
        \function_exists('Humbug\\Acme\\foo');
        \function_exists('Humbug\\Acme\\foo');
        \function_exists('Humbug\\dump');
        \function_exists('Humbug\\dump');
        \function_exists('Humbug\\dump');
        \function_exists('Humbug\\dump');
        \function_exists('var_dump');
        \function_exists('\\var_dump');
        \function_exists('Humbug\\var_dump');
        \function_exists('Humbug\\var_dump');
        
        PHP,
    ],

    'FQFN string argument on function from an excluded namespace' => [
        excludeNamespaces: [
            'Acme',
            '/^$/',
        ],
        'payload' => <<<'PHP'
        <?php
        
        function_exists('Acme\foo');
        function_exists('\\Acme\\foo');
        function_exists('Humbug\\Acme\\foo');
        function_exists('\\Humbug\\Acme\\foo');
        
        function_exists('dump');
        function_exists('\\dump');
        function_exists('Humbug\\dump');
        function_exists('\\Humbug\\dump');
        
        function_exists('var_dump');
        function_exists('\\var_dump');
        function_exists('Humbug\\var_dump');
        function_exists('\\Humbug\\var_dump');
        
        ----
        <?php
        
        namespace {
            \function_exists('Acme\\foo');
            \function_exists('\\Acme\\foo');
            \function_exists('Humbug\\Acme\\foo');
            \function_exists('\\Humbug\\Acme\\foo');
            \function_exists('dump');
            \function_exists('\\dump');
            \function_exists('Humbug\\dump');
            \function_exists('Humbug\\dump');
            \function_exists('var_dump');
            \function_exists('\\var_dump');
            \function_exists('Humbug\\var_dump');
            \function_exists('Humbug\\var_dump');
        }
        
        PHP,
    ],

    'FQFN string argument with global functions exposed' => [
        exposeGlobalFunctions: true,
        expectedRecordedFunctions: [
            ['dump', 'Humbug\dump'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        function_exists('Acme\foo');
        function_exists('\\Acme\\foo');
        function_exists('Humbug\\Acme\\foo');
        function_exists('\\Humbug\\Acme\\foo');
        
        function_exists('dump');
        function_exists('\\dump');
        function_exists('Humbug\\dump');
        function_exists('\\Humbug\\dump');
        
        function_exists('var_dump');
        function_exists('\\var_dump');
        function_exists('Humbug\\var_dump');
        function_exists('\\Humbug\\var_dump');
        
        ----
        <?php
        
        namespace Humbug;

        \function_exists('Humbug\\Acme\\foo');
        \function_exists('Humbug\\Acme\\foo');
        \function_exists('Humbug\\Acme\\foo');
        \function_exists('Humbug\\Acme\\foo');
        \function_exists('Humbug\\dump');
        \function_exists('Humbug\\dump');
        \function_exists('Humbug\\dump');
        \function_exists('Humbug\\dump');
        \function_exists('var_dump');
        \function_exists('\\var_dump');
        \function_exists('Humbug\\var_dump');
        \function_exists('Humbug\\var_dump');

        PHP,
    ],

    'FQCN string argument formed by concatenated strings' => <<<'PHP'
    <?php
    
    function_exists('Acme\foo'.'');
    function_exists('\\Acme\\foo'.'');
    function_exists('Humbug\\Acme\\foo'.'');
    function_exists('\\Humbug\\Acme\\foo'.'');
    
    function_exists('dump'.'');
    function_exists('\\dump'.'');
    function_exists('Humbug\\dump'.'');
    function_exists('\\Humbug\\dump'.'');
    
    function_exists('var_dump'.'');
    function_exists('\\var_dump'.'');
    function_exists('Humbug\\var_dump'.'');
    function_exists('\\Humbug\\var_dump'.'');
    
    ----
    <?php
    
    namespace Humbug;
    
    \function_exists('Acme\\foo' . '');
    \function_exists('\\Acme\\foo' . '');
    \function_exists('Humbug\\Acme\\foo' . '');
    \function_exists('\\Humbug\\Acme\\foo' . '');
    \function_exists('dump' . '');
    \function_exists('\\dump' . '');
    \function_exists('Humbug\\dump' . '');
    \function_exists('\\Humbug\\dump' . '');
    \function_exists('var_dump' . '');
    \function_exists('\\var_dump' . '');
    \function_exists('Humbug\\var_dump' . '');
    \function_exists('\\Humbug\\var_dump' . '');
    
    PHP,
];
