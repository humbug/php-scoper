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

namespace Humbug\PhpScoper\Console\Command;

use Fidry\Console\Test\AppTester;
use Humbug\PhpScoper\Console\Application;
use Humbug\PhpScoper\Console\AppTesterAbilities;
use Humbug\PhpScoper\Console\AppTesterTestCase;
use Humbug\PhpScoper\Container;
use Humbug\PhpScoper\FileSystemTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use function array_reduce;
use function iterator_to_array;
use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function Safe\fileperms;
use function Safe\preg_replace;
use function Safe\realpath;
use function sprintf;
use function str_replace;
use const DIRECTORY_SEPARATOR;

/**
 * @internal
 */
#[Group('integration')]
#[CoversNothing]
class AddPrefixCommandIntegrationTest extends FileSystemTestCase implements AppTesterTestCase
{
    use AppTesterAbilities;

    private const FIXTURE_PATH = __DIR__.'/../../../fixtures/set002/original';

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

        file_put_contents('scoper.inc.php', '<?php return [];');
    }

    public function test_scope_the_given_paths(): void
    {
        $outputDir = $this->tmp.DIRECTORY_SEPARATOR.'build';

        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                self::FIXTURE_PATH,
            ],
            '--output-dir' => $outputDir,
            '--no-interaction' => null,
            '--no-config' => null,
        ];

        $this->appTester->run($input);

        self::assertSame(0, $this->appTester->getStatusCode());

        self::assertFilesAreSame(
            self::FIXTURE_PATH.'/../scoped',
            $outputDir,
        );
    }

    public function test_scope_in_quiet_mode(): void
    {
        $outputDir = $this->tmp.DIRECTORY_SEPARATOR.'build';

        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                self::FIXTURE_PATH,
            ],
            '--output-dir' => $outputDir,
            '--quiet' => null,
        ];

        $this->appTester->run($input);

        $this->assertExpectedOutput('', 0);
    }

    public function test_scope_in_normal_mode(): void
    {
        $outputDir = $this->tmp.DIRECTORY_SEPARATOR.'build';

        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                self::FIXTURE_PATH,
            ],
            '--output-dir' => $outputDir,
            '--no-interaction' => null,
        ];

        $this->appTester->run($input);

        $expected = <<<'EOF'


                ____  __  ______     _____
               / __ \/ / / / __ \   / ___/_________  ____  ___  _____
              / /_/ / /_/ / /_/ /   \__ \/ ___/ __ \/ __ \/ _ \/ ___/
             / ____/ __  / ____/   ___/ / /__/ /_/ / /_/ /  __/ /
            /_/   /_/ /_/_/       /____/\___/\____/ .___/\___/_/
                                                 /_/

            PhpScoper version TestVersion 28/01/2020

             0/5 [░░░░░░░░░░░░░░░░░░░░░░░░░░░░]   0%
             5/5 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

             [OK] Successfully prefixed 5 files.

             // Memory usage: 5.00MB (peak: 10.00MB), time: 0.00s


            EOF;

        $this->assertExpectedOutput(
            $expected,
            0,
            $this->createDisplayNormalizer(),
            self::replaceIntermediateProgressBarSteps(...),
        );
    }

    public function test_scope_in_verbose_mode(): void
    {
        $outputDir = $this->tmp.DIRECTORY_SEPARATOR.'build';

        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                self::FIXTURE_PATH,
            ],
            '--output-dir' => $outputDir,
            '-v' => null,
            '--no-interaction' => null,
        ];

        $this->appTester->run($input);

        $expected = <<<'EOF'


                ____  __  ______     _____
               / __ \/ / / / __ \   / ___/_________  ____  ___  _____
              / /_/ / /_/ / /_/ /   \__ \/ ___/ __ \/ __ \/ _ \/ ___/
             / ____/ __  / ____/   ___/ / /__/ /_/ / /_/ /  __/ /
            /_/   /_/ /_/_/       /____/\___/\____/ .___/\___/_/
                                                 /_/

            PhpScoper version TestVersion 28/01/2020

             * [OK] /path/to/composer/installed.json
             * [OK] /path/to/executable-file.php
             * [OK] /path/to/file.php
             * [NO] /path/to/invalid-file.php
             * [OK] /path/to/scoper.inc.php


             [OK] Successfully prefixed 5 files.

             // Memory usage: 5.00MB (peak: 10.00MB), time: 0.00s


            EOF;

        $this->assertExpectedOutput(
            $expected,
            0,
            $this->createDisplayNormalizer(),
        );
    }

    public function test_scope_in_very_verbose_mode(): void
    {
        $outputDir = $this->tmp.DIRECTORY_SEPARATOR.'build';

        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                self::FIXTURE_PATH,
            ],
            '--output-dir' => $outputDir,
            '-vv' => null,
            '--no-interaction' => null,
        ];

        $this->appTester->run($input);

        $expected = <<<'EOF'


                ____  __  ______     _____
               / __ \/ / / / __ \   / ___/_________  ____  ___  _____
              / /_/ / /_/ / /_/ /   \__ \/ ___/ __ \/ __ \/ _ \/ ___/
             / ____/ __  / ____/   ___/ / /__/ /_/ / /_/ /  __/ /
            /_/   /_/ /_/_/       /____/\___/\____/ .___/\___/_/
                                                 /_/

            PhpScoper version TestVersion 28/01/2020

             * [OK] /path/to/composer/installed.json
             * [OK] /path/to/executable-file.php
             * [OK] /path/to/file.php
             * [NO] /path/to/invalid-file.php
            	Could not parse the file "/path/to/invalid-file.php".: PhpParser
            Stack trace:
            #0
            #1
            #2
            #3
            #4
            #5
            #6
            #7
            #8
            #9
             * [OK] /path/to/scoper.inc.php


             [OK] Successfully prefixed 5 files.

             // Memory usage: 5.00MB (peak: 10.00MB), time: 0.00s


            EOF;

        $extraDisplayNormalization = static function (string $display): string {
            $display = preg_replace('/(Could not parse the file ".+?"\.: \w+).*(\n)/', '$1$2', $display);
            $display = preg_replace('/(#\d+).*(\n)/', '$1$2', $display);

            // Remove overly lengthy stack-trace
            return preg_replace('/(Stack trace:(?:\n\#\d)+)\n?((?:\n\#\d{2,})+)/', '$1', $display);
        };

        $this->assertExpectedOutput(
            $expected,
            0,
            $this->createDisplayNormalizer(),
            $extraDisplayNormalization,
        );
    }

    /**
     * @return callable(string):string
     */
    private function createDisplayNormalizer(): callable
    {
        return fn ($display) => $this->getNormalizeDisplay($display);
    }

    private function getNormalizeDisplay(string $display): string
    {
        $display = str_replace(
            [realpath(self::FIXTURE_PATH), $this->tmp],
            '/path/to',
            $display,
        );

        return preg_replace(
            '/\/\/ Memory usage: \d+\.\d{2}MB \(peak: \d+\.\d{2}MB\), time: \d+\.\d{2}s/',
            '// Memory usage: 5.00MB (peak: 10.00MB), time: 0.00s',
            $display,
        );
    }

    private static function assertFilesAreSame(string $expectedDir, string $actualDir): void
    {
        $expected = self::collectFiles($expectedDir);
        $actual = self::collectFiles($actualDir);

        self::assertSame($expected, $actual);
    }

    private static function collectFiles(string $dir): array
    {
        $dir = realpath($dir);
        $finder = new Finder();

        $files = $finder->files()
            ->in($dir)
            ->sortByName();

        return array_reduce(
            iterator_to_array($files),
            static function (array $collectedFiles, SplFileInfo $file) use ($dir): array {
                $realPath = $file->getRealPath();

                self::assertIsString(
                    $realPath,
                    sprintf(
                        'Expected file "%s" to have a real path.',
                        $file->getPathname(),
                    ),
                );

                $path = str_replace($dir, '', $realPath);

                $collectedFiles[$path] = [file_get_contents($realPath), fileperms($realPath)];

                return $collectedFiles;
            },
            [],
        );
    }

    private static function replaceIntermediateProgressBarSteps(string $output): string
    {
        return str_replace(
            [
                <<<'EOF'

                     1/5 [▓▓▓▓▓░░░░░░░░░░░░░░░░░░░░░░░]  20%
                    EOF,
                <<<'EOF'

                     2/5 [▓▓▓▓▓▓▓▓▓▓░░░░░░░░░░░░░░░░░░]  40%
                    EOF,
                <<<'EOF'

                     3/5 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓░░░░░░░░░░]  60%
                    EOF,
                <<<'EOF'

                     4/5 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓░░░░░]  80%
                    EOF,
            ],
            ['', '', ''],
            $output,
        );
    }
}
