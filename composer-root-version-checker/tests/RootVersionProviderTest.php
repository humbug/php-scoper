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

use Humbug\PhpScoperComposerRootChecker\RootVersionProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoperComposerRootChecker\RootVersionProvider
 *
 * @internal
 */
final class RootVersionProviderTest extends TestCase
{
    /**
     * @dataProvider contentProvider
     */
    public function test_it_can_parse_the_composer_root_version(
        string $content,
        string $expected
    ): void {
        $actual = RootVersionProvider::parseVersion($content);

        self::assertSame($expected, $actual);
    }

    public static function contentProvider(): iterable
    {
        yield 'nominal' => [
            <<<'EOF'
                COMPOSER_ROOT_VERSION='0.17.99'
                EOF,
            '0.17.99',
        ];

        yield 'blank ending line' => [
            <<<'EOF'
                COMPOSER_ROOT_VERSION='0.17.99'

                EOF,
            '0.17.99',
        ];

        yield 'shell script' => [
            <<<'EOF'
                #!/bin/sh

                export COMPOSER_ROOT_VERSION='0.17.99'
                EOF,
            '0.17.99',
        ];

        yield 'bash script' => [
            <<<'EOF'
                #!/usr/bin/env bash

                export COMPOSER_ROOT_VERSION='0.17.99'

                EOF,
            '0.17.99',
        ];
    }

    public function test_it_can_provide_the_current_composer_root_version(): void
    {
        self::assertNotEmpty(RootVersionProvider::provideCurrentVersion());
    }
}
