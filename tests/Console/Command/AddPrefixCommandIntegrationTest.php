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
    private $tmpDir;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        if (null !== $this->appTester) {
            chdir($this->tmpDir);

            return;
        }

        $this->cwd = getcwd();

        $application = create_application();
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $this->appTester = new ApplicationTester($application);

        $this->tmpDir = make_tmp_dir('scoper', __CLASS__);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        chdir($this->cwd);

        remove_dir($this->tmpDir);
    }

    public function test_scope_the_given_paths()
    {
        $input = [
            'add-prefix',
            'prefix' => 'MyPrefix',
            'paths' => [
                self::FIXTURE_PATH,
            ],
            '--output-dir' => $this->tmpDir,
        ];

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->assertFilesAreSame(self::FIXTURE_PATH.'/../scoped', $this->tmpDir);
    }

    public function test_scope_in_quiet_mode()
    {
        $input = [
            'add-prefix',
            'prefix' => 'MyPrefix',
            'paths' => [
                $this->tmpDir,
            ],
            '--quiet',
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
            'prefix' => 'MyPrefix',
            'paths' => [
                $this->tmpDir,
            ],
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

    0 [░░░░░░░░░░░░░░░░░░░░░░░░░░░░]
    1 [▓░░░░░░░░░░░░░░░░░░░░░░░░░░░]

 [OK] Successfully prefixed 1 files.                                            


EOF;

        $actual = $this->getNormalizeDisplay($this->appTester->getDisplay(true));

        $this->assertSame($expected, $actual);
        $this->assertSame(0, $this->appTester->getStatusCode());
    }

    public function test_scope_in_verbose_mode()
    {
        $input = [
            'add-prefix',
            'prefix' => 'MyPrefix',
            'paths' => [
                $this->tmpDir,
            ],
            '-v',
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


 [OK] Successfully prefixed 1 files.                                            

 // Memory usage: 5.00MB (peak: 10.00MB), time: 0.00s                            


EOF;

        $actual = $this->getNormalizeDisplay($this->appTester->getDisplay(true));

        $this->assertSame($expected, $actual);
        $this->assertSame(0, $this->appTester->getStatusCode());
    }

    private function getNormalizeDisplay(string $display)
    {
        $display = str_replace($this->tmpDir, '/path/to', $display);
        $display = preg_replace(
            '/\/\/ Memory usage: \d+\.\d{2}MB \(peak: \d+\.\d{2}MB\), time: \d+\.\d{2}s/',
            '// Memory usage: 5.00MB (peak: 10.00MB), time: 0.00s',
            $display
        );

        return preg_replace(
            '/(dev-)?\b([a-f0-9]{40})\b/',
            '12ccf1ac8c7ae8eaf502bd30f95630a112dc713f',
            $display
        );
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
