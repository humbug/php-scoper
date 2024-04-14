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

namespace Humbug\PhpScoper\Scoper\Spec;

use Humbug\PhpScoper\Configuration\ConfigurationKeys;
use Humbug\PhpScoper\Configuration\RegexChecker;
use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Configuration\SymbolsConfigurationFactory;
use Humbug\PhpScoper\NotInstantiable;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;
use function array_diff;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function implode;
use function is_array;
use function is_int;
use function is_string;
use function Safe\preg_split;
use function sprintf;

/**
 * @internal
 */
#[Group('integration')]
class SpecParser extends TestCase
{
    use NotInstantiable;

    private const SPECS_META_KEYS = [
        'minPhpVersion',
        'maxPhpVersion',
        'title',
        ConfigurationKeys::PREFIX_KEYWORD,
        // SPECS_CONFIG_KEYS included
        'expected-recorded-classes',
        'expected-recorded-functions',
    ];

    // Keys allowed on a spec level
    private const SPECS_SPEC_KEYS = [
        ConfigurationKeys::PREFIX_KEYWORD,
        // SPECS_CONFIG_KEYS included
        'minPhpVersion',
        'maxPhpVersion',
        'expected-recorded-classes',
        'expected-recorded-functions',
        'payload',
    ];

    // Keys kept and used to build the symbols configuration
    private const SPECS_CONFIG_KEYS = [
        ConfigurationKeys::EXPOSE_GLOBAL_CONSTANTS_KEYWORD,
        ConfigurationKeys::EXPOSE_GLOBAL_CLASSES_KEYWORD,
        ConfigurationKeys::EXPOSE_GLOBAL_FUNCTIONS_KEYWORD,

        ConfigurationKeys::EXPOSE_NAMESPACES_KEYWORD,
        ConfigurationKeys::EXPOSE_CLASSES_SYMBOLS_KEYWORD,
        ConfigurationKeys::EXPOSE_FUNCTIONS_SYMBOLS_KEYWORD,
        ConfigurationKeys::EXPOSE_CONSTANTS_SYMBOLS_KEYWORD,

        ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD,
        ConfigurationKeys::CLASSES_INTERNAL_SYMBOLS_KEYWORD,
        ConfigurationKeys::FUNCTIONS_INTERNAL_SYMBOLS_KEYWORD,
        ConfigurationKeys::CONSTANTS_INTERNAL_SYMBOLS_KEYWORD,
    ];

    /**
     * @throws UnparsableFile
     *
     * @return iterable<SpecScenario>
     */
    public static function parseSpecFile(
        string $sourceDir,
        SplFileInfo $file,
    ): iterable {
        try {
            $specs = include $file;

            self::checkSpecFileSchema($specs);

            $meta = $specs['meta'];
            unset($specs['meta']);

            foreach ($specs as $title => $spec) {
                yield self::parseSpec(
                    basename($sourceDir).'/'.$file->getRelativePathname(),
                    $meta,
                    $title,
                    $spec,
                );
            }
        } catch (Throwable $throwable) {
            throw UnparsableFile::create($file, $throwable);
        }
    }

    /**
     * @phpstan-assert array{'meta': array, array-key: string|array} $specs
     */
    private static function checkSpecFileSchema(mixed $specs): void
    {
        Assert::assertIsArray($specs);

        Assert::assertArrayHasKey('meta', $specs);
        Assert::assertIsArray($specs['meta']);

        foreach ($specs as $key => $spec) {
            if ('meta' === $key) {
                continue;
            }

            Assert::assertTrue(is_string($spec) || is_array($spec));
        }
    }

    private static function parseSpec(
        string $file,
        array $meta,
        int|string $title,
        array|string $fixtureSet,
    ): SpecScenario {
        static $specMetaKeys;
        static $specKeys;

        if (!isset($specMetaKeys)) {
            $specMetaKeys = [
                ...self::SPECS_META_KEYS,
                ...self::SPECS_CONFIG_KEYS,
            ];
        }

        if (!isset($specKeys)) {
            $specKeys = [
                ...self::SPECS_SPEC_KEYS,
                ...self::SPECS_CONFIG_KEYS,
            ];
        }

        $completeTitle = sprintf(
            '[%s] %s',
            $meta['title'],
            is_int($title) ? 'spec #'.$title : $title,
        );

        $payload = is_string($fixtureSet) ? $fixtureSet : $fixtureSet['payload'];

        $payloadParts = preg_split("/\n----(?:\n|$)/", $payload);

        self::assertSame(
            [],
            $diff = array_diff(
                array_keys($meta),
                $specMetaKeys,
            ),
            sprintf(
                'Expected the keys found in the meta section to be known keys, unknown keys: "%s"',
                implode('", "', $diff),
            ),
        );

        if (is_array($fixtureSet)) {
            $diff = array_diff(
                array_keys($fixtureSet),
                $specKeys,
            );

            self::assertSame(
                [],
                $diff,
                sprintf(
                    'Expected the keys found in the spec section to be known keys, unknown keys: "%s"',
                    implode('", "', $diff),
                ),
            );
        }

        return new SpecScenario(
            $fixtureSet['minPhpVersion'] ?? $meta['minPhpVersion'] ?? null,
            $fixtureSet['maxPhpVersion'] ?? $meta['maxPhpVersion'] ?? null,
            $file,
            $completeTitle,
            $payloadParts[0],
            $fixtureSet[ConfigurationKeys::PREFIX_KEYWORD] ?? $meta[ConfigurationKeys::PREFIX_KEYWORD],
            self::createSymbolsConfiguration(
                $file,
                is_string($fixtureSet) ? [] : $fixtureSet,
                $meta,
            ),
            '' === $payloadParts[1] ? null : $payloadParts[1],   // Expected output; null means an exception is expected,
            $fixtureSet['expected-recorded-classes'] ?? $meta['expected-recorded-classes'],
            $fixtureSet['expected-recorded-functions'] ?? $meta['expected-recorded-functions'],
        );
    }

    private static function createSymbolsConfiguration(
        string $file,
        array|string $fixtureSet,
        array $meta
    ): SymbolsConfiguration {
        if (is_string($fixtureSet)) {
            $fixtureSet = [];
        }

        $mergedConfig = array_merge($meta, $fixtureSet);

        $config = [];

        foreach (self::SPECS_CONFIG_KEYS as $key) {
            if (!array_key_exists($key, $mergedConfig)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Missing the key "%s" for the file "%s"',
                        $key,
                        $file,
                    ),
                );
            }

            $config[$key] = $mergedConfig[$key];
        }

        return (new SymbolsConfigurationFactory(new RegexChecker()))->createSymbolsConfiguration($config);
    }
}
