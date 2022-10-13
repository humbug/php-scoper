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

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use function array_values;

/**
 * @covers \Humbug\PhpScoper\Configuration\ConfigurationKeys
 *
 * @internal
 */
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
        // TODO in PHP 8.0 pass ReflectionClassConstant::IS_PUBLIC as a filter
        //  and rename `$constants` to `$publicConstants`
        $constants = $configKeysReflection->getConstants();

        unset($constants['KEYWORDS']);

        foreach ($constants as $name => $value) {
            self::assertNonEmptyStringConstantValue(
                $value,
                $name,
            );
        }

        return array_values($constants);
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

        return $constants;
    }

    private static function assertNonEmptyStringConstantValue(
        mixed $value,
        string $name
    ): void {
        self::assertIsString($value, $name);
        self::assertNotSame('', $value, $name);
    }
}
