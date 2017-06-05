<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Handler;

use Humbug\PhpScoper\Logger\ConsoleLogger;
use Humbug\PhpScoper\Scoper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Filesystem\Filesystem;
use function Humbug\PhpScoper\escape_path;
use function Humbug\PhpScoper\makeTempDir;

/**
 * @covers \Humbug\PhpScoper\Handler\HandleAddPrefix
 */
class HandleAddPrefixTest extends TestCase
{
    const FIXTURE_PATH_000 = __DIR__.'/../../fixtures/set000';
    const FIXTURE_PATH_001 = __DIR__.'/../../fixtures/set001';

    /**
     * @var Scoper|ObjectProphecy
     */
    private $scoperProphecy;

    /**
     * @var ConsoleLogger|ObjectProphecy
     */
    private $loggerProphecy;

    /**
     * @var HandleAddPrefix
     */
    private $handle;

    /**
     * @var string
     */
    private $tmpDir0 = '';

    /**
     * @var string
     */
    private $tmpDir1 = '';

    /**
     * @var string
     */
    private $cwd;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        if (file_exists($this->tmpDir0)) {
            return;
        }

        $this->cwd = getcwd();

        $this->tmpDir0 = makeTempDir('scoper0', __CLASS__);
        $this->tmpDir1 = makeTempDir('scoper1', __CLASS__);

        $filesystem = new Filesystem();
        $filesystem->mirror(escape_path(self::FIXTURE_PATH_000), $this->tmpDir0);
        $filesystem->mirror(escape_path(self::FIXTURE_PATH_001), $this->tmpDir1);

        $this->scoperProphecy = $this->prophesize(Scoper::class);
        /** @var Scoper $scoper */
        $scoper = $this->scoperProphecy->reveal();

        $this->handle = new HandleAddPrefix($scoper);

        $this->loggerProphecy = $this->prophesize(ConsoleLogger::class);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        chdir(escape_path($this->cwd));

        $filesystem = new Filesystem();
        $filesystem->remove(escape_path($this->tmpDir0));
    }

    /**
     * @dataProvider providePaths
     */
    public function test_scopes_all_the_files_found_in_the_given_paths(array $paths, array $expected)
    {
        chdir(escape_path($this->tmpDir0));

        $prefix = 'MyPrefix';

        $paths = array_map(
            function (string $relativePath) {
                return escape_path($this->tmpDir0.'/'.$relativePath);
            },
            $paths
        );

        /** @var ConsoleLogger $logger */
        $logger = $this->loggerProphecy->reveal();

        foreach ($expected as $fileContent) {
            $filePath = escape_path($this->tmpDir0.$fileContent);

            $this->scoperProphecy->scope($fileContent, $prefix)->shouldBeCalled();
            $this->loggerProphecy->outputSuccess($filePath)->shouldBeCalled();
        }

        $this->loggerProphecy->outputFileCount(count($expected))->shouldBeCalled();

        $this->handle->__invoke($prefix, $paths, $logger);

        $this->scoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(count($expected));
        $this->loggerProphecy->outputSuccess(Argument::cetera())->shouldHaveBeenCalledTimes(count($expected));
        $this->loggerProphecy->outputFileCount(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_replaces_the_content_of_the_files_with_the_scoped_content()
    {
        chdir(escape_path($this->tmpDir1));

        $prefix = 'MyPrefix';
        $paths = [
            $filePath = escape_path($this->tmpDir1.'/file.php'),
        ];

        /** @var ConsoleLogger $logger */
        $logger = $this->loggerProphecy->reveal();

        $expected = <<<PHP
<?php

declare(strict_types=1);

namespace Myprefix\MyNamespace;

PHP;

        $this->scoperProphecy->scope(Argument::any(), $prefix)->willReturn($expected);

        $this->handle->__invoke($prefix, $paths, $logger);

        $actual = file_get_contents($filePath);

        $this->assertSame($expected, $actual);
    }

    public function providePaths()
    {
        yield 'directory with file' => [
            [
                'dir1',
            ],
            [
                '/dir1/fileA.php',
            ],
        ];

        yield 'PHP file' => [
            [
                'file1.php',
            ],
            [
                '/file1.php',
            ],
        ];

        yield 'non PHP file' => [
            [
                'unknown',
            ],
            [],
        ];

        yield 'empty directory' => [
            [
                'empty-dir',
            ],
            [],
        ];

        yield 'complete sample' => [
            [
                'dir1',
                'dir2',
                'file2.php',
            ],
            [
                '/dir1/fileA.php',
                '/dir2/dir3/fileD.php',
                '/dir2/fileB.php',
                '/dir2/fileC.php',
                '/file2.php',
            ],
        ];
    }
}
