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

use Roave\BetterReflection\Identifier\Exception\InvalidIdentifierName;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflector\Exception\IdentifierNotFound;
use function array_key_exists;
use function array_values;
use function get_defined_constants;
use Roave\BetterReflection\Reflector\Reflector as BetterReflectionReflector;
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

    private $classReflector;
    private $functionReflector;

    /**
     * @var <string, mixed> Lazily instantiated internal constants.
     */
    private $constants;

    public function __construct(
        BetterReflectionReflector $classReflector,
        BetterReflectionReflector $functionReflector
    ) {
        $this->classReflector = $classReflector;
        $this->functionReflector = $functionReflector;
    }

    public function isClassInternal(string $name): bool
    {
        try {
            /** @var ReflectionClass $classReflection */
            $classReflection = $this->classReflector->reflect($name);

            return $classReflection->isInternal();
        } catch (IdentifierNotFound | InvalidIdentifierName $exception) {
            return false;
        }
    }

    public function isFunctionInternal(string $name): bool
    {
        try {
            /** @var ReflectionFunction $functionReflection */
            $functionReflection = $this->functionReflector->reflect($name);

            return $functionReflection->isInternal();
        } catch (IdentifierNotFound | InvalidIdentifierName $exception) {
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
