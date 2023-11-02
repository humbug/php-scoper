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
        'title' => 'It adds @internal annotations to all declarations',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',

        'tag-declarations-as-internal' => true,

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

    'Declarations without any comment' => <<<'PHP'
    <?php
    
    class RegularClass {
        public function aMethod() {}
    }
    
    interface RegularInterface {}
    
    abstract class RegularAbstractClass {}
    
    function regular_function () {}
    
    trait RegularTrait {}
    
    const REGULAR_CONSTANT = 'FOO';
    
    enum RegularEnum {}
    
    ----
    <?php
    
    namespace Humbug;

    /** @internal */
    class RegularClass
    {
        public function aMethod()
        {
        }
    }
    /** @internal */
    interface RegularInterface
    {
    }
    /** @internal */
    abstract class RegularAbstractClass
    {
    }
    /** @internal */
    function regular_function()
    {
    }
    /** @internal */
    trait RegularTrait
    {
    }
    /** @internal */
    const REGULAR_CONSTANT = 'FOO';
    /** @internal */
    enum RegularEnum
    {
    }
    
    PHP,

    'Declarations without with comments' => <<<'PHP'
    <?php
    
    // Smth
    class RegularClass {
        public function aMethod() {}
    }
    ----
    <?php
    
    namespace Humbug;

    // Smth
    /** @internal */
    class RegularClass
    {
        public function aMethod()
        {
        }
    }
    
    PHP,

    'Declarations with existing phpDoc' => <<<'PHP'
    <?php
    
    /**
     * A comment.
     */
    class RegularClass {
        public function aMethod() {}
    }
    ----
    <?php
    
    namespace Humbug;

    /**
     * A comment.
     * @internal
     */
    class RegularClass
    {
        public function aMethod()
        {
        }
    }
    
    PHP,

    'Declarations with inlined phpDoc' => <<<'PHP'
    <?php
    
    /** A comment. */
    class RegularClass {
        public function aMethod() {}
    }
    ----
    <?php
    
    namespace Humbug;

    /** A comment.
     * @internal
     */
    class RegularClass
    {
        public function aMethod()
        {
        }
    }
    
    PHP,

    'Declarations with inlined phpDoc already containing the @internal tag' => <<<'PHP'
    <?php
    
    /** @internal */
    class RegularClass {
        public function aMethod() {}
    }
    ----
    <?php
    
    namespace Humbug;

    /** @internal */
    class RegularClass
    {
        public function aMethod()
        {
        }
    }
    
    PHP,

    'Declarations with existing phpDoc containing the @internal tag' => <<<'PHP'
    <?php
    
    /**
     * A comment.
     * 
     * @private
     * @internal
     */
    class RegularClass {
        public function aMethod() {}
    }
    ----
    <?php
    
    namespace Humbug;

    /**
     * A comment.
     * 
     * @private
     * @internal
     */
    class RegularClass
    {
        public function aMethod()
        {
        }
    }
    
    PHP,
];
