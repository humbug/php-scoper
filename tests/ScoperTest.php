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

namespace Humbug\PhpScoper;

use Humbug\PhpScoper\Throwable\Exception\ParsingException;
use PhpParser\Error;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\Scoper
 */
class ScoperTest extends TestCase
{
    /**
     * @var Scoper
     */
    private $scoper;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->scoper = new Scoper(createParser());
    }

    public function test_cannot_scope_an_invalid_PHP_file()
    {
        $content = <<<'PHP'
<?php

$class = ;

PHP;
        $prefix = 'Humbug';

        try {
            $this->scoper->scope($content, $prefix);

            Assert::fail('Expected exception to have been thrown.');
        } catch (ParsingException $exception) {
            $this->assertEquals(
                'Syntax error, unexpected \';\' on line 3',
                $exception->getMessage()
            );
            $this->assertSame(0, $exception->getCode());
            $this->assertInstanceOf(Error::class, $exception->getPrevious());
        }
    }

    /**
     * @dataProvider provideValidFiles
     */
    public function test_can_scope_valid_files(string $content, string $prefix, string $expected)
    {
        $actual = $this->scoper->scope($content, $prefix);

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

use FooNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\FooNamespace;

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

use FooNamespace;
use BarNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\FooNamespace;
use Humbug\BarNamespace;

PHP
        ];

        yield '[Use statement for a class] multiple statement with prefixed ones' => [
            <<<'PHP'
<?php

use FooNamespace;
use Humbug\BarNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\FooNamespace;
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

use FooNamespace, BarNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\FooNamespace, Humbug\BarNamespace;

PHP
        ];

        yield '[Use statement for a class] multiple statement in-lined with in-lined ones' => [
            <<<'PHP'
<?php

use FooNamespace, Humbug\BarNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\FooNamespace, Humbug\BarNamespace;

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

use FooNamespace;
use Humbug\FooNamespace;

PHP
            ,
            'Humbug',
            <<<'PHP'
<?php

use Humbug\FooNamespace;
use Humbug\FooNamespace;

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
    }

    public function testShouldNotScopePhpOrSplReservedClasses()
    {
        $content = file_get_contents(__DIR__.'/Fixtures/reserved_classes.php');
        $this->assertEquals($content, $this->scoper->addNamespacePrefix($content, 'MyPrefix'));
    }
}
