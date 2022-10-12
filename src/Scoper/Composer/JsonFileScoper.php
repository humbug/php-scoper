<?php

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Humbug\PhpScoper\Scoper\Composer;

use Humbug\PhpScoper\Scoper\Scoper;
use InvalidArgumentException;
use stdClass;
use function gettype;
use function preg_match as native_preg_match;
use function Safe\json_decode;
use function Safe\json_encode;
use function Safe\sprintf;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

final class JsonFileScoper implements Scoper
{
    private Scoper $decoratedScoper;
    private AutoloadPrefixer $autoloadPrefixer;

    public function __construct(
        Scoper $decoratedScoper,
        AutoloadPrefixer $autoloadPrefixer
    ) {
        $this->decoratedScoper = $decoratedScoper;
        $this->autoloadPrefixer = $autoloadPrefixer;
    }

    /**
     * Scopes PHP and JSON files related to Composer.
     */
    public function scope(string $filePath, string $contents): string
    {
        if (1 !== native_preg_match('/composer\.json$/', $filePath)) {
            return $this->decoratedScoper->scope($filePath, $contents);
        }

        $decodedJson = self::decodeContents($contents);

        $decodedJson = $this->autoloadPrefixer->prefixPackageAutoloadStatements($decodedJson);

        return json_encode(
            $decodedJson,
            JSON_PRETTY_PRINT
        );
    }

    private static function decodeContents(string $contents): stdClass
    {
        $decodedJson = json_decode($contents, false, 512, JSON_THROW_ON_ERROR);

        if ($decodedJson instanceof stdClass) {
            return $decodedJson;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Expected the decoded JSON to be an stdClass instance, got "%s" instead',
                gettype($decodedJson),
            )
        );
    }
}
