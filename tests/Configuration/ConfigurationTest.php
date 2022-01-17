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

namespace Humbug\PhpScoper\Configuration;

use Humbug\PhpScoper\Patcher\PatcherChain;
use Humbug\PhpScoper\Whitelist;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\Configuration\ConfigurationFactory
 */
final class ConfigurationTest extends TestCase
{
    /**
     * @dataProvider prefixProvider
     */
    public function test_it_validates_the_prefix(
        string $prefix,
        string $expectedExceptionMessage
    ): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        new Configuration(
            null,
            $prefix,
            [],
            [],
            new PatcherChain([]),
            Whitelist::create(),
            [],
            [],
            [],
        );
    }

    public static function prefixProvider(): iterable
    {
        yield [
            ';',
            'The prefix needs to be composed solely of letters, digits and backslashes (as namespace separators). Got ";"',
        ];

        yield [
            'App\\\\Foo',
            'Invalid namespace separator sequence. Got "App\\\\Foo"',
        ];
    }
}
