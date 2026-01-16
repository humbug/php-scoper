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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(RegexChecker::class)]
final class RegexCheckerTest extends TestCase
{
    private RegexChecker $regexChecker;

    protected function setUp(): void
    {
        $this->regexChecker = new RegexChecker();
    }

    #[DataProvider('regexLikeProvider')]
    public function test_it_can_tell_if_a_string_looks_like_a_regex(
        string $value,
        bool $expected,
    ): void {
        $actual = $this->regexChecker->isRegexLike($value);

        self::assertSame($expected, $actual);
    }

    public static function regexLikeProvider(): iterable
    {
        yield 'empty string' => [
            '',
            false,
        ];

        yield 'regular string' => [
            'foo',
            false,
        ];

        yield 'empty regex' => [
            '//',
            true,
        ];

        yield 'empty regex with flags' => [
            '//ui',
            true,
        ];

        yield 'two letters non-regex' => [
            '/~',
            false,
        ];

        yield 'regular regex' => [
            '/foo/',
            true,
        ];

        yield 'fake regex (0)' => [
            '/Foo/Bar/',
            false,
        ];

        // See https://github.com/humbug/php-scoper/issues/597
        yield 'fake regex (1)' => [
            '\Foo\A',
            false,
        ];

        // See https://github.com/humbug/php-scoper/issues/597
        yield 'fake regex (2)' => [
            'Bar\WB',
            false,
        ];

        // See https://github.com/humbug/php-scoper/issues/666
        yield 'fake regex (3)' => [
            '__',
            false,
        ];

        yield 'minimal fake regex' => [
            '////',
            false,
        ];

        yield 'regular regex with flags' => [
            '/foo/iu',
            true,
        ];

        yield 'regular regex with flags (1)' => [
            '~foo~iu',
            true,
        ];

        yield 'regular regex with flags (2)' => [
            '#foo#iu',
            true,
        ];

        yield 'regular regex with invalid flags' => [
            '/foo/NOPE',
            false,
        ];
    }

    #[DataProvider('regexProvider')]
    public function test_it_can_validate_that_a_string_is_a_valid_regex_or_not(
        string $regex,
        ?string $expected,
    ): void {
        $actual = $this->regexChecker->validateRegex($regex);

        self::assertSame($expected, $actual);
    }

    public static function regexProvider(): iterable
    {
        yield 'empty string' => [
            '',
            'Invalid regex: Internal error (code 1)',
        ];

        yield 'regular string' => [
            'foo',
            'Invalid regex: Internal error (code 1)',
        ];

        yield 'empty regex' => [
            '//',
            null,
        ];

        yield 'valid regex' => [
            '/foo/',
            null,
        ];

        yield 'invalid regex' => [
            '/foo/$',
            'Invalid regex: Internal error (code 1)',
        ];
    }
}
