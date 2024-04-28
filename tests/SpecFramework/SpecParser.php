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
use function explode;
use function is_int;
use function is_string;
use function sprintf;
use function Safe\preg_match;
use function substr;
use function substr_count;
use const PREG_OFFSET_CAPTURE;

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
                    $file->getContents(),
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
        string $fileContents,
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

        $lineNumber = self::findLineNumber($fileContents, $title);
        if (null !== $lineNumber) {
            $file .= ':'.$lineNumber;
        }

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
            $specWithConfigOrSimpleSpec->expectedRecordedFunctionDeclarations ?? $meta->expectedRecordedFunctionDeclarations,
            $specWithConfigOrSimpleSpec->expectedRecordedAmbiguousFunctionCalls ?? $meta->expectedRecordedAmbiguousFunctionCalls,
        );
    }

    /**
     * @return positive-int|0
     */
    private static function findLineNumber(string $fileContents, int|string $title): ?int
    {
        if (is_int($title)) {
            return null;
        }

        $titleRegex = sprintf(
            '/ *\'%s\' => (?:SpecWithConfig|<<<\'PHP\')/',
            $title,
        );

        if (1 !== preg_match($titleRegex, $fileContents, $matches, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        $titlePosition = $matches[0][1];

        return substr_count(substr($fileContents, 0, $titlePosition), "\n") + 1;
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
