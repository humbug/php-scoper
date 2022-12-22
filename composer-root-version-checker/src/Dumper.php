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
use function getenv;
use function preg_replace;
use function Safe\file_get_contents;
use function Safe\file_put_contents;
use function sprintf;

final class Dumper
{
    private const COMPOSER_ROOT_VERSION_PATH = __DIR__.'/../../.composer-root-version';
    private const SCRUTINIZER_CONFIG_PATH = __DIR__.'/../../.scrutinizer.yml';

    public static function dump(): void
    {
        $logger = new Logger();
        $fetcher = new TagFetcher($logger);

        try {
            $lastTag = $fetcher->fetchLastTag();
        } catch (CouldNotParseTag $couldNotParseTag) {
            $logger->notice(
                sprintf(
                    'Skipped: %s',
                    $couldNotParseTag->getMessage(),
                ),
            );

            // This is the GitHub API playing tricks on us... I could not find a way to reliably fix it so it is just better
            // to avoid bailing out because of it for now.
            return;
        } catch (RuntimeException $couldNotFetchTag) {
            if (false !== getenv('PHP_SCOPER_GITHUB_TOKEN') && false === getenv('GITHUB_TOKEN')) {
                $logger->info('Skipped: not GitHub token configured. Export the environment variable "PHP_SCOPER_GITHUB_TOKEN" or "GITHUB_TOKEN" to fix this.');

                // Ignore this PR to avoid too many builds to fail untimely or locally due to API rate limits because the last
                // release version could not be retrieved.
                return;
            }

            throw $couldNotFetchTag;
        }

        $composerRootVersion = VersionCalculator::calculateDesiredVersion($lastTag);

        file_put_contents(
            self::COMPOSER_ROOT_VERSION_PATH,
            sprintf(
                <<<'BASH'
                    COMPOSER_ROOT_VERSION='%s'

                    BASH,
                $composerRootVersion,
            ),
        );

        file_put_contents(
            self::SCRUTINIZER_CONFIG_PATH,
            preg_replace(
                '/COMPOSER_ROOT_VERSION: \'.*?\'/',
                sprintf(
                    'COMPOSER_ROOT_VERSION: \'%s\'',
                    $composerRootVersion,
                ),
                file_get_contents(self::SCRUTINIZER_CONFIG_PATH),
            ),
        );

        $logger->notice(
            sprintf(
                'Dumped COMPOSER_ROOT_VERSION=%s',
                $composerRootVersion,
            ),
        );
    }

    private function __construct()
    {
    }
}
