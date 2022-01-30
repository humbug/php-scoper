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

namespace Humbug\PhpScoper\Autoload;

use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PhpParser\Node\Name\FullyQualified;
use function array_map;
use function array_unshift;
use function chr;
use function count;
use function explode;
use function implode;
use function Safe\sprintf;
use function str_repeat;
use function str_replace;
use function strpos;

final class ScoperAutoloadGenerator
{
    // TODO: aliasing functions could be done via a single function to reduce boiler-template.

    // TODO: doc link
    private const EXPOSED_FUNCTIONS_DOC = <<<'EOF'
    // Exposed functions. For more information see:
    // https://github.com/humbug/php-scoper/blob/master/README.md#functions-whitelisting
    EOF;

    // TODO: doc link
    private const EXPOSED_CLASSES_DOC = <<<'EOF'
    // Exposed classes. For more information see:
    // https://github.com/humbug/php-scoper/blob/master/README.md#class-whitelisting
    EOF;

    private static string $eol;
    
    private SymbolsRegistry $registry;

    public function __construct(SymbolsRegistry $registry)
    {
        self::$eol = chr(10);

        $this->registry = $registry;
    }

    public function dump(): string
    {
        $exposedFunctions = $this->registry->getRecordedFunctions();

        $hasNamespacedFunctions = self::hasNamespacedFunctions($exposedFunctions);

        $statements = implode(
                self::$eol,
                self::createClassAliasStatementsSection(
                    $this->registry->getRecordedClasses(),
                    $hasNamespacedFunctions,
                ),
            )
            .self::$eol
            .self::$eol
        ;
        $statements .= implode(
            self::$eol,
            self::createFunctionAliasStatements(
                $exposedFunctions,
                $hasNamespacedFunctions,
            ),
        );

        if ($hasNamespacedFunctions) {
            $dump = <<<PHP
            <?php
            
            // scoper-autoload.php @generated by PhpScoper
            
            namespace {
                \$loader = require_once __DIR__.'/autoload.php';
            }
            
            $statements
            
            namespace {
                return \$loader;
            }
            
            PHP;
        } else {
            $dump = <<<PHP
            <?php
            
            // scoper-autoload.php @generated by PhpScoper
            
            \$loader = require_once __DIR__.'/autoload.php';
            
            $statements
            
            return \$loader;
            
            PHP;
        }

        return self::removeUnnecessaryLineReturns($dump);
    }

    /**
     * @param list<array{string, string}> $exposedClasses
     *
     * @return list<string>
     */
    private static function createClassAliasStatementsSection(
        array $exposedClasses,
        bool $hasNamespacedFunctions
    ): array
    {
        $statements = self::createClassAliasStatements($exposedClasses);

        if (count($statements) === 0) {
            return $statements;
        }

        if ($hasNamespacedFunctions) {
            $statements = self::wrapStatementsInNamespaceBlock($statements);
        }

        array_unshift($statements, self::EXPOSED_CLASSES_DOC);

        return $statements;
    }

    /**
     * @param list<array{string, string}> $exposedClasses
     *
     * @return list<string>
     */
    private static function createClassAliasStatements(array $exposedClasses): array
    {
        return array_map(
            static fn (array $pair) => self::createClassAliasStatement(...$pair),
            $exposedClasses
        );
    }

    private static function createClassAliasStatement(
        string $original,
        string $alias
    ): string
    {
        return sprintf(
            <<<'PHP'
            if (!class_exists('%1$s', false) && !interface_exists('%1$s', false) && !trait_exists('%1$s', false)) {
                spl_autoload_call('%2$s');
            }
            PHP,
            $original,
            $alias
        );
    }

    /**
     * @param list<string> $statements
     *
     * @return list<string>
     */
    private static function wrapStatementsInNamespaceBlock(array $statements): array
    {
        $indent = str_repeat(' ', 4);
        $indentLine = static fn (string $line) => $indent.$line;

        $statements = array_map(
            static function (string $statement) use ($indentLine): string {
                $parts = explode(self::$eol, $statement);

                if (false === $parts) {
                    return $statement;
                }

                return implode(
                    self::$eol,
                    array_map($indentLine, $parts),
                );
            },
            $statements,
        );

        array_unshift($statements, 'namespace {');
        $statements[] = '}'.self::$eol;

        return $statements;
    }

    /**
     * @param list<array{string, string}> $exposedFunctions
     *
     * @return list<string>
     */
    private static function createFunctionAliasStatements(
        array $exposedFunctions,
        bool $hasNamespacedFunctions
    ): array
    {
        $statements = array_map(
            static fn (array $pair) => self::createFunctionAliasStatement(
                $hasNamespacedFunctions,
                ...$pair
            ),
            $exposedFunctions,
        );

        if ([] === $statements) {
            return $statements;
        }

        array_unshift($statements, self::EXPOSED_FUNCTIONS_DOC);

        return $statements;
    }

    private static function createFunctionAliasStatement(
        bool $hasNamespacedFunctions,
        string $original,
        string $alias
    ): string
    {
        if (!$hasNamespacedFunctions) {
            return sprintf(
                <<<'PHP'
                if (!function_exists('%1$s')) {
                    function %1$s(%2$s) {
                        return \%3$s(...func_get_args());
                    }
                }
                PHP,
                $original,
                '__autoload' === $original ? '$className' : '',
                $alias,
            );
        }

        // When the function is namespaced we need to wrap the function
        // declaration within its namespace
        // TODO: consider grouping the declarations within the same namespace
        //  i.e. that if there is Acme\foo and Acme\bar that only one
        //  namespace Acme statement is used

        $originalFQ = new FullyQualified($original);
        $namespace = $originalFQ->slice(0, -1);
        $functionName = null === $namespace ? $original : (string) $originalFQ->slice(1);

        return sprintf(
            <<<'PHP'
            namespace %s{
                if (!function_exists('%s')) {
                    function %s(%s) {
                        return \%s(...func_get_args());
                    }
                }
            }
            PHP,
            null === $namespace ? '' : $namespace->toString().' ',
            $original,
            $functionName,
            '__autoload' === $functionName ? '$className' : '',
            $alias,
        );
    }

    /**
     * @param list<array{string, string}> $functions
     */
    private static function hasNamespacedFunctions(array $functions): bool
    {
        foreach ($functions as [$original, $alias]) {
            $containsBackslash = false !== strpos($original, '\\');

            if ($containsBackslash) {
                return true;
            }
        }

        return false;
    }

    private static function removeUnnecessaryLineReturns(string $dump): string
    {
        $cleanedDump = $dump;

        do {
            $dump = $cleanedDump;
            $cleanedDump = str_replace("\n\n\n", "\n\n", $dump);
        } while ($cleanedDump !== $dump);

        return $dump;
    }
}
