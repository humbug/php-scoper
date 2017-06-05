<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\Handler;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\Handler\HandleAddPrefix
 */
class HandleAddPrefixTest extends TestCase
{
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        if (file_exists($this->tempDir)) {
            chdir($this->tempDir);
        }

        if (null !== $this->appTester) {
            return;
        }

        $this->cwd = getcwd();

        $this->handleProphecy = $this->prophesize(HandleAddPrefix::class);
        /** @var HandleAddPrefix $handle */
        $handle = $this->handleProphecy->reveal();

        $application = new Application('php-scoper-test');
        $application->addCommands([
            new AddPrefixCommand($handle),
        ]);
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        $this->appTester = new ApplicationTester($application);

        $this->tempDir = makeTempDir('php-scoper', __CLASS__);
        chdir($this->tempDir);

        $filesystem = new Filesystem();
        $filesystem->mirror(self::FIXTURES_DIR.DIRECTORY_SEPARATOR.'original', $this->tempDir);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        chdir($this->cwd);

        $filesystem = new Filesystem();
        $filesystem->remove($this->tempDir);
    }
}
