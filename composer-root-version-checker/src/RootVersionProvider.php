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

namespace Humbug\PhpScoperComposerRootChecker;

use RuntimeException;
use function Safe\file_get_contents;
use function Safe\preg_match;

final class RootVersionProvider
{
    private const COMPOSER_ROOT_VERSION_PATH = __DIR__.'/../../.composer-root-version';

    public static function provideCurrentVersion(): string
    {
        return self::parseVersion(
            file_get_contents(self::COMPOSER_ROOT_VERSION_PATH),
        );
    }

    public static function parseVersion(string $content): string
    {
        $result = preg_match(
            '/COMPOSER_ROOT_VERSION=\'(?<version>.*?)\'/',
            $content,
            $matches,
        );

        if (0 === $result) {
            throw new RuntimeException('Could not parse the Composer root version.');
        }

        return $matches['version'];
    }

    private function __construct()
    {
    }
}
