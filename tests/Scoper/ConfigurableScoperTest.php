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

use PHPUnit\Framework\TestCase;
use function is_a;

/**
 * @covers \Humbug\PhpScoper\Scoper\ConfigurableScoper
 */
class ConfigurableScoperTest extends TestCase
{
    private ScoperStub $decoratedScoper;

    protected function setUp(): void
    {
        $this->decoratedScoper = new ScoperStub();
    }

    public function test_is_a_Scoper(): void
    {
        self::assertTrue(is_a(ConfigurableScoper::class, Scoper::class, true));
    }

    public function test_it_scopes_the_files_with_the_decorated_scoper(): void
    {
        $filePath = '/path/to/file.php';
        $contents = 'Original file content';

        $this->decoratedScoper->addConfig(
            $filePath,
            $contents,
            $expected = 'Decorated scoper contents',
        );

        $scoper = new ConfigurableScoper($this->decoratedScoper);

        $actual = $scoper->scope($filePath, $contents);

        self::assertSame($expected, $actual);
    }

    public function test_it_can_create_a_scoper_allowing_to_exclude_specific_files(): void
    {
        $whitelistedFiles = [
            '/path/to/whitelisted-file-1',
            '/path/to/whitelisted-file-2',
        ];

        $filePath = '/path/to/file.php';
        $contents = 'Original file content';

        $this->decoratedScoper->addConfig(
            ScoperStub::ANY_FILE_PATH,
            $contents,
            $expected = 'Decorated scoper contents',
        );

        $scoper = (new ConfigurableScoper($this->decoratedScoper))->withWhitelistedFiles(...$whitelistedFiles);

        foreach ($whitelistedFiles as $whitelistedFile) {
            $actual = $scoper->scope($whitelistedFile, $contents);

            self::assertSame($contents, $actual);
        }

        $actual = $scoper->scope($filePath, $contents);

        self::assertSame($expected, $actual);
    }
}
