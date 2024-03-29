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
use function sprintf;

final class Checker
{
    public static function check(): int
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
            return 0;
        } catch (RuntimeException $couldNotFetchTag) {
            if (false !== getenv('PHP_SCOPER_GITHUB_TOKEN') && false === getenv('GITHUB_TOKEN')) {
                $logger->info('Skipped: not GitHub token configured. Export the environment variable "PHP_SCOPER_GITHUB_TOKEN" or "GITHUB_TOKEN" to fix this.');

                // Ignore this PR to avoid too many builds to fail untimely or locally due to API rate limits because the last
                // release version could not be retrieved.
                return 0;
            }

            throw $couldNotFetchTag;
        }

        $expectedComposerRootVersion = VersionCalculator::calculateDesiredVersion($lastTag);
        $currentRootVersion = RootVersionProvider::provideCurrentVersion();

        if ($expectedComposerRootVersion === $currentRootVersion) {
            $logger->notice(
                sprintf(
                    'The tag is up to date (%s).',
                    $currentRootVersion,
                ),
            );

            return 0;
        }

        $logger->error(
            sprintf(
                'Expected the COMPOSER_ROOT_VERSION value to be "%s" but got "%s" instead.'.PHP_EOL,
                $expectedComposerRootVersion,
                $currentRootVersion,
            ),
        );

        return 1;
    }

    private function __construct()
    {
    }
}
