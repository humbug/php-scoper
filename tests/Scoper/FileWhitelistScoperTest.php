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
 * @covers \Humbug\PhpScoper\Scoper\FileWhitelistScoper
 */
class FileWhitelistScoperTest extends TestCase
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
        self::assertTrue(is_a(FileWhitelistScoper::class, Scoper::class, true));
    }

    public function test_it_scopes_the_file_contents_with_the_decorated_scoper_if_file_not_whitelisted_and_the_contents_unchanged_when_is_whitelisted(): void
    {
        $whitelistedFilePath = '/path/to/whitelist-file.php';
        $notWhitelistedFilePath = '/path/to/not-file.php';
        $contents = 'Original file content';
        $prefix = 'Humbug';
        $patchers = [];
        $whitelist = Whitelist::create(true, true, true, 'Foo');

        $this->decoratedScoperProphecy
            ->scope($notWhitelistedFilePath, $contents, $prefix, $patchers, $whitelist)
            ->willReturn($scopedContents = 'Decorated scoper contents')
        ;

        $scoper = new FileWhitelistScoper($this->decoratedScoper, $whitelistedFilePath);

        self::assertSame(
            $scopedContents,
            $scoper->scope($notWhitelistedFilePath, $contents, $prefix, $patchers, $whitelist)
        );

        self::assertSame(
            $contents,
            $scoper->scope($whitelistedFilePath, $contents, $prefix, $patchers, $whitelist)
        );

        $this->decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }
}
