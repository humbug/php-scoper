<?php

/*
 * This file is part of the webmozart/php-scoper package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Humbug\PhpScoper;

use PhpParser\Error;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Throwable\Exception\ParsingException;

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
        $this->scoper = new Scoper((new ParserFactory())->create(ParserFactory::PREFER_PHP7));
    }

    public function test_cannot_scope_an_invalid_PHP_file()
    {
        $content = <<<'PHP'
<?php

$class = ;

PHP;
        $prefix = 'MyPrefix';

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
        yield 'simple namespace' => [
            <<<'PHP'
<?php

namespace MyNamespace;

PHP
            ,
            'MyPrefix',
            <<<'PHP'
<?php

namespace MyPrefix\MyNamespace;


PHP
        ];

        // ============================

        yield 'use namespace' => [
            <<<'PHP'
<?php

use AnotherNamespace;

PHP
            ,
            'MyPrefix',
            <<<'PHP'
<?php

use MyPrefix\AnotherNamespace;

PHP
        ];

        // ============================

        yield 'FQ namespace used' => [
            <<<'PHP'
<?php

$class = new \stdClass();

PHP
            ,
            'MyPrefix',
            <<<'PHP'
<?php

$class = new MyPrefix\stdClass();

PHP
        ];
    }
}
