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

use function array_map;
use function array_unshift;
use function chr;
use function explode;
use Humbug\PhpScoper\Whitelist;
use function implode;
use PhpParser\Node\Name\FullyQualified;
use function sprintf;
use function str_repeat;
use function str_replace;
use function strpos;

final class ScoperAutoloadGenerator
{
    private $whitelist;
    private $eol;

    public function __construct(Whitelist $whitelist)
    {
        $this->whitelist = $whitelist;
        $this->eol = chr(10);
    }

    public function dump(): string
    {
        $whitelistedFunctions = $this->whitelist->getRecordedWhitelistedFunctions();

        $hasNamespacedFunctions = $this->hasNamespacedFunctions($whitelistedFunctions);

        $statements = implode(
            $this->eol,
            $this->createClassAliasStatements(
                $this->whitelist->getRecordedWhitelistedClasses(),
                $hasNamespacedFunctions
            )
        )
            .$this->eol
            .$this->eol
        ;
        $statements .= implode(
            $this->eol,
            $this->createFunctionAliasStatements(
                $whitelistedFunctions,
                $hasNamespacedFunctions
            )
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

        $dump = $this->cleanAutoload($dump);

        return $dump;
    }

    /**
     * @return string[]
     */
    private function createClassAliasStatements(array $whitelistedClasses, bool $hasNamespacedFunctions): array
    {
        $statements = array_map(
            static function (array $pair): string {
                /**
                 * @var string
                 * @var string $prefixedClass
                 */
                [$originalClass, $prefixedClass] = $pair;

                return sprintf(
                    <<<'PHP'
if (!class_exists('%s', false)) {
    class_exists('%s');
}
PHP
                    ,
                    $originalClass,
                    $prefixedClass
                );
            },
            $whitelistedClasses
        );

        if ([] === $statements) {
            return $statements;
        }

        if ($hasNamespacedFunctions) {
            $eol = $this->eol;

            $statements = array_map(
                static function (string $statement) use ($eol): string {
                    return implode(
                        $eol,
                        array_map(
                            static function (string $statement): string {
                                return str_repeat(' ', 4).$statement;
                            },
                            explode($eol, $statement)
                        )
                    );
                },
                $statements
            );

            array_unshift($statements, 'namespace {');
            $statements[] = '}'.$this->eol;
        }

        array_unshift(
            $statements,
            <<<'EOF'
// Aliases for the whitelisted classes. For more information see:
// https://github.com/humbug/php-scoper/blob/master/README.md#class-whitelisting
EOF
        );

        return $statements;
    }

    /**
     * @return string[]
     */
    private function createFunctionAliasStatements(array $whitelistedFunctions, bool $hasNamespacedFunctions): array
    {
        $statements = array_map(
            static function (array $node) use ($hasNamespacedFunctions): string {
                $original = new FullyQualified($node[0]);
                $alias = new FullyQualified($node[1]);

                if ($hasNamespacedFunctions) {
                    $namespace = $original->slice(0, -1);
                    $functionName = null === $namespace ? $original->toString() : (string) $original->slice(1);

                    return sprintf(
                        <<<'PHP'
namespace %s{
    if (!function_exists('%s')) {
        function %s(%s) {
            return \%s(...func_get_args());
        }
    }
}
PHP
                        ,
                        null === $namespace ? '' : $namespace->toString().' ',
                        $original->toString(),
                        $functionName,
                        '__autoload' === $functionName ? '$className' : '',
                        $alias->toString()
                    );
                }

                return sprintf(
                    <<<'PHP'
if (!function_exists('%1$s')) {
    function %1$s(%2$s) {
        return \%3$s(...func_get_args());
    }
}
PHP
                    ,
                    $original,
                    '__autoload' === (string) $original ? '$className' : '',
                    $alias
                );
            },
            $whitelistedFunctions
        );

        if ([] === $statements) {
            return $statements;
        }

        array_unshift(
            $statements,
            <<<'EOF'
// Functions whitelisting. For more information see:
// https://github.com/humbug/php-scoper/blob/master/README.md#functions-whitelisting
EOF
        );

        return $statements;
    }

    private function hasNamespacedFunctions(array $functions): bool
    {
        foreach ($functions as [$original, $alias]) {
            /*
             * @var string
             * @var string $alias
             */
            if (false !== strpos($original, '\\')) {
                return true;
            }
        }

        return false;
    }

    private function cleanAutoload(string $dump): string
    {
        $cleanedDump = $dump;

        do {
            $dump = $cleanedDump;
            $cleanedDump = str_replace("\n\n\n", "\n\n", $dump);
        } while ($cleanedDump !== $dump);

        return $dump;
    }
}
