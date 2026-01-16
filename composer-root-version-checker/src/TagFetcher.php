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

use Psr\Log\LoggerInterface;
use RuntimeException;
use Safe\Exceptions\ExecException;
use function Safe\shell_exec;
use function sprintf;
use const PHP_EOL;

final readonly class TagFetcher
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function fetchLastTag(): string
    {
        $lastRelease = TagParser::parse($this->fetchTags());

        $this->logger->info('Latest tag found: '.$lastRelease);

        return $lastRelease;
    }

    private function fetchTags(): string
    {
        $gitHubToken = getenv('PHP_SCOPER_GITHUB_TOKEN');

        $headerOption = false === $gitHubToken || '' === $gitHubToken
            ? ''
            : "-H \"Authorization: token {$gitHubToken}\"";

        $command = <<<SHELL
            curl -s {$headerOption} https://api.github.com/repos/humbug/php-scoper/tags?per_page=1
            SHELL;

        $this->logger->info(
            <<<EOF
                cURL command:
                $ {$command}
                EOF,
        );

        try {
            $responseContent = shell_exec($command);
        } catch (ExecException $failedToFetchTag) {
            throw new RuntimeException(
                sprintf(
                    'Could not retrieve the last release endpoint: %s',
                    $failedToFetchTag->getMessage(),
                ),
            );
        }

        $this->logger->info(
            sprintf(
                'Got the following response:%s%s',
                PHP_EOL,
                $responseContent,
            ),
        );

        return $responseContent;
    }
}
