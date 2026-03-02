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

use Humbug\PhpScoper\Configuration\Throwable\InvalidConfigurationValue;
use Humbug\PhpScoper\Patcher\FakePatcher;
use Humbug\PhpScoper\Patcher\Patcher;
use Humbug\PhpScoper\Patcher\PatcherChain;
use PhpParser\PhpVersion;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Configuration::class)]
final class ConfigurationTest extends TestCase
{
    /**
     * @param non-empty-string $prefix
     */
    #[DataProvider('prefixProvider')]
    public function test_it_validates_the_prefix(
        string $prefix,
        string $expectedExceptionMessage,
    ): void {
        $this->expectException(InvalidConfigurationValue::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        new Configuration(
            null,
            null,
            $prefix,
            null,
            [],
            [],
            new PatcherChain([]),
            SymbolsConfiguration::create(),
        );
    }

    public static function prefixProvider(): iterable
    {
        yield [
            ';',
            'The prefix needs to be composed solely of letters, digits and backslashes (as namespace separators). Got ";".',
        ];

        yield [
            'App\\\Foo',
            'Invalid namespace separator sequence. Got "App\\\Foo".',
        ];
    }

    public function test_it_can_create_a_new_instance_with_a_different_prefix(): void
    {
        $values = [
            '/path/to/config',
            '/path/to/outputDir',
            'initialPrefix',
            null,
            ['/path/to/fileA' => ['/path/to/fileA', 'fileAContent']],
            ['/path/to/fileB' => ['/path/to/fileB', 'fileBContent']],
            new FakePatcher(),
            SymbolsConfiguration::create(),
        ];

        $config = new Configuration(...$values);

        // Sanity check
        self::assertStateIs($config, ...$values);

        $newConfig = $config->withPrefix('newPrefix');

        $expectedNewConfigValues = $values;
        $expectedNewConfigValues[2] = 'newPrefix';

        self::assertStateIs($config, ...$values);
        self::assertStateIs($newConfig, ...$expectedNewConfigValues);
    }

    public function test_it_can_create_a_new_instance_with_a_different_patcher(): void
    {
        $values = [
            '/path/to/config',
            '/path/to/outputDir',
            'initialPrefix',
            null,
            ['/path/to/fileA' => ['/path/to/fileA', 'fileAContent']],
            ['/path/to/fileB' => ['/path/to/fileB', 'fileBContent']],
            new FakePatcher(),
            SymbolsConfiguration::create(),
        ];

        $config = new Configuration(...$values);

        // Sanity check
        self::assertStateIs($config, ...$values);

        $newPatcher = new FakePatcher();
        $newConfig = $config->withPatcher($newPatcher);

        $expectedNewConfigValues = $values;
        $expectedNewConfigValues[6] = $newPatcher;

        self::assertStateIs($config, ...$values);
        self::assertStateIs($newConfig, ...$expectedNewConfigValues);
    }

    public static function assertStateIs(
        Configuration $configuration,
        ?string $expectedPath,
        ?string $expectedOutputDir,
        string $expectedPrefix,
        ?PhpVersion $expectedPhpVersion,
        array $expectedFilesWithContents,
        array $expectedExcludedFilesWithContents,
        Patcher $expectedPatcher,
        SymbolsConfiguration $expectedSymbolsConfiguration,
    ): void {
        self::assertSame($expectedPath, $configuration->getPath());
        self::assertSame($expectedOutputDir, $configuration->getOutputDir());
        self::assertSame($expectedPrefix, $configuration->getPrefix());
        self::assertEquals($expectedPhpVersion, $configuration->getPhpVersion());
        self::assertEqualsCanonicalizing(
            $expectedFilesWithContents,
            $configuration->getFilesWithContents(),
        );
        self::assertEqualsCanonicalizing(
            $expectedExcludedFilesWithContents,
            $configuration->getExcludedFilesWithContents(),
        );
        self::assertEquals($expectedPatcher, $configuration->getPatcher());
        self::assertEquals(
            $expectedSymbolsConfiguration,
            $configuration->getSymbolsConfiguration(),
        );
    }
}
