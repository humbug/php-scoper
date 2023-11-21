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

namespace Humbug\PhpScoperComposerRootChecker\Tests;

use Humbug\PhpScoperComposerRootChecker\VersionCalculator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(VersionCalculator::class)]
final class VersionCalculatorTest extends TestCase
{
    #[DataProvider('tagProvider')]
    public function test_it_can_calculate_the_desired_composer_root_version_from_the_tag(
        string $tag,
        string $expected
    ): void {
        $actual = VersionCalculator::calculateDesiredVersion($tag);

        self::assertSame($expected, $actual);
    }

    public static function tagProvider(): iterable
    {
        yield 'first dev version' => [
            '0.17.0',
            '0.17.99',
        ];

        yield 'arbitrary dev version' => [
            '0.17.3',
            '0.17.99',
        ];

        yield 'stable version' => [
            '1.17.3',
            '1.17.99',
        ];

        yield 'RC version of unstable release' => [
            '0.17.12-RC.0',
            '0.17.99',
        ];

        yield 'RC (dash) version of unstable release' => [
            '0.17.12-RC-0',
            '0.17.99',
        ];

        yield 'RC (no separator) version of unstable release' => [
            '0.17.12-RC0',
            '0.17.99',
        ];

        yield 'alpha version of unstable release' => [
            '0.17.12-ALPHA.0',
            '0.17.99',
        ];

        yield 'alpha (dash) version of unstable release' => [
            '0.17.12-ALPHA-0',
            '0.17.99',
        ];

        yield 'alpha (no separator) version of unstable release' => [
            '0.17.12-ALPHA0',
            '0.17.99',
        ];

        yield 'beta version of unstable release' => [
            '0.17.12-BETA.0',
            '0.17.99',
        ];

        yield 'beta (dash) version of unstable release' => [
            '0.17.12-BETA-0',
            '0.17.99',
        ];

        yield 'beta (no separator) version of unstable release' => [
            '0.17.12-BETA0',
            '0.17.99',
        ];

        yield 'RC version of stable release' => [
            '1.17.12-RC.0',
            '1.17.99',
        ];

        yield 'RC (dash) version of stable release' => [
            '1.17.12-RC-0',
            '1.17.99',
        ];

        yield 'RC (no separator) version of stable release' => [
            '1.17.12-RC0',
            '1.17.99',
        ];

        yield 'alpha version of stable release' => [
            '1.17.12-ALPHA.0',
            '1.17.99',
        ];

        yield 'alpha (dash) version of stable release' => [
            '1.17.12-ALPHA-0',
            '1.17.99',
        ];

        yield 'alpha (no separator) version of stable release' => [
            '1.17.12-ALPHA0',
            '1.17.99',
        ];

        yield 'beta version of stable release' => [
            '1.17.12-BETA.0',
            '1.17.99',
        ];

        yield 'beta (dash) version of stable release' => [
            '1.17.12-BETA-0',
            '1.17.99',
        ];

        yield 'beta (no separator) version of stable release' => [
            '1.17.12-BETA0',
            '1.17.99',
        ];

        yield 'bug case #1' => [
            '0.18.0-rc.0',
            '0.18.99',
        ];
    }
}
