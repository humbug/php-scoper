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

namespace Humbug\PhpScoper\PhpParser;

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\PhpParser\NodeVisitor\NamespaceStmt\NamespaceStmtCollection;
use Humbug\PhpScoper\PhpParser\NodeVisitor\UseStmt\UseStmtCollection;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\Reflector;
use LogicException;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeTraverser as PhpParserNodeTraverser;
use PHPUnit\Framework\TestCase;
use function Humbug\PhpScoper\create_parser;
use function sprintf;

/**
 * @covers \Humbug\PhpScoper\PhpParser\UseStmtName
 *
 * @internal
 */
final class UseStmtNameTest extends TestCase
{
    /**
     * @dataProvider provideUseStmtName
     */
    public function test_it_can_tell_whether_it_contains_a_name(
        Name $useStmt,
        Name $name,
        bool $expected
    ): void {
        $useStmtName = new UseStmtName($useStmt);

        $actual = $useStmtName->contains($name);

        self::assertSame($expected, $actual);
    }

    public static function provideUseStmtName(): iterable
    {
        yield 'symbol is NOT imported with use stmt' => [
            new Name('Acme\Foo\Bar'),
            new Name('Acme\Foo'),
            false,
        ];

        yield 'symbol is imported with use stmt (exact match)' => [
            new Name('Acme\Foo\Bar'),
            new Name('Acme\Foo\Bar'),
            true,
        ];

        yield 'symbol is imported with use stmt (1)' => [
            new Name('Acme\Foo\Bar'),
            new Name('Acme\Foo\Bar\Baz'),
            true,
        ];

        yield 'symbol is imported with use stmt (2)' => [
            new Name('Acme'),
            new Name('Acme\Foo'),
            true,
        ];
    }

    /**
     * @dataProvider provideUseStmtAliasType
     */
    public function test_it_can_retrieve_its_use_stmt_alias_and_type(
        Name $name,
        ?string $expectedAlias,
        int $expectedType
    ): void {
        $useStmtName = new UseStmtName($name);

        [$alias, $type] = $useStmtName->getUseStmtAliasAndType();

        self::assertSame($expectedAlias, $alias);
        self::assertSame($expectedType, $type);
    }

    public static function provideUseStmtAliasType(): iterable
    {
        foreach (self::provideUseStmtCodeSamples() as $title => [$php, $expectedAlias, $expectedType]) {
            yield $title => [
                self::parseUseStmtName($php),
                $expectedAlias,
                $expectedType,
            ];
        }
    }

    private static function provideUseStmtCodeSamples(): iterable
    {
        yield 'class use statement' => [
            <<<'PHP'
                <?php

                use Acme\Foo;
                PHP,
            null,
            Use_::TYPE_NORMAL,
        ];

        yield 'class use statement with alias' => [
            <<<'PHP'
                <?php

                use Acme\Foo as Bar;
                PHP,
            'Bar',
            Use_::TYPE_NORMAL,
        ];

        yield 'function use statement' => [
            <<<'PHP'
                <?php

                use function Acme\Foo;
                PHP,
            null,
            Use_::TYPE_FUNCTION,
        ];

        yield 'function use statement with alias' => [
            <<<'PHP'
                <?php

                use function Acme\Foo as Bar;
                PHP,
            'Bar',
            Use_::TYPE_FUNCTION,
        ];

        yield 'constant use statement' => [
            <<<'PHP'
                <?php

                use const Acme\FOO;
                PHP,
            null,
            Use_::TYPE_CONSTANT,
        ];

        yield 'constant use statement with alias' => [
            <<<'PHP'
                <?php

                use const Acme\FOO as BAR;
                PHP,
            'BAR',
            Use_::TYPE_CONSTANT,
        ];

        yield 'grouped statement' => [
            <<<'PHP'
                <?php

                use Acme\{Foo, Bar};
                PHP,
            null,
            Use_::TYPE_UNKNOWN,
        ];
    }

    private static function parseUseStmtName(string $php): Name
    {
        $parser = create_parser();
        $namespaceStatements = new NamespaceStmtCollection();
        $useStatements = new UseStmtCollection();

        $traverser = new NodeTraverser(
            new PhpParserNodeTraverser(),
        );

        $traverser->addVisitor(new NodeVisitor\AttributeAppender\ParentNodeAppender());
        $traverser->addVisitor(
            new NodeVisitor\NamespaceStmt\NamespaceStmtPrefixer(
                'Humbug',
                new EnrichedReflector(
                    Reflector::createWithPhpStormStubs(),
                    SymbolsConfiguration::create(),
                ),
                $namespaceStatements,
            ),
        );
        $traverser->addVisitor(
            new NodeVisitor\UseStmt\UseStmtCollector(
                $namespaceStatements,
                $useStatements,
            ),
        );

        $statements = $parser->parse($php);

        self::assertNotNull($statements);

        $traverser->traverse($statements);

        foreach ($useStatements as $useStmts) {
            /** @var Use_[] $useStmts */
            foreach ($useStmts as $useStmt) {
                foreach ($useStmt->uses as $useUse) {
                    return $useUse->name;
                }
            }
        }

        throw new LogicException(
            sprintf(
                'No use statement could be find for the sample given: %s',
                $php,
            ),
        );
    }
}
