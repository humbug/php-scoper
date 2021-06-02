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

use Fidry\Console\Application\SymfonyApplication;
use Humbug\PhpScoper\Console\Application;
use Humbug\PhpScoper\Console\DisplayNormalizer;
use Humbug\PhpScoper\Container;
use Humbug\PhpScoper\FileSystemTestCase;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use function array_map;
use function array_reduce;
use function explode;
use function implode;
use function iterator_to_array;
use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function Safe\preg_replace;
use function Safe\realpath;
use function str_replace;

/**
 * @coversNothing
 *
 * @group integration
 * @runTestsInSeparateProcesses
 */
class AddPrefixCommandIntegrationTest extends FileSystemTestCase
{
    private const FIXTURE_PATH = __DIR__.'/../../../fixtures/set002/original';

    /**
     * @var ApplicationTester
     */
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

        file_put_contents('scoper.inc.php', '<?php return [];');
    }

    public function test_scope_the_given_paths(): void
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                self::FIXTURE_PATH,
            ],
            '--output-dir' => $this->tmp,
            '--no-interaction' => null,
            '--no-config' => null,
        ];

        $this->appTester->run($input);

        self::assertSame(0, $this->appTester->getStatusCode());

        $this->assertFilesAreSame(self::FIXTURE_PATH.'/../scoped', $this->tmp);
    }

    public function test_scope_in_quiet_mode(): void
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                self::FIXTURE_PATH,
            ],
            '--output-dir' => $this->tmp,
            '--quiet' => null,
        ];

        $this->appTester->run($input);

        $expected = '';

        $actual = $this->getNormalizeDisplay($this->appTester->getDisplay(true));

        self::assertSame($expected, $actual);
        self::assertSame(0, $this->appTester->getStatusCode());
    }

    public function test_scope_in_normal_mode(): void
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                self::FIXTURE_PATH,
            ],
            '--output-dir' => $this->tmp,
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

 0/4 [░░░░░░░░░░░░░░░░░░░░░░░░░░░░]   0%
 4/4 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

 [OK] Successfully prefixed 4 files.

 // Memory usage: 5.00MB (peak: 10.00MB), time: 0.00s


EOF;

        $actual = $this->getNormalizeDisplay($this->appTester->getDisplay(true));

        // Remove the intermediary states of the progress bar as it might
        // change depending of the speed of the machine
        $actual = str_replace(
            [
                '
 1/4 [▓▓▓▓▓▓▓░░░░░░░░░░░░░░░░░░░░░]  25%',
                '
 2/4 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓░░░░░░░░░░░░░░]  50%',
                '
 3/4 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓░░░░░░░]  75%',
            ],
            ['', '', ''],
            $actual
        );

        self::assertSame($expected, $actual);
        self::assertSame(0, $this->appTester->getStatusCode());
    }

    public function test_scope_in_verbose_mode(): void
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                self::FIXTURE_PATH,
            ],
            '--output-dir' => $this->tmp,
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

 * [NO] /path/to/composer/installed.json
 * [OK] /path/to/file.php
 * [NO] /path/to/invalid-file.php
 * [OK] /path/to/scoper.inc.php


 [OK] Successfully prefixed 4 files.

 // Memory usage: 5.00MB (peak: 10.00MB), time: 0.00s


EOF;

        $actual = $this->getNormalizeDisplay($this->appTester->getDisplay(true));

        self::assertSame($expected, $actual);
        self::assertSame(0, $this->appTester->getStatusCode());
    }

    public function test_scope_in_very_verbose_mode(): void
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                self::FIXTURE_PATH,
            ],
            '--output-dir' => $this->tmp,
            '-vv' => null,
            '--no-interaction' => null,
        ];

        $this->appTester->run($input);

        $expected = <<<EOF


    ____  __  ______     _____
   / __ \/ / / / __ \   / ___/_________  ____  ___  _____
  / /_/ / /_/ / /_/ /   \__ \/ ___/ __ \/ __ \/ _ \/ ___/
 / ____/ __  / ____/   ___/ / /__/ /_/ / /_/ /  __/ /
/_/   /_/ /_/_/       /____/\___/\____/ .___/\___/_/
                                     /_/

PhpScoper version TestVersion 28/01/2020

 * [NO] /path/to/composer/installed.json
	Could not parse the file "/path/to/composer/installed.json".: TypeError
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


 [OK] Successfully prefixed 4 files.

 // Memory usage: 5.00MB (peak: 10.00MB), time: 0.00s


EOF;

        $actual = $this->getNormalizeDisplay($this->appTester->getDisplay(true));
        $actual = preg_replace('/(Could not parse the file ".+?"\.: \w+).*(\n)/', '$1$2', $actual);
        $actual = preg_replace('/(#\d+).*(\n)/', '$1$2', $actual);
        // Remove overly lengthy stack-trace
        $actual = preg_replace('/(Stack trace:(?:\n\#\d)+)\n?((?:\n\#\d{2,})+)/', '$1', $actual);

        self::assertSame($expected, $actual);
        self::assertSame(0, $this->appTester->getStatusCode());
    }

    private function getNormalizeDisplay(string $display): string
    {
        $display = str_replace(realpath(self::FIXTURE_PATH), '/path/to', $display);
        $display = str_replace($this->tmp, '/path/to', $display);
        $display = DisplayNormalizer::normalizeSeparators($display);
        $display = DisplayNormalizer::normalizeProgressBar($display);
        $display = preg_replace(
            '/\/\/ Memory usage: \d+\.\d{2}MB \(peak: \d+\.\d{2}MB\), time: \d+\.\d{2}s/',
            '// Memory usage: 5.00MB (peak: 10.00MB), time: 0.00s',
            $display
        );

        $lines = explode("\n", $display);

        $lines = array_map(
            'rtrim',
            $lines
        );

        return implode("\n", $lines);
    }

    private function assertFilesAreSame(string $expectedDir, string $actualDir): void
    {
        $expected = $this->collectFiles($expectedDir);

        $actual = $this->collectFiles($actualDir);

        self::assertSame($expected, $actual);
    }

    private function collectFiles(string $dir): array
    {
        $dir = realpath($dir);
        $finder = new Finder();

        $files = $finder->files()
            ->in($dir)
            ->sortByName()
        ;

        return array_reduce(
            iterator_to_array($files),
            static function (array $collectedFiles, SplFileInfo $file) use ($dir): array {
                $path = str_replace($dir, '', $file->getRealPath());

                $collectedFiles[$path] = file_get_contents($file->getRealPath());

                return $collectedFiles;
            },
            []
        );
    }
}
