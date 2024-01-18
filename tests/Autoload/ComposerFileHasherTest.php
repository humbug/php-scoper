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

namespace Autoload;

use Humbug\PhpScoper\Autoload\ComposerFileHasher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(ComposerFileHasher::class)]
final class ComposerFileHasherTest extends TestCase
{
    #[DataProvider('filesProvider')]
    public function test_it_can_get_the_composer_hash_of_the_files(
        string $vendorDir,
        string $rootDir,
        array $filePaths,
        array $expected,
    ): void {
        $hasher = ComposerFileHasher::create($vendorDir, $rootDir, $filePaths);

        $actual = $hasher->generateHashes();

        self::assertSame($expected, $actual);
    }

    public static function filesProvider(): iterable
    {
        yield [
            '/path/to/project/vendor',
            '/path/to/project',
            [
                '/path/to/project/src/App.php',
                '/path/to/project/vendor/humbug/box/src/Box.php',
            ],
            [
                md5('__root__:src/App.php'),
                md5('humbug/box:src/Box.php'),
            ],
        ];
    }
}
