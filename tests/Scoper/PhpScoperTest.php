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

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\PhpParser\FakeParser;
use Humbug\PhpScoper\Scoper;
use PhpParser\Error as PhpParserError;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use function Humbug\PhpScoper\create_fake_patcher;
use function Humbug\PhpScoper\create_parser;
use function Humbug\PhpScoper\escape_path;
use function Humbug\PhpScoper\make_tmp_dir;
use function Humbug\PhpScoper\remove_dir;

/**
 * @covers \Humbug\PhpScoper\Scoper\PhpScoper
 */
class PhpScoperTest extends TestCase
{
    /**
     * @var Scoper
     */
    private $scoper;

    /**
     * @var string
     */
    private $cwd;

    /**
     * @var string
     */
    private $tmp;

    /**
     * @var Scoper|ObjectProphecy
     */
    private $decoratedScoperProphecy;

    /**
     * @var Scoper
     */
    private $decoratedScoper;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->scoper = new PhpScoper(
            create_parser(),
            new FakeScoper()
        );

        if (null === $this->tmp) {
            $this->cwd = getcwd();
            $this->tmp = make_tmp_dir('scoper', __CLASS__);
        }

        $this->decoratedScoperProphecy = $this->prophesize(Scoper::class);
        $this->decoratedScoper = $this->decoratedScoperProphecy->reveal();

        chdir($this->tmp);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        chdir($this->cwd);

        remove_dir($this->tmp);
    }

    public function test_is_a_Scoper()
    {
        $this->assertTrue(is_a(PhpScoper::class, Scoper::class, true));
    }

    public function test_can_scope_a_PHP_file()
    {
        $prefix = 'Humbug';
        $filePath = escape_path($this->tmp.'/file.php');
        $patchers = [create_fake_patcher()];

        $content = <<<'PHP'
echo "Humbug!";
PHP;

        touch($filePath);
        file_put_contents($filePath, $content);

        $expected = <<<'PHP'
echo "Humbug!";

PHP;

        $actual = $this->scoper->scope($filePath, $prefix, $patchers);

        $this->assertSame($expected, $actual);
    }

    public function test_does_not_scope_file_if_is_not_a_PHP_file()
    {
        $filePath = 'file.yaml';
        $prefix = 'Humbug';
        $patchers = [create_fake_patcher()];

        $this->decoratedScoperProphecy
            ->scope($filePath, $prefix, $patchers)
            ->willReturn(
                $expected = 'Scoped content'
            )
        ;

        $scoper = new PhpScoper(
            new FakeParser(),
            $this->decoratedScoper
        );

        $actual = $scoper->scope($filePath, $prefix, $patchers);

        $this->assertSame($expected, $actual);

        $this->decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_can_scope_PHP_binary_files()
    {
        $prefix = 'Humbug';
        $filePath = escape_path($this->tmp.'/hello');
        $patchers = [create_fake_patcher()];

        $content = <<<'PHP'
#!/usr/bin/env php
<?php

echo "Hello world";
PHP;

        touch($filePath);
        file_put_contents($filePath, $content);

        $expected = <<<'PHP'
#!/usr/bin/env php
<?php 
echo "Hello world";

PHP;

        $actual = $this->scoper->scope($filePath, $prefix, $patchers);

        $this->assertSame($expected, $actual);
    }

    public function test_does_not_scope_a_non_PHP_binary_files()
    {
        $prefix = 'Humbug';

        $filePath = escape_path($this->tmp.'/hello');

        $patchers = [create_fake_patcher()];

        $content = <<<'PHP'
#!/usr/bin/env bash
<?php

echo "Hello world";
PHP;

        touch($filePath);
        file_put_contents($filePath, $content);

        $this->decoratedScoperProphecy
            ->scope($filePath, $prefix, $patchers)
            ->willReturn(
                $expected = 'Scoped content'
            )
        ;

        $scoper = new PhpScoper(
            new FakeParser(),
            $this->decoratedScoper
        );

        $actual = $scoper->scope($filePath, $prefix, $patchers);

        $this->assertSame($expected, $actual);

        $this->decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_cannot_scope_an_invalid_PHP_file()
    {
        $filePath = escape_path($this->tmp.'/invalid-file.php');
        $content = <<<'PHP'
<?php

$class = ;

PHP;

        touch($filePath);
        file_put_contents($filePath, $content);

        $prefix = 'Humbug';
        $patchers = [create_fake_patcher()];

        try {
            $this->scoper->scope($filePath, $prefix, $patchers);

            $this->fail('Expected exception to have been thrown.');
        } catch (PhpParserError $error) {
            $this->assertEquals(
                'Syntax error, unexpected \';\' on line 3',
                $error->getMessage()
            );
            $this->assertSame(0, $error->getCode());
            $this->assertNull($error->getPrevious());
        }
    }

    /**
     * @dataProvider provideValidFiles
     */
    public function test_can_scope_valid_files(string $content, string $prefix, string $expected)
    {
        $filePath = escape_path($this->tmp.'/file.php');

        touch($filePath);
        file_put_contents($filePath, $content);

        $patchers = [create_fake_patcher()];

        $actual = $this->scoper->scope($filePath, $prefix, $patchers);

        $this->assertSame($expected, $actual);
    }

    public function provideValidFiles()
    {
        //
        // Namespace declaration
        //
        // ============================

        yield '[Namespace declaration] no declaration' => [
            <<<'PHP'
<?php

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php



PHP
        ];

        yield '[Namespace declaration] simple declaration' => [
            <<<'PHP'
<?php

namespace MyNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

namespace Humbug\MyNamespace;


PHP
        ];

        yield '[Namespace declaration] simple declaration with brackets' => [
        <<<'PHP'
<?php

namespace MyNamespace {
}

PHP
        ,
        'Humbug',
        <<<'PHP'
<?php

namespace Humbug\MyNamespace;


PHP
    ];

        yield '[Namespace declaration] prefixed simple declaration' => [
            <<<'PHP'
<?php

namespace Humbug\MyNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

namespace Humbug\MyNamespace;


PHP
        ];

        yield '[Namespace declaration] multiple declarations' => [
            <<<'PHP'
<?php

namespace MyNamespace1;
namespace MyNamespace2;
namespace MyNamespace3;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

namespace Humbug\MyNamespace1;

namespace Humbug\MyNamespace2;

namespace Humbug\MyNamespace3;


PHP
        ];

        yield '[Namespace declaration] multiple declarations with prefixed ones' => [
            <<<'PHP'
<?php

namespace MyNamespace1;
namespace Humbug\MyNamespace2;
namespace MyNamespace3;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

namespace Humbug\MyNamespace1;

namespace Humbug\MyNamespace2;

namespace Humbug\MyNamespace3;


PHP
        ];

        yield 'Namespace declaration] root namespace declaration' => [
            <<<'PHP'
<?php

namespace {
}

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

namespace {
}

PHP
        ];

        //
        // Use statement for a class
        //
        // ============================

        yield '[Use statement for a class] simple statement' => [
            <<<'PHP'
<?php

use Bar\FooNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\Bar\FooNamespace;

PHP
        ];

        yield '[Use statement for a class] prefixed statement' => [
            <<<'PHP'
<?php

use Humbug\FooNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\FooNamespace;

PHP
        ];

        yield '[Use statement for a class] simple statement with an alias' => [
            <<<'PHP'
<?php

use FooNamespace\X as XAlias;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\FooNamespace\X as XAlias;

PHP
        ];

        yield '[Use statement for a class] prefixed statement with an alias' => [
            <<<'PHP'
<?php

use Humbug\FooNamespace\X as XAlias;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\FooNamespace\X as XAlias;

PHP
        ];

        yield '[Use statement for a class] multiple statement' => [
            <<<'PHP'
<?php

use Bar\FooNamespace;
use Bar\BarNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\Bar\FooNamespace;
use Humbug\Bar\BarNamespace;

PHP
        ];

        yield '[Use statement for a class] multiple statement with prefixed ones' => [
            <<<'PHP'
<?php

use Bar\FooNamespace;
use Humbug\BarNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\Bar\FooNamespace;
use Humbug\BarNamespace;

PHP
        ];

        yield '[Use statement for a class] multiple statement with aliases' => [
            <<<'PHP'
<?php

use FooNamespace\X as XAlias;
use BarNamespace\Y;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\FooNamespace\X as XAlias;
use Humbug\BarNamespace\Y;

PHP
        ];

        yield '[Use statement for a class] multiple statement in-lined' => [
            <<<'PHP'
<?php

use Bar\FooNamespace, Bar\BarNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\Bar\FooNamespace, Humbug\Bar\BarNamespace;

PHP
        ];

        yield '[Use statement for a class] multiple statement in-lined with in-lined ones' => [
            <<<'PHP'
<?php

use Bar\FooNamespace, Humbug\BarNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\Bar\FooNamespace, Humbug\BarNamespace;

PHP
        ];

        yield '[Use statement for a class] grouped use statements' => [
            <<<'PHP'
<?php

use FooNamespace\{X, Y, Z};

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\FooNamespace\{X, Y, Z};

PHP
        ];

        yield '[Use statement for a class] grouped use statements with prefixed ones' => [
            <<<'PHP'
<?php

use FooNamespace\{X, Y, Z};
use Humbug\BarNamespace\{X, Y, Z};
use BazNamespace\{X, Y, Z};

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\FooNamespace\{X, Y, Z};
use Humbug\BarNamespace\{X, Y, Z};
use Humbug\BazNamespace\{X, Y, Z};

PHP
        ];

        yield '[Use statement for a class] multiple declaration with collision' => [
            <<<'PHP'
<?php

use Bar\FooNamespace;
use Humbug\Bar\FooNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\Bar\FooNamespace;
use Humbug\Bar\FooNamespace;

PHP
        ];

        yield '[Use statement for a class] composer use statement' => [
            <<<'PHP'
<?php

use Composer\Unknown;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Composer\Unknown;

PHP
        ];

        //
        // Use statement for a function
        //
        // ============================

        yield '[Use statement for a function] simple statement' => [
            <<<'PHP'
<?php

use function FooNamespace\foo;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use function Humbug\FooNamespace\foo;

PHP
        ];

        yield '[Use statement for a function] prefixed statement' => [
            <<<'PHP'
<?php

use function Humbug\FooNamespace\foo;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use function Humbug\FooNamespace\foo;

PHP
        ];

        //
        // Use statement for a constant
        //
        // ============================

        yield '[Use statement for a function] simple statement' => [
            <<<'PHP'
<?php

use const FooNamespace\FOO;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use const Humbug\FooNamespace\FOO;

PHP
        ];

        yield '[Use statement for a function] prefixed statement' => [
            <<<'PHP'
<?php

use const Humbug\FooNamespace\FOO;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use const Humbug\FooNamespace\FOO;

PHP
        ];

        //
        // FQN usage for a class
        //
        // ====================

        yield '[FQN usage for a name] fully qualified class' => [
            <<<'PHP'
<?php

new \Foo\Bar();

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

new \Humbug\Foo\Bar();

PHP
        ];

        //
        // FQN usage for a method
        //
        // ====================

        yield '[FQN usage for a name] fully qualified method' => [
            <<<'PHP'
<?php

\PHPUnit\TextUI\Command::main();

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

\Humbug\PHPUnit\TextUI\Command::main();

PHP
        ];

        //
        // Single part global namespace reference
        //
        // ======================================

        yield '[Single part global namespace reference] a simple use statement' => [
            <<<'PHP'
<?php

use Closure;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Closure;

PHP
        ];

        yield '[Single part global namespace reference] a full qualified class reference' => [
            <<<'PHP'
<?php

$foo = new \Closure();

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

$foo = new \Closure();

PHP
        ];

        yield '[Single part global namespace reference] a non-FQN class reference' => [
            <<<'PHP'
<?php

$foo = new Closure();

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

$foo = new Closure();

PHP
        ];

        yield '[Single part global namespace reference] a fully qualified typehint' => [
            <<<'PHP'
<?php

function foo(\Closure $bar)
{
}

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

function foo(\Closure $bar)
{
}

PHP
        ];

        yield '[Single part global namespace reference] a non-FQN typehint' => [
            <<<'PHP'
<?php

function foo(Closure $bar)
{
}

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

function foo(Closure $bar)
{
}

PHP
        ];

        yield '[Single part global namespace reference] a fully qualified constant' => [
            <<<'PHP'
<?php

$a = \PHP_EOL;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

$a = \PHP_EOL;

PHP
        ];

        yield '[Single part global namespace reference] a non-FQN constant' => [
            <<<'PHP'
<?php

$a = PHP_EOL;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

$a = PHP_EOL;

PHP
        ];

        yield '[Single part global namespace reference] a fully qualified function' => [
            <<<'PHP'
<?php

\var_dump(1);

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

\var_dump(1);

PHP
        ];

        yield '[Single part global namespace reference] a fully qualified return type' => [
            <<<'PHP'
<?php

function foo($bar) : \Closure
{
}

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

function foo($bar) : \Closure
{
}

PHP
        ];

        yield '[Single part global namespace reference] a non-FQN return type' => [
            <<<'PHP'
<?php

function foo($bar) : Closure
{
}

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

function foo($bar) : Closure
{
}

PHP
        ];

        yield '[Single part global namespace reference] an aliased root namespace' => [
            <<<'PHP'
<?php

use Foo as Bar;
new Bar\Baz();

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\Foo as Bar;
new Bar\Baz();

PHP
        ];

        //
        // Function parameters
        //
        // ====================

        yield '[Function parameter] class_exists' => [
            <<<'PHP'
<?php

class_exists('Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Symfony\\Component\\Yaml\\Yaml');

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

class_exists('Humbug\\Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

PHP
        ];

        yield '[Function parameter] interface_exists' => [
            <<<'PHP'
<?php

interface_exists('Foo\\Bar');

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

interface_exists('Humbug\\Foo\\Bar');

PHP
        ];

        yield '[Function parameter] class_exists with concat strings' => [
            <<<'PHP'
<?php

class_exists('Symfony\\Component' . '\\Yaml\\Yaml');

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

class_exists('Symfony\\Component' . '\\Yaml\\Yaml');

PHP
        ];

        yield '[Function parameter] class_exists with constant' => [
            <<<'PHP'
<?php

class_exists(\Symfony\Component\Yaml\Yaml::class);

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

class_exists(\Humbug\Symfony\Component\Yaml\Yaml::class);

PHP
        ];

        yield '[Function parameter] class_exists with variable (no change)' => [
            <<<'PHP'
<?php

$x = '\\Symfony\\Component\\Yaml\\Yaml';
class_exists($x);

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

$x = '\\Symfony\\Component\\Yaml\\Yaml';
class_exists($x);

PHP
        ];

        yield '[Function parameter] Do not prepend global namespace' => [
            <<<'PHP'
<?php

class_exists('Closure');
class_exists('\\Closure');

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

class_exists('Closure');
class_exists('\\Closure');

PHP
        ];
    }
}
