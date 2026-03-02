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

namespace Humbug\PhpScoper\Configuration;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionClassConstant;
use function array_values;

/**
 * @internal
 */
#[CoversClass(ConfigurationKeys::class)]
final class ConfigurationKeysTest extends TestCase
{
    public function test_keywords_contains_all_the_known_configuration_keys(): void
    {
        $configKeys = self::retrieveConfigurationKeys();
        $keywords = self::retrieveKeywords();

        self::assertEqualsCanonicalizing($configKeys, $keywords);
    }

    /**
     * @return list<string>
     */
    private static function retrieveConfigurationKeys(): array
    {
        $configKeysReflection = new ReflectionClass(ConfigurationKeys::class);
        $publicConstants = $configKeysReflection->getConstants(ReflectionClassConstant::IS_PUBLIC);

        unset($publicConstants['KEYWORDS']);

        foreach ($publicConstants as $name => $value) {
            self::assertNonEmptyStringConstantValue(
                $value,
                $name,
            );
        }

        /** @phpstan-ignore return.type */
        return array_values($publicConstants);
    }

    /**
     * @return list<string>
     */
    private static function retrieveKeywords(): array
    {
        $configKeysReflection = new ReflectionClass(ConfigurationKeys::class);

        $constants = $configKeysReflection->getConstant('KEYWORDS');

        self::assertIsArray($constants);

        foreach ($constants as $index => $value) {
            self::assertNonEmptyStringConstantValue(
                $value,
                (string) $index,
            );
        }

        self::assertIsList($constants);

        /** @phpstan-ignore return.type */
        return $constants;
    }

    /**
     * @phpstan-assert non-empty-string $value
     */
    private static function assertNonEmptyStringConstantValue(
        mixed $value,
        string $name,
    ): void {
        self::assertIsString($value, $name);
        self::assertNotSame('', $value, $name);
    }
}
