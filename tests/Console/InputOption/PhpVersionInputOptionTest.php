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

namespace Humbug\PhpScoper\Console\InputOption;

use Fidry\Console\IO;
use PhpParser\PhpVersion;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * @internal
 */
#[CoversClass(PhpVersionInputOption::class)]
final class PhpVersionInputOptionTest extends TestCase
{
    #[DataProvider('provideInput')]
    public function test_it_can_parse_the_php_version(IO $io, ?PhpVersion $expected): void
    {
        $actual = PhpVersionInputOption::getPhpVersion($io);

        self::assertEquals($expected, $actual);
    }

    public static function provideInput(): iterable
    {
        yield [
            self::createIO(null),
            null,
        ];

        yield [
            self::createIO('8.2'),
            PhpVersion::fromComponents(8, 2),
        ];
    }

    private static function createIO(?string $value): IO
    {
        $input = new ArrayInput(
            null === $value
                ? []
                : [
                    '--php-version' => $value,
                ],
        );
        $input->bind(new InputDefinition([PhpVersionInputOption::createInputOption()]));

        return IO::createNull()->withInput($input);
    }
}
