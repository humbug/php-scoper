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

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\PhpParser\FakeParser;
use Humbug\PhpScoper\PhpParser\FakePrinter;
use Humbug\PhpScoper\PhpParser\Printer\Printer;
use Humbug\PhpScoper\PhpParser\Printer\StandardPrinter;
use Humbug\PhpScoper\PhpParser\TraverserFactory;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\Reflector;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use LogicException;
use PhpParser\Error as PhpParserError;
use PhpParser\Lexer;
use PhpParser\Node\Name;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use function Humbug\PhpScoper\create_parser;
use function is_a;

/**
 * @internal
 */
class PhpScoperTest extends TestCase
{
    use ProphecyTrait;

    private const PREFIX = 'Humbug';

    private Scoper $scoper;

    /**
     * @var ObjectProphecy<Scoper>
     */
    private ObjectProphecy $decoratedScoperProphecy;

    private Scoper $decoratedScoper;

    /**
     * @var ObjectProphecy<TraverserFactory>
     */
    private ObjectProphecy $traverserFactoryProphecy;

    private TraverserFactory $traverserFactory;

    /**
     * @var ObjectProphecy<Parser>
     */
    private ObjectProphecy $parserProphecy;

    private Parser $parser;

    private SymbolsRegistry $symbolsRegistry;

    private Printer $printer;

    private Lexer $lexer;

    protected function setUp(): void
    {
        $this->decoratedScoperProphecy = $this->prophesize(Scoper::class);
        $this->decoratedScoper = $this->decoratedScoperProphecy->reveal();

        $this->traverserFactoryProphecy = $this->prophesize(TraverserFactory::class);
        $this->traverserFactory = $this->traverserFactoryProphecy->reveal();

        $this->parserProphecy = $this->prophesize(Parser::class);
        $this->parser = $this->parserProphecy->reveal();

        $this->symbolsRegistry = new SymbolsRegistry();
        $this->printer = new StandardPrinter(new Standard());

        $lexerProphecy = $this->prophesize(Lexer::class);
        $lexerProphecy->getTokens()->willReturn([]);

        $this->lexer = $lexerProphecy->reveal();

        $this->scoper = new PhpScoper(
            create_parser(),
            new FakeScoper(),
            new TraverserFactory(
                new EnrichedReflector(
                    Reflector::createEmpty(),
                    SymbolsConfiguration::create(),
                ),
                self::PREFIX,
                $this->symbolsRegistry,
            ),
            $this->printer,
            $this->lexer,
        );
    }

    public function test_is_a__scoper(): void
    {
        self::assertTrue(is_a(PhpScoper::class, Scoper::class, true));
    }

    public function test_can_scope_a__ph_p_file(): void
    {
        $filePath = 'file.php';

        $contents = <<<'PHP'
            <?php

            echo "Humbug!";
            PHP;

        $expected = <<<'PHP'
            <?php

            namespace Humbug;

            echo "Humbug!";

            PHP;

        $actual = $this->scoper->scope($filePath, $contents);

        self::assertSame($expected, $actual);
    }

    public function test_does_not_scope_file_if_is_not_a__ph_p_file(): void
    {
        $filePath = 'file.yaml';
        $fileContents = '';

        $this->decoratedScoperProphecy
            ->scope($filePath, $fileContents)
            ->willReturn($expected = 'Scoped content');

        $this->traverserFactoryProphecy
            ->create(Argument::cetera())
            ->willThrow(new LogicException('Unexpected call.'));

        $scoper = new PhpScoper(
            new FakeParser(),
            $this->decoratedScoper,
            $this->traverserFactory,
            new FakePrinter(),
            $this->lexer,
        );

        $actual = $scoper->scope($filePath, $fileContents);

        self::assertSame($expected, $actual);

        $this->decoratedScoperProphecy
            ->scope(Argument::cetera())
            ->shouldHaveBeenCalledTimes(1);
    }

    public function test_can_scope_a__ph_p_file_with_the_wrong_extension(): void
    {
        $filePath = 'file';

        $contents = <<<'PHP'
            <?php

            echo "Humbug!";

            PHP;

        $expected = <<<'PHP'
            <?php

            namespace Humbug;

            echo "Humbug!";

            PHP;

        $actual = $this->scoper->scope($filePath, $contents);

        self::assertSame($expected, $actual);
    }

    public function test_can_scope__ph_p_executable_files(): void
    {
        $filePath = 'hello';

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

        $actual = $this->scoper->scope($filePath, $contents);

        self::assertSame($expected, $actual);
    }

    public function test_does_not_scope_a_non__ph_p_executable_files(): void
    {
        $filePath = 'hello';

        $contents = <<<'PHP'
            #!/usr/bin/env bash
            <?php

            echo "Hello world";
            PHP;

        $this->decoratedScoperProphecy
            ->scope($filePath, $contents)
            ->willReturn($expected = 'Scoped content');

        $this->traverserFactoryProphecy
            ->create(Argument::cetera())
            ->willThrow(new LogicException('Unexpected call.'));

        $scoper = new PhpScoper(
            new FakeParser(),
            $this->decoratedScoper,
            $this->traverserFactory,
            new FakePrinter(),
            $this->lexer,
        );

        $actual = $scoper->scope($filePath, $contents);

        self::assertSame($expected, $actual);

        $this->decoratedScoperProphecy
            ->scope(Argument::cetera())
            ->shouldHaveBeenCalledTimes(1);
    }

    public function test_cannot_scope_an_invalid__ph_p_file(): void
    {
        $filePath = 'invalid-file.php';
        $contents = <<<'PHP'
            <?php

            $class = ;

            PHP;

        try {
            $this->scoper->scope($filePath, $contents);

            self::fail('Expected exception to have been thrown.');
        } catch (PhpParserError $error) {
            self::assertEquals(
                'Syntax error, unexpected \';\' on line 3',
                $error->getMessage()
            );
            self::assertSame(0, $error->getCode());
            self::assertNull($error->getPrevious());
        }
    }

    public function test_creates_a_new_traverser_for_each_file(): void
    {
        $files = [
            'file1.php' => 'file1',
            'file2.php' => 'file2',
        ];

        $this->decoratedScoperProphecy
            ->scope(Argument::any(), Argument::any())
            ->willReturn('Scoped content');

        $this->parserProphecy
            ->parse('file1')
            ->willReturn($file1Stmts = [
                new Name('file1'),
            ]);
        $this->parserProphecy
            ->parse('file2')
            ->willReturn($file2Stmts = [
                new Name('file2'),
            ]);

        $firstTraverserProphecy = $this->prophesize(NodeTraverserInterface::class);
        $firstTraverserProphecy->traverse($file1Stmts)->willReturn([]);

        $secondTraverserProphecy = $this->prophesize(NodeTraverserInterface::class);
        $secondTraverserProphecy->traverse($file2Stmts)->willReturn([]);

        $i = 0;
        $this->traverserFactoryProphecy
            ->create(
                Argument::that(
                    static function () use (&$i): bool {
                        ++$i;

                        return 1 === $i;
                    }
                ),
            )
            ->willReturn($firstTraverserProphecy->reveal());

        $this->traverserFactoryProphecy
            ->create(
                Argument::that(
                    static function () use (&$i): bool {
                        ++$i;

                        // It is 4 instead of 2 because Prophecy will check all
                        // registered calls even if the first one matches.
                        // So it will call this one too for the first file
                        // hence by the time it is the 2nd call for the 2nd file
                        // we are at the 4th call.
                        return 4 === $i;
                    }
                ),
            )
            ->willReturn($secondTraverserProphecy->reveal());

        $scoper = new PhpScoper(
            $this->parser,
            new FakeScoper(),
            $this->traverserFactory,
            $this->printer,
            $this->lexer,
        );

        foreach ($files as $file => $contents) {
            $scoper->scope($file, $contents);
        }

        $this->parserProphecy
            ->parse(Argument::cetera())
            ->shouldHaveBeenCalledTimes(2);
        $this->traverserFactoryProphecy
            ->create(Argument::cetera())
            ->shouldHaveBeenCalledTimes(2);
        $firstTraverserProphecy
            ->traverse(Argument::cetera())
            ->shouldHaveBeenCalledTimes(1);
        $secondTraverserProphecy
            ->traverse(Argument::cetera())
            ->shouldHaveBeenCalledTimes(1);
    }
}
