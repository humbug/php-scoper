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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use function Humbug\PhpScoper\createApplication;
use function Humbug\PhpScoper\makeTempDir;
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

        $application = createApplication();
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $this->appTester = new ApplicationTester($application);

        $this->tmpDir = makeTempDir('scoper', __CLASS__);

        $filesystem = new Filesystem();
        $filesystem->mirror(self::FIXTURE_PATH, $this->tmpDir);
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
                $this->tmpDir,
            ],
        ];

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->assertFilesAreSame(self::FIXTURE_PATH.'/../scoped', $this->tmpDir);
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
