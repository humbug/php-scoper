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

use Humbug\PhpScoper\Container;
use Humbug\PhpScoper\FileSystemTestCase;
use Humbug\PhpScoper\Patcher\ComposerPatcher;
use Humbug\PhpScoper\Patcher\PatcherChain;
use Humbug\PhpScoper\Patcher\SymfonyPatcher;
use Humbug\PhpScoper\Symbol\NamespaceRegistry;
use InvalidArgumentException;
use function array_keys;
use function KevinGH\Box\FileSystem\dump_file;
use function Safe\touch;
use const DIRECTORY_SEPARATOR;

/**
 * @covers \Humbug\PhpScoper\Configuration\ConfigurationFactory
 */
class ConfigurationFactoryTest extends FileSystemTestCase
{
    private ConfigurationFactory $configFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configFactory = (new Container())->getConfigurationFactory();
    }

    public function test_it_can_be_created_without_a_file(): void
    {
        $configuration = $this->configFactory->create();

        self::assertSame([], $configuration->getWhitelistedFilesWithContents());
        self::assertEquals(
            SymbolsConfiguration::create(),
            $configuration->getSymbolsConfiguration(),
        );
        self::assertNull($configuration->getPath());
        self::assertMatchesRegularExpression('/_PhpScoper[a-z\d]{12}/', $configuration->getPrefix());
        self::assertSame([], $configuration->getFilesWithContents());
        self::assertEquals(
            new PatcherChain([
                new ComposerPatcher(),
                new SymfonyPatcher(),
            ]),
            $configuration->getPatcher(),
        );
    }

    public function test_it_cannot_create_a_configuration_with_an_invalid_key(): void
    {
        self::dumpStandardConfigFile(
            <<<'PHP'
            <?php
            
            return [
                'unknown key' => 'val',
            ];
            PHP,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid configuration key value "unknown key" found.');

        $this->createConfigFromStandardFile();
    }

    public function test_it_can_create_a_complete_configuration(): void
    {
        self::dumpStandardConfigFile(
            <<<'PHP'
            <?php
            
            return [
                'prefix' => 'MyPrefix',
                'files-whitelist' => ['file1', 'file2'],
                'patchers' => [],
                'finders' => [],
                
                'whitelist' => ['Foo', 'Bar\*'],
                
                'expose-global-constants' => false,
                'expose-global-classes' => false,
                'expose-global-functions' => false,
                'expose-namespaces' => ['PHPUnit\Runner'],
                'expose-constants' => [],
                'expose-classes' => [],
                'expose-functions' => [],
                
                'exclude-namespaces' => ['PHPUnit\Runner'],
                'exclude-constants' => [],
                'exclude-classes' => [],
                'exclude-functions' => [],
            ];
            PHP,
        );
        touch('file1');

        $rawConfig = include $this->tmp.DIRECTORY_SEPARATOR.'scoper.inc.php';

        self::assertEqualsCanonicalizing(
            ConfigurationKeys::KEYWORDS,
            array_keys($rawConfig),
            'The complete config must contain all the known configuration keys',
        );

        $configuration = $this->createConfigFromStandardFile();

        self::assertSame($this->tmp.DIRECTORY_SEPARATOR.'scoper.inc.php', $configuration->getPath());
        self::assertSame('MyPrefix', $configuration->getPrefix());
        self::assertSame([], $configuration->getFilesWithContents());
        self::assertSame(
            [
                $this->tmp.DIRECTORY_SEPARATOR.'file1' => [
                    $this->tmp.DIRECTORY_SEPARATOR.'file1',
                    '',
                ],
            ],
            $configuration->getWhitelistedFilesWithContents(),
        );
        self::assertEquals(
            new PatcherChain([
                new ComposerPatcher(),
                new SymfonyPatcher(),
            ]),
            $configuration->getPatcher(),
        );
        self::assertEquals(
            SymbolsConfiguration::create(
                false,
                false,
                false,
                NamespaceRegistry::create(
                    [
                        'PHPUnit\Runner',
                        'Bar',
                    ],
                ),
                null,
                ['foo'],
                [],
                ['foo'],
                [],
                ['Foo'],
                [],
            ),
            $configuration->getSymbolsConfiguration(),
        );
    }

    private static function dumpStandardConfigFile(string $contents): void
    {
        dump_file('scoper.inc.php', $contents);
    }

    private function createConfigFromStandardFile(): Configuration
    {
        return $this->configFactory->create(
            $this->tmp.DIRECTORY_SEPARATOR.'scoper.inc.php',
        );
    }
}
