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
        title: 'Self, static and parent keywords on static variables',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'Usage for classes in the global scope' => <<<'PHP'
    <?php
    
    class A {
        protected static $foo = 'FOO';
        
        private $name;
        
        public function __construct(string $name) {
            $this->name = $name;
        }
        
        public static function test() {
            self::$foo;
            static::$foo;
        }
        
        public function getName(): string
        {
            return $this->name;
        }
    }
    
    class B extends A {
        static $foo = 'BAR';
        
        public function __construct(string $name) {
            parent::__construct($name);
            
            parent::$foo;
        }
    }
    
    B::test();
    echo (new B('yo'))->getName().PHP_EOL;
    
    ----
    <?php
    
    namespace Humbug;

    class A
    {
        protected static $foo = 'FOO';
        private $name;
        public function __construct(string $name)
        {
            $this->name = $name;
        }
        public static function test()
        {
            self::$foo;
            static::$foo;
        }
        public function getName() : string
        {
            return $this->name;
        }
    }
    class B extends A
    {
        static $foo = 'BAR';
        public function __construct(string $name)
        {
            parent::__construct($name);
            parent::$foo;
        }
    }
    B::test();
    echo (new B('yo'))->getName() . \PHP_EOL;
    
    PHP,

    'Usage for classes in a namespaced' => <<<'PHP'
    <?php
    
    namespace Foo {
        class A {
            protected static $foo = 'FOO';
            
            private $name;
            
            public function __construct(string $name) {
                $this->name = $name;
            }
            
            public static function test() {
                self::$foo;
                static::$foo;
            }
            
            public function getName(): string
            {
                return $this->name;
            }
        }
            
        class B extends A {
            static $foo = 'BAR';
            
            public function __construct(string $name) {
                parent::__construct($name);
                
                parent::$foo;
            }
        }
    }
    
    namespace {
        use Foo\B;
    
        B::test();
        echo (new B('yo'))->getName().PHP_EOL;
    }
    
    ----
    <?php
    
    namespace Humbug\Foo;
    
    class A
    {
        protected static $foo = 'FOO';
        private $name;
        public function __construct(string $name)
        {
            $this->name = $name;
        }
        public static function test()
        {
            self::$foo;
            static::$foo;
        }
        public function getName() : string
        {
            return $this->name;
        }
    }
    class B extends A
    {
        static $foo = 'BAR';
        public function __construct(string $name)
        {
            parent::__construct($name);
            parent::$foo;
        }
    }
    namespace Humbug;
    
    use Humbug\Foo\B;
    B::test();
    echo (new B('yo'))->getName() . \PHP_EOL;
    
    PHP,
];
