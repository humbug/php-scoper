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

// Safe JSON functions; ensures the encode/decode always throws an exception on error.
// This code can be removed once 7.3.0 is required as a minimal version by leveraging
// JSON_THROW_ON_ERROR.

namespace Humbug\PhpScoper {
    use JsonException;
use function class_exists;
use function error_clear_last;
use function json_decode as original_json_decode;
use function json_encode as original_json_encode;
use function json_last_error;
use function json_last_error_msg as original_json_last_error_msg;

    /**
     * @throws JsonException
     *
     * @return object|array
     */
    function json_decode(string $json, bool $assoc = false, int $depth = 512, int $options = 0)
    {
        error_clear_last();

        $result = original_json_decode($json, $assoc, $depth, $options);

        if (null === $result) {
            throw create_json_exception();
        }

        return $result;
    }

    function json_encode($value, int $options = 0, int $depth = 512): string
    {
        error_clear_last();

        $result = original_json_encode($value, $options, $depth);

        if (false === $result) {
            throw create_json_exception();
        }

        return $result;
    }

    /**
     * @internal
     */
    function create_json_exception(): JsonException
    {
        return new JsonException(original_json_last_error_msg(), json_last_error());
    }
}

namespace {
    if (false === class_exists(JsonException::class, false)) {
        class JsonException extends Exception
        {
        }
    }
}
