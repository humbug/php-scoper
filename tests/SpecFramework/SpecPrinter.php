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
        $expectedRecordedClasses = $scenario->expectedRecordedClasses;
        $expectedRecordedFunctionsDeclarations = $scenario->expectedRecordedFunctionsDeclarations;
        $expectedRecordedAmbiguousFunctionCalls = $scenario->expectedRecordedAmbiguousFunctions;

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

        $formattedExpectedRecordedClasses = self::formatTupleList($expectedRecordedClasses);
        $formattedExpectedRecordedFunctionDeclarations = self::formatTupleList($expectedRecordedFunctionsDeclarations);
        $formattedExpectedRecordedAmbiguousFunctionCalls = self::formatList($expectedRecordedAmbiguousFunctionCalls);

        $formattedActualRecordedClasses = self::formatTupleList($symbolsRegistry->getRecordedClasses());
        $formattedActualRecordedFunctionDeclarations = self::formatTupleList($symbolsRegistry->getRecordedFunctionDeclarations());
        $formattedActualRecordedAmbiguousFunctionCalls = self::formatList($symbolsRegistry->getRecordedAmbiguousFunctionCalls());

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
            recorded function declarations: {$formattedExpectedRecordedFunctionDeclarations}
            recorded ambiguous function calls: {$formattedExpectedRecordedAmbiguousFunctionCalls}
            recorded classes: {$formattedExpectedRecordedClasses}

            {$titleSeparator}
            ACTUAL
            {$titleSeparator}
            {$actualCode}
            ----------------
            recorded function declarations: {$formattedActualRecordedFunctionDeclarations}
            recorded ambiguous function calls: {$formattedActualRecordedAmbiguousFunctionCalls}
            recorded classes: {$formattedActualRecordedClasses}

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
     * @param array<array{string, string}> $stringTuples
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

    /**
     * @param string[] $strings
     */
    private static function formatList(array $strings): string
    {
        if (0 === count($strings)) {
            return '[]';
        }

        if (1 === count($strings)) {
            /** @var string $string */
            $string = current($strings);

            return sprintf('[%s]', $string);
        }

        return sprintf(
            "[\n%s\n]",
            implode(
                PHP_EOL,
                array_map(
                    static fn (string $string): string => sprintf('  - %s', $string),
                    $strings,
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
