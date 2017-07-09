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

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use function Humbug\PhpScoper\create_application;
use function Humbug\PhpScoper\make_tmp_dir;
use function Humbug\PhpScoper\remove_dir;

/**
 * @coversNothing
 *
 * @group integration
 */
class AddPrefixCommandIntegrationTest extends TestCase
{
    const FIXTURE_PATH = __DIR__.'/../../../fixtures/set002/original';

    /**
     * @var ApplicationTester
     */
    private $appTester;

    /**
     * @var string
     */
    private $cwd;

    /**
     * @var string
     */
    private $tmp;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        if (null !== $this->appTester) {
            chdir($this->tmp);

            return;
        }

        $application = create_application();
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $this->appTester = new ApplicationTester($application);

        $this->cwd = getcwd();
        $this->tmp = make_tmp_dir('scoper', __CLASS__);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        chdir($this->cwd);

        remove_dir($this->tmp);
    }

    public function test_scope_the_given_paths()
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

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->assertFilesAreSame(self::FIXTURE_PATH.'/../scoped', $this->tmp);
    }

    public function test_scope_in_quiet_mode()
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

        $this->assertSame($expected, $actual);
        $this->assertSame(0, $this->appTester->getStatusCode());
    }

    public function test_scope_in_normal_mode()
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

PHP Scoper version 12ccf1ac8c7ae8eaf502bd30f95630a112dc713f

 0/2 [░░░░░░░░░░░░░░░░░░░░░░░░░░░░]   0%
 1/2 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓░░░░░░░░░░░░░░]  50%
 2/2 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

 [OK] Successfully prefixed 2 files.

 // Memory usage: 5.00MB (peak: 10.00MB), time: 0.00s


EOF;

        $actual = $this->getNormalizeDisplay($this->appTester->getDisplay(true));

        $this->assertSame($expected, $actual);
        $this->assertSame(0, $this->appTester->getStatusCode());
    }

    public function test_scope_in_verbose_mode()
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

PHP Scoper version 12ccf1ac8c7ae8eaf502bd30f95630a112dc713f

 * [OK] /path/to/file.php
 * [NO] /path/to/invalid-file.php


 [OK] Successfully prefixed 2 files.

 // Memory usage: 5.00MB (peak: 10.00MB), time: 0.00s


EOF;

        $actual = $this->getNormalizeDisplay($this->appTester->getDisplay(true));

        $this->assertSame($expected, $actual);
        $this->assertSame(0, $this->appTester->getStatusCode());
    }

    public function test_scope_in_very_verbose_mode()
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

        $dir = realpath(__DIR__.'/../../../');

        $expected = <<<EOF

    ____  __  ______     _____
   / __ \/ / / / __ \   / ___/_________  ____  ___  _____
  / /_/ / /_/ / /_/ /   \__ \/ ___/ __ \/ __ \/ _ \/ ___/
 / ____/ __  / ____/   ___/ / /__/ /_/ / /_/ /  __/ /
/_/   /_/ /_/_/       /____/\___/\____/ .___/\___/_/
                                     /_/

PHP Scoper version 12ccf1ac8c7ae8eaf502bd30f95630a112dc713f

 * [OK] /path/to/file.php
 * [NO] /path/to/invalid-file.php
	Could not parse the file "/path/to/invalid-file.php".: PhpParser\Error: Syntax error, unexpected EOF on line 3 in $dir/vendor/nikic/php-parser/lib/PhpParser/ParserAbstract.php:293
Stack trace:
#0 $dir/src/Scoper/PhpScoper.php(66): PhpParser\ParserAbstract->parse('<?php\\n\\n\$x = ''')
#1 $dir/src/Scoper/Composer/InstalledPackagesScoper.php(54): Humbug\PhpScoper\Scoper\PhpScoper->scope('/Users/theofidr...', 'MyPrefix', Array)
#2 $dir/src/Scoper/Composer/JsonFileScoper.php(36): Humbug\PhpScoper\Scoper\Composer\InstalledPackagesScoper->scope('/Users/theofidr...', 'MyPrefix', Array)
#3 $dir/src/Scoper/PatchScoper.php(33): Humbug\PhpScoper\Scoper\Composer\JsonFileScoper->scope('/Users/theofidr...', 'MyPrefix', Array)
#4 $dir/src/Handler/HandleAddPrefix.php(177): Humbug\PhpScoper\Scoper\PatchScoper->scope('/Users/theofidr...', 'MyPrefix', Array)
#5 $dir/src/Handler/HandleAddPrefix.php(156): Humbug\PhpScoper\Handler\HandleAddPrefix->scopeFile('/Users/theofidr...', '/private/var/fo...', 'MyPrefix', Array, false, Object(Humbug\PhpScoper\Logger\ConsoleLogger))
#6 $dir/src/Handler/HandleAddPrefix.php(59): Humbug\PhpScoper\Handler\HandleAddPrefix->scopeFiles(Array, 'MyPrefix', Array, false, Object(Humbug\PhpScoper\Logger\ConsoleLogger))
#7 $dir/src/Console/Command/AddPrefixCommand.php(140): Humbug\PhpScoper\Handler\HandleAddPrefix->__invoke('MyPrefix', Array, '/private/var/fo...', Array, false, Object(Humbug\PhpScoper\Logger\ConsoleLogger))
#8 $dir/vendor/symfony/console/Command/Command.php(264): Humbug\PhpScoper\Console\Command\AddPrefixCommand->execute(Object(Symfony\Component\Console\Input\ArrayInput), Object(Symfony\Component\Console\Output\StreamOutput))
#9 $dir/vendor/symfony/console/Application.php(869): Symfony\Component\Console\Command\Command->run(Object(Symfony\Component\Console\Input\ArrayInput), Object(Symfony\Component\Console\Output\StreamOutput))
#10 $dir/vendor/symfony/console/Application.php(223): Symfony\Component\Console\Application->doRunCommand(Object(Humbug\PhpScoper\Console\Command\AddPrefixCommand), Object(Symfony\Component\Console\Input\ArrayInput), Object(Symfony\Component\Console\Output\StreamOutput))
#11 $dir/vendor/symfony/console/Application.php(130): Symfony\Component\Console\Application->doRun(Object(Symfony\Component\Console\Input\ArrayInput), Object(Symfony\Component\Console\Output\StreamOutput))
#12 $dir/vendor/symfony/console/Tester/ApplicationTester.php(100): Symfony\Component\Console\Application->run(Object(Symfony\Component\Console\Input\ArrayInput), Object(Symfony\Component\Console\Output\StreamOutput))
#13 $dir/tests/Console/Command/AddPrefixCommandIntegrationTest.php(219): Symfony\Component\Console\Tester\ApplicationTester->run(Array)
#14 [internal function]: Humbug\PhpScoper\Console\Command\AddPrefixCommandIntegrationTest->test_scope_in_very_verbose_mode()
#15 $dir/vendor/phpunit/phpunit/src/Framework/TestCase.php(1069): ReflectionMethod->invokeArgs(Object(Humbug\PhpScoper\Console\Command\AddPrefixCommandIntegrationTest), Array)
#16 $dir/vendor/phpunit/phpunit/src/Framework/TestCase.php(928): PHPUnit\Framework\TestCase->runTest()
#17 $dir/vendor/phpunit/phpunit/src/Framework/TestResult.php(695): PHPUnit\Framework\TestCase->runBare()
#18 $dir/vendor/phpunit/phpunit/src/Framework/TestCase.php(883): PHPUnit\Framework\TestResult->run(Object(Humbug\PhpScoper\Console\Command\AddPrefixCommandIntegrationTest))
#19 $dir/vendor/phpunit/phpunit/src/Framework/TestSuite.php(746): PHPUnit\Framework\TestCase->run(Object(PHPUnit\Framework\TestResult))
#20 $dir/vendor/phpunit/phpunit/src/Framework/TestSuite.php(746): PHPUnit\Framework\TestSuite->run(Object(PHPUnit\Framework\TestResult))
#21 $dir/vendor/phpunit/phpunit/src/TextUI/TestRunner.php(537): PHPUnit\Framework\TestSuite->run(Object(PHPUnit\Framework\TestResult))
#22 $dir/vendor/phpunit/phpunit/src/TextUI/Command.php(210): PHPUnit\TextUI\TestRunner->doRun(Object(PHPUnit\Framework\TestSuite), Array, true)
#23 $dir/vendor/phpunit/phpunit/src/TextUI/Command.php(141): PHPUnit\TextUI\Command->run(Array, true)
#24 $dir/vendor/phpunit/phpunit/phpunit(53): PHPUnit\TextUI\Command::main()
#25 {main}


 [OK] Successfully prefixed 2 files.

 // Memory usage: 5.00MB (peak: 10.00MB), time: 0.00s


EOF;

        $actual = $this->getNormalizeDisplay($this->appTester->getDisplay(true));

        $this->assertSame($expected, $actual);
        $this->assertSame(0, $this->appTester->getStatusCode());
    }

    private function getNormalizeDisplay(string $display)
    {
        $display = str_replace(realpath(self::FIXTURE_PATH), '/path/to', $display);
        $display = str_replace($this->tmp, '/path/to', $display);
        $display = preg_replace(
            '/\/\/ Memory usage: \d+\.\d{2}MB \(peak: \d+\.\d{2}MB\), time: \d+\.\d{2}s/',
            '// Memory usage: 5.00MB (peak: 10.00MB), time: 0.00s',
            $display
        );

        $display = preg_replace(
            '/(dev-)?\b([a-f0-9]{40})\b/',
            '12ccf1ac8c7ae8eaf502bd30f95630a112dc713f',
            $display
        );

        $lines = explode("\n", $display);

        $lines = array_map(
            'rtrim',
            $lines
        );

        return implode("\n", $lines);
    }

    private function assertFilesAreSame(string $expectedDir, string $actualDir)
    {
        $expected = $this->collectFiles($expectedDir);

        $actual = $this->collectFiles($actualDir);

        $this->assertSame($expected, $actual);
    }

    private function collectFiles(string $dir)
    {
        $dir = realpath($dir);
        $finder = new Finder();

        $files = $finder->files()
            ->in($dir)
            ->sortByName()
        ;

        return array_reduce(
            iterator_to_array($files),
            function (array $collectedFiles, SplFileInfo $file) use ($dir): array {
                $path = str_replace($dir, '', $file->getRealPath());

                $collectedFiles[$path] = file_get_contents($file->getRealPath());

                return $collectedFiles;
            },
            []
        );
    }
}
