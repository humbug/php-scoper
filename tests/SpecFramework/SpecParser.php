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

namespace Humbug\PhpScoper\SpecFramework;

use Humbug\PhpScoper\Configuration\RegexChecker;
use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Configuration\SymbolsConfigurationFactory;
use Humbug\PhpScoper\NotInstantiable;
use Humbug\PhpScoper\SpecFramework\Config\Meta;
use Humbug\PhpScoper\SpecFramework\Config\SpecWithConfig;
use Humbug\PhpScoper\SpecFramework\Throwable\UnparsableFile;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;
use function array_merge;
use function is_int;
use function is_string;
use function sprintf;

/**
 * @internal
 */
#[Group('integration')]
class SpecParser extends TestCase
{
    use NotInstantiable;

    /**
     * @throws UnparsableFile
     *
     * @return iterable<string, SpecScenario>
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
                $relativePath = basename($sourceDir).'/'.$file->getRelativePathname();

                yield $relativePath.': '.$title => self::parseSpec(
                    $relativePath,
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
     * @phpstan-assert array{'meta': Meta, array-key: string|SpecWithConfig} $specs
     */
    private static function checkSpecFileSchema(mixed $specs): void
    {
        Assert::assertIsArray($specs);

        Assert::assertArrayHasKey('meta', $specs);
        Assert::assertInstanceOf(Meta::class, $specs['meta']);

        foreach ($specs as $key => $spec) {
            if ('meta' === $key) {
                continue;
            }

            Assert::assertTrue(is_string($spec) || $spec instanceof SpecWithConfig);
        }
    }

    private static function parseSpec(
        string $file,
        Meta $meta,
        int|string $title,
        SpecWithConfig|string $specWithConfigOrSimpleSpec,
    ): SpecScenario {
        $completeTitle = sprintf(
            '[%s] %s',
            $meta->title,
            is_int($title) ? 'spec #'.$title : $title,
        );

        $specWithConfig = is_string($specWithConfigOrSimpleSpec)
            ? SpecWithConfig::fromSimpleSpec($specWithConfigOrSimpleSpec)
            : $specWithConfigOrSimpleSpec;

        return new SpecScenario(
            $specWithConfig->minPhpVersion ?? $meta->minPhpVersion ?? null,
            $specWithConfig->maxPhpVersion ?? $meta->maxPhpVersion ?? null,
            $file,
            $completeTitle,
            $specWithConfig->inputCode,
            $specWithConfigOrSimpleSpec->prefix ?? $meta->prefix,
            self::createSymbolsConfiguration($specWithConfig, $meta),
            $specWithConfig->expectedOutputCode,
            $specWithConfigOrSimpleSpec->expectedRecordedClasses ?? $meta->expectedRecordedClasses,
            $specWithConfigOrSimpleSpec->expectedRecordedFunctions ?? $meta->expectedRecordedFunctions,
        );
    }

    private static function createSymbolsConfiguration(
        SpecWithConfig $specWithConfig,
        Meta $meta,
    ): SymbolsConfiguration {
        static $factory;

        if (!isset($factory)) {
            $factory = new SymbolsConfigurationFactory(new RegexChecker());
        }

        $mergedConfig = array_merge(
            $meta->getSymbolsConfig(),
            $specWithConfig->getSymbolsConfig(),
        );

        return $factory->createSymbolsConfiguration($mergedConfig);
    }
}
