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

use Humbug\PhpScoper\Patcher\DummyPatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @internal
 */
#[CoversClass(PatchScoper::class)]
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

    public function test_applies_the_list_of_patches_to_the_scoped_file(): void
    {
        $filePath = '/path/to/file.php';
        $contents = 'Original file content';
        $prefix = 'Humbug';
        $patcher = new DummyPatcher();

        $this->decoratedScoperProphecy
            ->scope($filePath, $contents)
            ->willReturn('Decorated scoper contents');

        $expected = 'patchedContent<Decorated scoper contents>';

        $scoper = new PatchScoper(
            $this->decoratedScoper,
            $prefix,
            $patcher,
        );

        $actual = $scoper->scope($filePath, $contents);

        self::assertSame($expected, $actual);

        $this->decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }
}
