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

namespace Humbug\PhpScoper;

use ReflectionException;
use ReflectionFunction;
use Roave\BetterReflection\Identifier\Exception\InvalidIdentifierName;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\Reflector\Exception\IdentifierNotFound;
use function array_key_exists;
use function array_values;
use function get_defined_constants;
use function strtoupper;

/**
 * Main class used to determine if a given symbol is internal or not. As of the time of writing, it leverages
 * Roave\BetterReflection to determine if given class is internal or not which allows to do reflection:.
 *
 * - Without loading the code scanned code at all
 * - Do reliable reflection against non-loaded APIs, i.e. a class from a non-loaded extension will properly appear
 *   as internal whereas the regular reflection would not.
 *
 * However Roave\BetterReflection is still not supporting constants and functions hence requires some hacks here
 * meanwhile.
 *
 * @private
 */
final class Reflector
{
    private const KNOWN_INTERNAL_CONSTANTS = [
        'CURLM_ADDED_ALREADY' => true,
        'JSON_UNESCAPED_LINE_TERMINATORS' => true,
        'OPENSSL_DONT_ZERO_PAD_KEY' => true,
        'PHP_FD_SETSIZE' => true,
        'PHP_INT_MIN' => true,
        'PHP_OS_FAMILY' => true,
    ];

    private const KNOWN_INTERNAL_FUNCTIONS = [
        'deflate_add' => true,
        'deflate_init' => true,
        'error_clear_last' => true,
        'ftp_append' => true,
        'hash_hkdf' => true,
        'inflate_add' => true,
        'inflate_init' => true,
        'intdiv' => true,
        'is_iterable' => true,
        'openssl_pkcs7_read' => true,
        'pcntl_signal_get_handler' => true,
        'preg_replace_callback_array' => true,
        'sapi_windows_vt100_support' => true,
        'socket_export_stream' => true,
        'spl_object_id' => true,
        'stream_isatty' => true,
        'utf8_decode' => true,
        'utf8_encode' => true,
        'zend_loader_file_encoded' => true,
    ];

    private $classReflector;
    private $constants;

    public function __construct(ClassReflector $classReflector)
    {
        $this->classReflector = $classReflector;
    }

    public function isClassInternal(string $name): bool
    {
        try {
            return $this->classReflector->reflect($name)->isInternal();
        } catch (IdentifierNotFound | InvalidIdentifierName $exception) {
            return false;
        }
    }

    public function isFunctionInternal(string $name): bool
    {
        if (array_key_exists($name, self::KNOWN_INTERNAL_FUNCTIONS)) {
            return true;
        }

        try {
            return (new ReflectionFunction($name))->isInternal();
        } catch (ReflectionException $exception) {
            return false;
        }
    }

    public function isConstantInternal(string $name): bool
    {
        if (array_key_exists($name, self::KNOWN_INTERNAL_CONSTANTS)) {
            return true;
        }

        return array_key_exists(strtoupper($name), $this->getInternalConstants());
    }

    private function getInternalConstants(): array
    {
        if (null !== $this->constants) {
            return $this->constants;
        }

        $constants = get_defined_constants(true);

        unset($constants['user']);

        $this->constants = array_merge(...array_values($constants));

        return $this->constants;
    }
}
