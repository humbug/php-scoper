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

function get_last_tag_name(): string
{
    $gitHubToken = getenv('GITHUB_TOKEN');

    $headerOption = false === $gitHubToken || '' === $gitHubToken
        ? ''
        : "-H \"Authorization: token $gitHubToken\""
    ;

    $lastReleaseEndpointContents = shell_exec(
        <<<BASH
curl -s $headerOption https://api.github.com/repos/humbug/php-scoper/releases/latest
BASH
    );

    if (null === $lastReleaseEndpointContents) {
        throw new RuntimeException('Could not retrieve the last release endpoint.');
    }

    $contents = json_decode($lastReleaseEndpointContents, false, 512, JSON_PRETTY_PRINT);

    if (JSON_ERROR_NONE !== json_last_error()) {
        // TODO: switch to safe json parsing in the future
        throw new RuntimeException(
            sprintf(
                'Could not parse the request contents: "%d: %s"',
                json_last_error(),
                json_last_error_msg()
            )
        );
    }

    if (false === isset($contents->tag_name) || false === is_string($contents->tag_name)) {
        throw new RuntimeException(
            sprintf(
                'No tag name could be found in: %s',
                $lastReleaseEndpointContents
            ),
            100
        );
    }

    if ('' !== $lastRelease = trim($contents->tag_name)) {
        return $lastRelease;
    }

    throw new RuntimeException('Invalid tag name found.');
}

function get_composer_root_version(string $lastTagName): string
{
    $tagParts = explode('.', $lastTagName);

    array_pop($tagParts);

    $tagParts[] = '99';

    return implode('.', $tagParts);
}
