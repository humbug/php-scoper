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

use Generator;
use Humbug\PhpScoper\PhpParser\FakeParser;
use Humbug\PhpScoper\PhpParser\TraverserFactory;
use Humbug\PhpScoper\Reflector;
use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Whitelist;
use LogicException;
use PhpParser\Error as PhpParserError;
use PhpParser\Node\Name;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\StringSourceLocator;
use Symfony\Component\Finder\Finder;
use Throwable;
use UnexpectedValueException;
use function Humbug\PhpScoper\create_fake_patcher;
use function Humbug\PhpScoper\create_parser;
use function implode;
use function sprintf;

class PhpScoperTest extends TestCase
{
    private const SPECS_PATH = __DIR__.'/../../specs';
    private const SECONDARY_SPECS_PATH = __DIR__.'/../../_specs';

    /**
     * @var Scoper
     */
    private $scoper;

    /**
     * @var Scoper|ObjectProphecy
     */
    private $decoratedScoperProphecy;

    /**
     * @var Scoper
     */
    private $decoratedScoper;

    /**
     * @var TraverserFactory|ObjectProphecy
     */
    private $traverserFactoryProphecy;

    /**
     * @var TraverserFactory
     */
    private $traverserFactory;

    /**
     * @var NodeTraverserInterface|ObjectProphecy
     */
    private $traverserProphecy;

    /**
     * @var NodeTraverserInterface
     */
    private $traverser;

    /**
     * @var Parser|ObjectProphecy
     */
    private $parserProphecy;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var ClassReflector|ObjectProphecy
     */
    private $classReflectorProphecy;

    /**
     * @var ClassReflector
     */
    private $classReflector;

    /**
     * @var FunctionReflector|ObjectProphecy
     */
    private $functionReflectorProphecy;

    /**
     * @var FunctionReflector
     */
    private $functionReflector;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->decoratedScoperProphecy = $this->prophesize(Scoper::class);
        $this->decoratedScoper = $this->decoratedScoperProphecy->reveal();

        $this->traverserFactoryProphecy = $this->prophesize(TraverserFactory::class);
        $this->traverserFactory = $this->traverserFactoryProphecy->reveal();

        $this->traverserProphecy = $this->prophesize(NodeTraverserInterface::class);
        $this->traverser = $this->traverserProphecy->reveal();

        $this->parserProphecy = $this->prophesize(Parser::class);
        $this->parser = $this->parserProphecy->reveal();

        $this->classReflectorProphecy = $this->prophesize(ClassReflector::class);
        $this->classReflector = $this->classReflectorProphecy->reveal();

        $this->functionReflectorProphecy = $this->prophesize(FunctionReflector::class);
        $this->functionReflector = $this->functionReflectorProphecy->reveal();

        $this->scoper = new PhpScoper(
            create_parser(),
            new FakeScoper(),
            new TraverserFactory(
                new Reflector(
                    $this->classReflector,
                    $this->functionReflector
                )
            )
        );
    }

    public function test_is_a_Scoper()
    {
        $this->assertTrue(is_a(PhpScoper::class, Scoper::class, true));
    }

    public function test_can_scope_a_PHP_file()
    {
        $prefix = 'Humbug';
        $filePath = 'file.php';
        $patchers = [create_fake_patcher()];
        $whitelist = Whitelist::create(true, 'Foo');

        $contents = <<<'PHP'
<?php

echo "Humbug!";
PHP;

        $expected = <<<'PHP'
<?php

namespace Humbug;

echo "Humbug!";

PHP;

        $actual = $this->scoper->scope($filePath, $contents, $prefix, $patchers, $whitelist);

        $this->assertSame($expected, $actual);
    }

    public function test_does_not_scope_file_if_is_not_a_PHP_file()
    {
        $filePath = 'file.yaml';
        $fileContents = '';
        $prefix = 'Humbug';
        $patchers = [create_fake_patcher()];
        $whitelist = Whitelist::create(true, 'Foo');

        $this->decoratedScoperProphecy
            ->scope($filePath, $fileContents, $prefix, $patchers, $whitelist)
            ->willReturn(
                $expected = 'Scoped content'
            )
        ;

        $this->traverserFactoryProphecy
            ->create(Argument::cetera())
            ->willThrow(new LogicException('Unexpected call.'))
        ;

        $scoper = new PhpScoper(
            new FakeParser(),
            $this->decoratedScoper,
            $this->traverserFactory
        );

        $actual = $scoper->scope($filePath, $fileContents, $prefix, $patchers, $whitelist);

        $this->assertSame($expected, $actual);

        $this->decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_can_scope_a_PHP_file_with_the_wrong_extension()
    {
        $prefix = 'Humbug';
        $filePath = 'file';
        $patchers = [create_fake_patcher()];
        $whitelist = Whitelist::create(true, 'Foo');

        $contents = <<<'PHP'
<?php

echo "Humbug!";

PHP;

        $expected = <<<'PHP'
<?php

namespace Humbug;

echo "Humbug!";

PHP;

        $actual = $this->scoper->scope($filePath, $contents, $prefix, $patchers, $whitelist);

        $this->assertSame($expected, $actual);
    }

    public function test_can_scope_PHP_binary_files()
    {
        $prefix = 'Humbug';
        $filePath = 'hello';
        $patchers = [create_fake_patcher()];
        $whitelist = Whitelist::create(true, 'Foo');

        $contents = <<<'PHP'
#!/usr/bin/env php
<?php

echo "Hello world";
PHP;

        $expected = <<<'PHP'
#!/usr/bin/env php
<?php 
namespace Humbug;

echo "Hello world";

PHP;

        $actual = $this->scoper->scope($filePath, $contents, $prefix, $patchers, $whitelist);

        $this->assertSame($expected, $actual);
    }

    public function test_does_not_scope_a_non_PHP_binary_files()
    {
        $prefix = 'Humbug';

        $filePath = 'hello';

        $patchers = [create_fake_patcher()];

        $whitelist = Whitelist::create(true, 'Foo');

        $contents = <<<'PHP'
#!/usr/bin/env bash
<?php

echo "Hello world";
PHP;

        $this->decoratedScoperProphecy
            ->scope($filePath, $contents, $prefix, $patchers, $whitelist)
            ->willReturn(
                $expected = 'Scoped content'
            )
        ;

        $this->traverserFactoryProphecy
            ->create(Argument::cetera())
            ->willThrow(new LogicException('Unexpected call.'))
        ;

        $scoper = new PhpScoper(
            new FakeParser(),
            $this->decoratedScoper,
            $this->traverserFactory
        );

        $actual = $scoper->scope($filePath, $contents, $prefix, $patchers, $whitelist);

        $this->assertSame($expected, $actual);

        $this->decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_cannot_scope_an_invalid_PHP_file()
    {
        $filePath = 'invalid-file.php';
        $contents = <<<'PHP'
<?php

$class = ;

PHP;

        $prefix = 'Humbug';
        $patchers = [create_fake_patcher()];
        $whitelist = Whitelist::create(true, 'Foo');

        try {
            $this->scoper->scope($filePath, $contents, $prefix, $patchers, $whitelist);

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

    public function test_creates_a_new_traverser_for_each_file()
    {
        $files = [
            'file1.php' => 'file1',
            'file2.php' => 'file2',
        ];

        $prefix = 'Humbug';
        $patchers = [create_fake_patcher()];
        $whitelist = Whitelist::create(true, 'Foo');

        $this->decoratedScoperProphecy
            ->scope(Argument::any(), Argument::any(), $prefix, $patchers, $whitelist)
            ->willReturn(
                $expected = 'Scoped content'
            )
        ;

        $this->parserProphecy
            ->parse('file1')
            ->willReturn($file1Stmts = [
                new Name('file1'),
            ])
        ;
        $this->parserProphecy
            ->parse('file2')
            ->willReturn($file2Stmts = [
                new Name('file2'),
            ])
        ;

        /** @var NodeTraverserInterface|ObjectProphecy $firstTraverserProphecy */
        $firstTraverserProphecy = $this->prophesize(NodeTraverserInterface::class);
        /** @var NodeTraverserInterface $firstTraverser */
        $firstTraverser = $firstTraverserProphecy->reveal();

        /** @var NodeTraverserInterface|ObjectProphecy $secondTraverserProphecy */
        $secondTraverserProphecy = $this->prophesize(NodeTraverserInterface::class);
        /** @var NodeTraverserInterface $secondTraverser */
        $secondTraverser = $secondTraverserProphecy->reveal();

        $i = 0;
        $this->traverserFactoryProphecy
            ->create($prefix, $whitelist, Argument::that(
                function (...$args) use (&$i): bool {
                    ++$i;

                    return 1 === $i;
                }
            ))
            ->willReturn($firstTraverser)
        ;
        $this->traverserFactoryProphecy
            ->create($prefix, $whitelist, Argument::that(
                function (...$args) use (&$i): bool {
                    ++$i;

                    return 4 === $i;
                }
            ))
            ->willReturn($secondTraverser)
        ;

        $firstTraverserProphecy->traverse($file1Stmts)->willReturn([]);
        $secondTraverserProphecy->traverse($file2Stmts)->willReturn([]);

        $scoper = new PhpScoper(
            $this->parser,
            new FakeScoper(),
            $this->traverserFactory
        );

        foreach ($files as $file => $contents) {
            $scoper->scope($file, $contents, $prefix, $patchers, $whitelist);
        }

        $this->parserProphecy->parse(Argument::cetera())->shouldHaveBeenCalledTimes(2);
        $this->traverserFactoryProphecy->create(Argument::cetera())->shouldHaveBeenCalledTimes(2);
        $firstTraverserProphecy->traverse(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $secondTraverserProphecy->traverse(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    /**
     * @dataProvider provideValidFiles
     */
    public function test_can_scope_valid_files(string $spec, string $contents, string $prefix, Whitelist $whitelist, ?string $expected)
    {
        $filePath = 'file.php';

        $patchers = [create_fake_patcher()];

        $astLocator = (new BetterReflection())->astLocator();

        $sourceLocator = new AggregateSourceLocator([
            new StringSourceLocator($contents, $astLocator),
            new PhpInternalSourceLocator($astLocator),
        ]);

        $classReflector = new ClassReflector($sourceLocator);

        $this->scoper = new PhpScoper(
            create_parser(),
            new FakeScoper(),
            new TraverserFactory(
                new Reflector(
                    $classReflector,
                    new FunctionReflector($sourceLocator, $classReflector)
                )
            )
        );

        try {
            $actual = $this->scoper->scope($filePath, $contents, $prefix, $patchers, $whitelist);

            if (null === $expected) {
                $this->fail('Expected exception to be thrown.');
            }
        } catch (UnexpectedValueException $exception) {
            if (null !== $expected) {
                throw $exception;
            }

            $this->assertTrue(true);

            return;
        }

        $formattedWhitelist = 0 === count($whitelist)
            ? '[]'
            : sprintf('[%s]', implode(', ', $whitelist->toArray()))
        ;

        $formattedWhitelistGlobalConstants = $whitelist->whitelistGlobalConstants() ? 'true' : 'false';

        $titleSeparator = str_repeat(
            '=',
            min(
                strlen($spec),
                80
            )
        );

        $this->assertSame(
            $expected,
            $actual,
            <<<OUTPUT
$titleSeparator
SPECIFICATION
$titleSeparator
$spec

$titleSeparator
INPUT
whitelist: $formattedWhitelist
whitelist global constants: $formattedWhitelistGlobalConstants
$titleSeparator
$contents

$titleSeparator
EXPECTED
$titleSeparator
$expected

$titleSeparator
ACTUAL
$titleSeparator
$actual

-------------------------------------------------------------------------------
OUTPUT
        );
    }

    /**
     * This test is to ensure no file is left in _specs for the CI. It is fine otherwise for this test to fail locally
     * when developing something.
     */
    public function test_it_uses_the_right_specs_directory()
    {
        $files = (new Finder())->files()->in(self::SECONDARY_SPECS_PATH);

        $this->assertCount(0, $files);
    }

    public function provideValidFiles()
    {
        $files = (new Finder())->files()->in(self::SECONDARY_SPECS_PATH);

        if (0 === count($files)) {
            $files = (new Finder())->files()->in(self::SPECS_PATH);
        }

        $files->sortByName();

        foreach ($files as $file) {
            try {
                $fixtures = include $file;

                $meta = $fixtures['meta'];
                unset($fixtures['meta']);

                foreach ($fixtures as $fixtureTitle => $fixtureSet) {
                    yield $this->parseSpecFile($meta, $fixtureTitle, $fixtureSet)->current();
                }
            } catch (Throwable $throwable) {
                $this->fail(
                    sprintf(
                        'An error occurred while parsing the file "%s": %s',
                        $file,
                        $throwable->getMessage()
                    )
                );
            }
        }
    }

    /**
     * @param array        $meta
     * @param string|int   $fixtureTitle
     * @param string|array $fixtureSet
     *
     * @return Generator
     */
    private function parseSpecFile(array $meta, $fixtureTitle, $fixtureSet): Generator
    {
        $spec = sprintf(
            '[%s] %s',
            $meta['title'],
            isset($fixtureSet['spec']) ? $fixtureSet['spec'] : $fixtureTitle
        );

        $payload = is_string($fixtureSet) ? $fixtureSet : $fixtureSet['payload'];

        $payloadParts = preg_split("/\n----(?:\n|$)/", $payload);

        yield [
            $spec,
            $payloadParts[0],   // Input
            $fixtureSet['prefix'] ?? $meta['prefix'],
            Whitelist::create(
                $fixtureSet['whitelist-global-constants'] ?? $meta['whitelist-global-constants'],
                ...($fixtureSet['whitelist'] ?? $meta['whitelist'])
            ),
            '' === $payloadParts[1] ? null : $payloadParts[1],   // Expected output
        ];
    }
}
