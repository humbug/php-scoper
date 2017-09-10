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
        'title' => 'Self, static and parent keywords on methods',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Usage for classes in the global scope.
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class A {
    private $name;
    
    public function __construct(string $name) {
        $this->name = $name;
    }
    
    public static function who() {
        echo __METHOD__.PHP_EOL;
    }
    
    public static function test() {
        self::who();
        static::who();
    }
    
    public function getName(): string
    {
        return $this->name;
    }
}

class B extends A {
    public function __construct(string $name) {
        parent::__construct($name);
    }
    
    public static function who() {
        echo __METHOD__.PHP_EOL;
    }
}

B::test();
echo (new B('yo'))->getName().PHP_EOL;

----
<?php

class A
{
    private $name;
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    public static function who()
    {
        echo __METHOD__ . \PHP_EOL;
    }
    public static function test()
    {
        self::who();
        static::who();
    }
    public function getName() : string
    {
        return $this->name;
    }
}
class B extends \A
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }
    public static function who()
    {
        echo __METHOD__ . \PHP_EOL;
    }
}
\B::test();
echo (new \B('yo'))->getName() . \PHP_EOL;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Usage for classes in a namespaced.
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class A {
        private $name;
        
        public function __construct(string $name) {
            $this->name = $name;
        }
        
        public static function who() {
            echo __METHOD__.PHP_EOL;
        }
        
        public static function test() {
            self::who();
            static::who();
        }
        
        public function getName(): string
        {
            return $this->name;
        }
    }
    
    class B extends A {
        public function __construct(string $name) {
            parent::__construct($name);
        }
        
        public static function who() {
            echo __METHOD__.PHP_EOL;
        }
    }
}

namespace {
    B::test();
    echo (new B('yo'))->getName().PHP_EOL;
}

----
<?php

namespace Humbug\Foo {
    class A
    {
        private $name;
        public function __construct(string $name)
        {
            $this->name = $name;
        }
        public static function who()
        {
            echo __METHOD__ . PHP_EOL;
        }
        public static function test()
        {
            self::who();
            static::who();
        }
        public function getName() : string
        {
            return $this->name;
        }
    }
    class B extends \Humbug\Foo\A
    {
        public function __construct(string $name)
        {
            parent::__construct($name);
        }
        public static function who()
        {
            echo __METHOD__ . PHP_EOL;
        }
    }
}
namespace {
    \B::test();
    echo (new \B('yo'))->getName() . \PHP_EOL;
}

PHP
    ],
];
