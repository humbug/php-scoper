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

use Humbug\PhpScoper\NotInstantiable;
use Humbug\PhpScoper\Symbol\NamespaceRegistry;
use Humbug\PhpScoper\Symbol\SymbolRegistry;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PHPUnit\Framework\TestCase;
use function array_map;
use function count;
use function current;
use function implode;
use function min;
use function sprintf;
use function str_repeat;
use function strlen;
use const PHP_EOL;

/**
 * @internal
 */
final class SpecPrinter extends TestCase
{
    use NotInstantiable;

    public static function createSpecMessage(
        SpecScenario $scenario,
        SymbolsRegistry $symbolsRegistry,
        ?string $actualCode,
    ): string {
        $file = $scenario->file;
        $title = $scenario->title;
        $inputCode = $scenario->inputCode;
        $symbolsConfiguration = $scenario->symbolsConfiguration;
        $expectedCode = $scenario->expectedCode;
        $expectedRegisteredClasses = $scenario->expectedRegisteredClasses;
        $expectedRegisteredFunctions = $scenario->expectedRegisteredFunctions;

        $formattedExposeGlobalClasses = self::convertBoolToString($symbolsConfiguration->shouldExposeGlobalClasses());
        $formattedExposeGlobalConstants = self::convertBoolToString($symbolsConfiguration->shouldExposeGlobalConstants());
        $formattedExposeGlobalFunctions = self::convertBoolToString($symbolsConfiguration->shouldExposeGlobalFunctions());

        $formattedNamespacesToExclude = self::formatNamespaceRegistry($symbolsConfiguration->getExcludedNamespaces());
        $formattedNamespacesToExpose = self::formatNamespaceRegistry($symbolsConfiguration->getExposedNamespaces());

        $formattedClassesToExpose = self::formatSymbolRegistry($symbolsConfiguration->getExposedClasses());
        $formattedFunctionsToExpose = self::formatSymbolRegistry($symbolsConfiguration->getExposedFunctions());
        $formattedConstantsToExpose = self::formatSymbolRegistry($symbolsConfiguration->getExposedConstants());

        $formattedInternalClasses = self::formatSymbolRegistry($symbolsConfiguration->getExcludedClasses());
        $formattedInternalFunctions = self::formatSymbolRegistry($symbolsConfiguration->getExcludedFunctions());
        $formattedInternalConstants = self::formatSymbolRegistry($symbolsConfiguration->getExcludedConstants());

        $formattedExpectedRegisteredClasses = self::formatTupleList($expectedRegisteredClasses);
        $formattedExpectedRegisteredFunctions = self::formatTupleList($expectedRegisteredFunctions);

        $formattedActualRegisteredClasses = self::formatTupleList($symbolsRegistry->getRecordedClasses());
        $formattedActualRegisteredFunctions = self::formatTupleList($symbolsRegistry->getRecordedFunctions());

        $titleSeparator = str_repeat(
            '=',
            min(
                strlen($title),
                80,
            ),
        );

        return <<<OUTPUT
            {$titleSeparator}
            SPECIFICATION
            {$titleSeparator}
            {$title}
            {$file}

            {$titleSeparator}
            INPUT
            expose global classes: {$formattedExposeGlobalClasses}
            expose global functions: {$formattedExposeGlobalFunctions}
            expose global constants: {$formattedExposeGlobalConstants}

            exclude namespaces: {$formattedNamespacesToExclude}
            expose namespaces: {$formattedNamespacesToExpose}

            expose classes: {$formattedClassesToExpose}
            expose functions: {$formattedFunctionsToExpose}
            expose constants: {$formattedConstantsToExpose}

            (raw) internal classes: {$formattedInternalClasses}
            (raw) internal functions: {$formattedInternalFunctions}
            (raw) internal constants: {$formattedInternalConstants}
            {$titleSeparator}
            {$inputCode}

            {$titleSeparator}
            EXPECTED
            {$titleSeparator}
            {$expectedCode}
            ----------------
            recorded functions: {$formattedExpectedRegisteredFunctions}
            recorded classes: {$formattedExpectedRegisteredClasses}

            {$titleSeparator}
            ACTUAL
            {$titleSeparator}
            {$actualCode}
            ----------------
            recorded functions: {$formattedActualRegisteredFunctions}
            recorded classes: {$formattedActualRegisteredClasses}

            -------------------------------------------------------------------------------
            OUTPUT;
    }

    /**
     * @param string[] $strings
     */
    private static function formatSimpleList(array $strings): string
    {
        if (0 === count($strings)) {
            return '[]';
        }

        if (1 === count($strings)) {
            return '[ '.current($strings).' ]';
        }

        return sprintf(
            "[\n%s\n]",
            implode(
                PHP_EOL,
                array_map(
                    static fn (string $string): string => '  - '.$string,
                    $strings,
                ),
            ),
        );
    }

    /**
     * @param string[][] $stringTuples
     */
    private static function formatTupleList(array $stringTuples): string
    {
        if (0 === count($stringTuples)) {
            return '[]';
        }

        if (1 === count($stringTuples)) {
            /** @var string[] $tuple */
            $tuple = current($stringTuples);

            return sprintf('[%s => %s]', ...$tuple);
        }

        return sprintf(
            "[\n%s\n]",
            implode(
                PHP_EOL,
                array_map(
                    static fn (array $stringTuple): string => sprintf('  - %s => %s', ...$stringTuple),
                    $stringTuples,
                ),
            ),
        );
    }

    private static function convertBoolToString(bool $bool): string
    {
        return true === $bool ? 'true' : 'false';
    }

    private static function formatNamespaceRegistry(NamespaceRegistry $namespaceRegistry): string
    {
        return self::formatSimpleList([
            ...$namespaceRegistry->getNames(),
            ...$namespaceRegistry->getRegexes(),
        ]);
    }

    private static function formatSymbolRegistry(SymbolRegistry $symbolRegistry): string
    {
        return self::formatSimpleList([
            ...$symbolRegistry->getNames(),
            ...$symbolRegistry->getRegexes(),
        ]);
    }
}
