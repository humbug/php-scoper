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

use Humbug\PhpScoper\Configuration\RegexChecker;
use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Configuration\SymbolsConfigurationFactory;
use Humbug\PhpScoper\NotInstantiable;
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

            foreach ($specs as $fixtureTitle => $fixtureSet) {
                yield self::parseSpec(
                    basename($sourceDir).'/'.$file->getRelativePathname(),
                    $meta,
                    $fixtureTitle,
                    $fixtureSet,
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
    ): array {
        $completeTitle = sprintf(
            '[%s] %s',
            $meta->title,
            is_int($title) ? 'spec #'.$title : $title,
        );

        $specWithConfig = is_string($specWithConfigOrSimpleSpec)
            ? SpecWithConfig::fromSimpleSpec($specWithConfigOrSimpleSpec)
            : $specWithConfigOrSimpleSpec;

        return [
            $file,
            $completeTitle,
            $specWithConfig->inputCode,
            $specWithConfigOrSimpleSpec->prefix ?? $meta->prefix,
            self::createSymbolsConfiguration($specWithConfig, $meta),
            $specWithConfig->expectedOutputCode,
            $specWithConfigOrSimpleSpec->expectedRecordedClasses ?? $meta->expectedRecordedClasses,
            $specWithConfigOrSimpleSpec->expectedRecordedFunctions ?? $meta->expectedRecordedFunctions,
            $specWithConfigOrSimpleSpec->minPhpVersion ?? $meta->minPhpVersion,
            $specWithConfigOrSimpleSpec->maxPhpVersion ?? $meta->maxPhpVersion,
        ];
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
