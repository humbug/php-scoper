<?php

/*
 * This file is part of the webmozart/php-scoper package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\PhpScoper\Tests;

use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Webmozart\PhpScoper\Scoper;

/**
 * @author Matthieu Auger <mail@matthieuauger.com>
 */
class ScoperTest extends TestCase
{
    /**
     * @var Scoper
     */
    private $scoper;

    public function setUp()
    {
        $this->scoper = new Scoper((new ParserFactory())->create(ParserFactory::PREFER_PHP7));
    }

    /**
     * @expectedException \Webmozart\PhpScoper\Exception\ParsingException
     */
    public function testScopeIncorrectFile()
    {
        $content = <<<'EOF'
<?php

$class = ;

EOF;

        $this->scoper->addNamespacePrefix($content, 'MyPrefix');
    }

    public function testScopeNamespace()
    {
        $content = <<<'EOF'
<?php

namespace MyNamespace;

EOF;
        $expected = <<<EOF
<?php

namespace MyPrefix\MyNamespace;


EOF;

        $this->assertEquals($expected, $this->scoper->addNamespacePrefix($content, 'MyPrefix'));
    }

    public function testScopeUseNamespace()
    {
        $content = <<<'EOF'
<?php

use AnotherNamespace;

EOF;
        $expected = <<<EOF
<?php

use MyPrefix\AnotherNamespace;

EOF;

        $this->assertEquals($expected, $this->scoper->addNamespacePrefix($content, 'MyPrefix'));
    }

    public function testShouldScopeMNamespacedFunctionUse()
    {
        $content = <<<EOF
<?php

use function FooNamespace\\foo;

EOF;
        $expected = <<<EOF
<?php

use function MyPrefix\FooNamespace\\foo;

EOF;

        $this->assertEquals($expected, $r = $this->scoper->addNamespacePrefix($content, 'MyPrefix'), $r);
    }

    public function testShouldScopeNamespacedConstantUse()
    {
        $content = <<<EOF
<?php

use const FooNamespace\FOO;

EOF;
        $expected = <<<EOF
<?php

use const MyPrefix\FooNamespace\FOO;

EOF;

        $this->assertEquals($expected, $this->scoper->addNamespacePrefix($content, 'MyPrefix'));
    }

    public function testShouldScopeMultipleNamespaceUsesInOneStatement()
    {
        $content = <<<EOF
<?php

use FooNamespace as Foo, BarNamespace as Bar;

EOF;
        $expected = <<<EOF
<?php

use MyPrefix\FooNamespace as Foo, MyPrefix\BarNamespace as Bar;

EOF;

        $this->assertEquals($expected, $this->scoper->addNamespacePrefix($content, 'MyPrefix'));
    }

    public function testShouldScopeMultipleNamespacedFunctionUsesInOneStatement()
    {
        $content = <<<EOF
<?php

use function FooNamespace\\foo, BarNamespace\bar;

EOF;
        $expected = <<<EOF
<?php

use function MyPrefix\FooNamespace\\foo, MyPrefix\BarNamespace\bar;

EOF;

        $this->assertEquals($expected, $this->scoper->addNamespacePrefix($content, 'MyPrefix'));
    }

    public function testShouldScopeMultipleNamespacedConstantUsesInOneStatement()
    {
        $content = <<<EOF
<?php

use const FooNamespace\FOO, BarNamespace\BAR;

EOF;
        $expected = <<<EOF
<?php

use const MyPrefix\FooNamespace\FOO, MyPrefix\BarNamespace\BAR;

EOF;

        $this->assertEquals($expected, $this->scoper->addNamespacePrefix($content, 'MyPrefix'));
    }

    public function testShouldScopeMultipleSubNamespaceUsesInOneStatement()
    {
        $content = <<<EOF
<?php

use AnotherNamespace\{Foo,Bar,Baz};

EOF;
        $expected = <<<EOF
<?php

use MyPrefix\AnotherNamespace\{Foo,Bar,Baz};

EOF;

        $this->assertEquals($expected, $this->scoper->addNamespacePrefix($content, 'MyPrefix'));
    }

    public function testShouldScopeMultipleSubNamespacedFunctionUsesInOneStatement()
    {
        $content = <<<EOF
<?php

use function AnotherNamespace\{foo,bar,baz};

EOF;
        $expected = <<<EOF
<?php

use function MyPrefix\AnotherNamespace\{foo,bar,baz};

EOF;

        $this->assertEquals($expected, $this->scoper->addNamespacePrefix($content, 'MyPrefix'));
    }

    public function testShouldScopeMultipleSubNamespacedConstantUsesInOneStatement()
    {
        $content = <<<EOF
<?php

use const AnotherNamespace\{FOO,BAR,BAZ};

EOF;
        $expected = <<<EOF
<?php

use const MyPrefix\AnotherNamespace\{FOO,BAR,BAZ};

EOF;

        $this->assertEquals($expected, $this->scoper->addNamespacePrefix($content, 'MyPrefix'));
    }

    public function testScopeFullyQualifiedNamespaceUse()
    {
        $content = <<<EOF
<?php

\$class = new \stdClass();

EOF;
        $expected = <<<EOF
<?php

\$class = new MyPrefix\stdClass();

EOF;

        $this->assertEquals($expected, $this->scoper->addNamespacePrefix($content, 'MyPrefix'));
    }
}
