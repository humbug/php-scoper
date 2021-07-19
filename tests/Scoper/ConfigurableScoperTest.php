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
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use function is_a;

/**
 * @covers \Humbug\PhpScoper\Scoper\ConfigurableScoper
 */
class ConfigurableScoperTest extends TestCase
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
        self::assertTrue(is_a(ConfigurableScoper::class, Scoper::class, true));
    }

    public function test_it_scopes_the_files_with_the_decorated_scoper(): void
    {
        $filePath = '/path/to/file.php';
        $contents = 'Original file content';
        $prefix = 'Humbug';
        $patchers = [];
        $whitelist = Whitelist::create();

        $this->decoratedScoperProphecy
            ->scope($filePath, $contents, $prefix, $patchers, $whitelist)
            ->willReturn($expected = 'Decorated scoper contents')
        ;

        $scoper = new ConfigurableScoper($this->decoratedScoper);

        $actual = $scoper->scope($filePath, $contents, $prefix, $patchers, $whitelist);

        self::assertSame($expected, $actual);

        $this->decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_it_can_create_a_scoper_allowing_to_whitelist_specific_files(): void
    {
        $whitelistedFiles = [
            '/path/to/whitelisted-file-1',
            '/path/to/whitelisted-file-2',
        ];

        $filePath = '/path/to/file.php';
        $contents = 'Original file content';
        $prefix = 'Humbug';
        $patchers = [];
        $whitelist = Whitelist::create();

        $this->decoratedScoperProphecy
            ->scope(Argument::any(), $contents, $prefix, $patchers, $whitelist)
            ->willReturn($expected = 'scoped contents')
        ;

        $scoper = (new ConfigurableScoper($this->decoratedScoper))->withWhitelistedFiles(...$whitelistedFiles);

        foreach ($whitelistedFiles as $whitelistedFile) {
            $actual = $scoper->scope($whitelistedFile, $contents, $prefix, $patchers, $whitelist);

            self::assertSame($contents, $actual);
        }

        $actual = $scoper->scope($filePath, $contents, $prefix, $patchers, $whitelist);

        self::assertSame($expected, $actual);

        $this->decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }
}
