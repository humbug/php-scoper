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

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\Scoper;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use function Humbug\PhpScoper\create_fake_patcher;
use function Humbug\PhpScoper\create_fake_whitelister;
use function Humbug\PhpScoper\escape_path;
use function Humbug\PhpScoper\make_tmp_dir;
use function Humbug\PhpScoper\remove_dir;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \Humbug\PhpScoper\Scoper\PatchScoper
 */
class PatchScoperTest extends TestCase
{
    /**
     * @var Scoper|ObjectProphecy
     */
    private $decoratedScoperProphecy;

    /**
     * @var Scoper
     */
    private $decoratedScoper;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->decoratedScoperProphecy = $this->prophesize(Scoper::class);
        $this->decoratedScoper = $this->decoratedScoperProphecy->reveal();
    }

    public function test_is_a_Scoper()
    {
        $this->assertTrue(is_a(PatchScoper::class, Scoper::class, true));
    }

    public function test_applies_the_list_of_patches_to_the_scoped_file()
    {
        $filePath = '/path/to/file.php';
        $content = 'Original file content';
        $prefix = 'Humbug';

        $patchers = [
            function (string $patcherFilePath, string $patcherPrefix, string $content) use ($filePath, $prefix): string {
                Assert::assertSame($filePath, $patcherFilePath);
                Assert::assertSame($prefix, $patcherPrefix);
                Assert::assertSame('Original file content', $content);

                return 'File content after patch 1';
            },
            function (string $patcherFilePath, string $patcherPrefix, string $content) use ($filePath, $prefix): string {
                Assert::assertSame($filePath, $patcherFilePath);
                Assert::assertSame($prefix, $patcherPrefix);
                Assert::assertSame('File content after patch 1', $content);

                return 'File content after patch 2';
            },
        ];

        $whitelister = create_fake_whitelister();

        $this->decoratedScoperProphecy
            ->scope($filePath, $prefix, $patchers, $whitelister)
            ->willReturn($content)
        ;

        $expected = 'File content after patch 2';

        $scoper = new PatchScoper($this->decoratedScoper);

        $actual = $scoper->scope($filePath, $prefix, $patchers, $whitelister);

        $this->assertSame($expected, $actual);

        $this->decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }
}
