<?php

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Humbug\PhpScoper\Console\Command;

use Fidry\Console\Application\SymfonyApplication;
use Fidry\Console\DisplayNormalizer;
use Humbug\PhpScoper\Console\Application;
use Humbug\PhpScoper\Container;
use Humbug\PhpScoper\FileSystemTestCase;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @covers \Humbug\PhpScoper\Console\Command\InspectSymbolCommand
 *
 * @group integration
 *
 * @internal
 */
class InspectSymbolCommandIntegrationTest extends FileSystemTestCase
{
    private const STATIC_CONTENT = <<<'EOL'

        Internal (configured via the `excluded-*` settings) are treated as PHP native symbols, i.e. will remain untouched.
        Exposed symbols (configured via the `expose-*` settings) will be prefixed but aliased to its original symbol.
        If a symbol is neither internal or exposed, it will be prefixed and not aliased

        For more information, see:
         * Doc link for excluded symbols
         * Doc link for exposed symbols


        EOL;

    private ApplicationTester $appTester;

    protected function setUp(): void
    {
        parent::setUp();

        $application = new Application(
            new Container(),
            'TestVersion',
            '28/01/2020',
            false,
            false,
        );

        $this->appTester = new ApplicationTester(
            new SymfonyApplication($application),
        );
    }

    public function test_inspects_the_given_symbol(): void
    {
        $input = [
            'inspect-symbol',
            'symbol' => 'Acme\Foo',
            'type' => SymbolType::CLASS_TYPE,
            '--no-interaction' => null,
            '--no-config' => null,
        ];

        $this->appTester->run($input);

        $expected = self::STATIC_CONTENT.<<<'EOL'
            No configuration loaded.

            Inspecting the symbol Acme\Foo for type class:

             * Internal: false
             * Exposed:  false


            EOL;

        $this->assertSameOutput($expected, 0);
    }

    public function test_inspects_the_given_symbol_for_any_type(): void
    {
        $input = [
            'inspect-symbol',
            'symbol' => 'Acme\Foo',
            '--no-interaction' => null,
            '--no-config' => null,
        ];

        $this->appTester->run($input);

        $expected = self::STATIC_CONTENT.<<<'EOL'
            No configuration loaded.

            Inspecting the symbol Acme\Foo for all types.

            As a class:
             * Internal: false
             * Exposed:  false

            As a function:
             * Internal: false
             * Exposed:  false

            As a constant:
             * Internal: false
             * Exposed:  false


            EOL;

        $this->assertSameOutput($expected, 0);
    }

    private function assertSameOutput(string $expectedOutput, int $expectedStatusCode): void
    {
        self::assertSame($expectedOutput, $this->getDisplay());
        self::assertSame($expectedStatusCode, $this->appTester->getStatusCode());
    }

    private function getDisplay(): string
    {
        return DisplayNormalizer::removeTrailingSpaces(
            $this->appTester->getDisplay(true),
        );
    }
}
