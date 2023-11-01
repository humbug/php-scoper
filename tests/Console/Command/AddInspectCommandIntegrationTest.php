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

namespace Console\Command;

use Fidry\Console\ExitCode;
use Fidry\Console\Test\AppTester;
use Fidry\Console\Test\OutputAssertions;
use Humbug\PhpScoper\Console\Application;
use Humbug\PhpScoper\Container;
use PHPUnit\Framework\TestCase;
use function Safe\preg_replace;

/**
 * @coversNothing
 *
 * @group integration
 *
 * @internal
 */
class AddInspectCommandIntegrationTest extends TestCase
{
    private const FIXTURE_PATH = __DIR__.'/../../../fixtures/set002/original';

    private AppTester $appTester;

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

        $this->appTester = AppTester::fromConsoleApp($application);
    }

    public function test_it_shows_the_scopped_content_of_the_file_given(): void
    {
        $input = [
            'inspect',
            '--prefix' => 'MyPrefix',
            'file-path' => self::FIXTURE_PATH.'/file.php',
            '--no-interaction' => null,
            '--no-config' => null,
        ];

        $this->appTester->run($input);

        OutputAssertions::assertSameOutput(
            <<<'PHP'

                Scopped contents:

                """
                <?php

                declare (strict_types=1);
                namespace MyPrefix\MyNamespace;


                """

                Symbols Registry:

                """
                Humbug\PhpScoper\Symbol\SymbolsRegistry {#140
                  -recordedFunctions: []
                  -recordedClasses: []
                }

                """

                PHP,
            ExitCode::SUCCESS,
            $this->appTester,
            self::normalizeSymbolsRegistryReference(...),
        );
    }

    public function test_it_shows_the_raw_scopped_content_of_the_file_given_in_quiet_mode(): void
    {
        $input = [
            'inspect',
            '--prefix' => 'MyPrefix',
            'file-path' => self::FIXTURE_PATH.'/file.php',
            '--no-interaction' => null,
            '--no-config' => null,
            '--quiet' => null,
        ];

        $this->appTester->run($input);

        OutputAssertions::assertSameOutput(
            <<<'PHP'
                <?php

                declare (strict_types=1);
                namespace MyPrefix\MyNamespace;



                PHP,
            ExitCode::SUCCESS,
            $this->appTester,
        );
    }

    private static function normalizeSymbolsRegistryReference(string $output): string
    {
        return preg_replace(
            '/ \{#\d{3,}/',
            ' {#140',
            $output,
        );
    }
}
