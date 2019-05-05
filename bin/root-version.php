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
    $tags = json_decode(
        file_get_contents(
            'https://api.github.com/repos/humbug/php-scoper/tags',
            false,
            stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => <<<'EOF'
    User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36;
    Content-Type: text/json;
EOF
                ],
            ])
        )
    );

    return $tags[0]->name;
}

function get_composer_root_version(string $lastTagName): string
{
    $tagParts = explode('.', $lastTagName);

    array_pop($tagParts);

    $tagParts[] = '99';

    return implode('.', $tagParts);
}
