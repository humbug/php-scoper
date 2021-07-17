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
use Humbug\PhpScoper\Whitelist;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use function is_a;

/**
 * @covers \Humbug\PhpScoper\Scoper\PatchScoper
 */
class PatchScoperTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<Scoper>
     */
    private ObjectProphecy $decoratedScoperProphecy;

    private Scoper $decoratedScoper;

    protected function setUp(): void
    {
        $this->decoratedScoperProphecy = $this->prophesize(Scoper::class);
        $this->decoratedScoper = $this->decoratedScoperProphecy->reveal();
    }

    public function test_is_a_Scoper(): void
    {
        self::assertTrue(is_a(PatchScoper::class, Scoper::class, true));
    }

    public function test_applies_the_list_of_patches_to_the_scoped_file(): void
    {
        $filePath = '/path/to/file.php';
        $contents = 'Original file content';
        $prefix = 'Humbug';

        $patchers = [
            static function (string $patcherFilePath, string $patcherPrefix, string $contents) use ($filePath, $prefix): string {
                Assert::assertSame($filePath, $patcherFilePath);
                Assert::assertSame($prefix, $patcherPrefix);
                Assert::assertSame('Decorated scoper contents', $contents);

                return 'File content after patch 1';
            },
            static function (string $patcherFilePath, string $patcherPrefix, string $contents) use ($filePath, $prefix): string {
                Assert::assertSame($filePath, $patcherFilePath);
                Assert::assertSame($prefix, $patcherPrefix);
                Assert::assertSame('File content after patch 1', $contents);

                return 'File content after patch 2';
            },
        ];

        $whitelist = Whitelist::create(
            true,
            true,
            true,
            [],
            [],
            'Foo',
        );

        $this->decoratedScoperProphecy
            ->scope($filePath, $contents, $prefix, $patchers, $whitelist)
            ->willReturn('Decorated scoper contents')
        ;

        $expected = 'File content after patch 2';

        $scoper = new PatchScoper($this->decoratedScoper);

        $actual = $scoper->scope($filePath, $contents, $prefix, $patchers, $whitelist);

        self::assertSame($expected, $actual);

        $this->decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }
}
