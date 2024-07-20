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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ScoperAutoloadGeneratorTest extends TestCase
{
    #[DataProvider('provideRegistry')]
    public function test_generate_the_autoload(
        SymbolsRegistry $registry,
        array $fileHashes,
        string $expected,
    ): void {
        $generator = new ScoperAutoloadGenerator($registry, $fileHashes);

        $actual = $generator->dump();

        self::assertSame($expected, $actual);
    }

    public static function provideRegistry(): iterable
    {
        yield 'empty registry' => [
            new SymbolsRegistry(),
            [],
            <<<'PHP'
                <?php

                // scoper-autoload.php @generated by PhpScoper

                $loader = (static function () {
                    // Backup the autoloaded Composer files
                    $existingComposerAutoloadFiles = isset($GLOBALS['__composer_autoload_files']) ? $GLOBALS['__composer_autoload_files'] : [];

                    $loader = require_once __DIR__.'/autoload.php';
                    // Ensure InstalledVersions is available
                    $installedVersionsPath = __DIR__.'/composer/InstalledVersions.php';
                    if (file_exists($installedVersionsPath)) require_once $installedVersionsPath;

                    // Restore the backup and ensure the excluded files are properly marked as loaded
                    $GLOBALS['__composer_autoload_files'] = \array_merge(
                        $existingComposerAutoloadFiles,
                        \array_fill_keys([], true)
                    );

                    return $loader;
                })();

                return $loader;

                PHP,
        ];

        yield 'empty registry with file hashes' => [
            new SymbolsRegistry(),
            ['a610a8e036135f992c6edfb10ca9f4e9', 'e252736c6babb7c097ab6692dbcb2a5a'],
            <<<'PHP'
                <?php

                // scoper-autoload.php @generated by PhpScoper

                $loader = (static function () {
                    // Backup the autoloaded Composer files
                    $existingComposerAutoloadFiles = isset($GLOBALS['__composer_autoload_files']) ? $GLOBALS['__composer_autoload_files'] : [];

                    $loader = require_once __DIR__.'/autoload.php';
                    // Ensure InstalledVersions is available
                    $installedVersionsPath = __DIR__.'/composer/InstalledVersions.php';
                    if (file_exists($installedVersionsPath)) require_once $installedVersionsPath;

                    // Restore the backup and ensure the excluded files are properly marked as loaded
                    $GLOBALS['__composer_autoload_files'] = \array_merge(
                        $existingComposerAutoloadFiles,
                        \array_fill_keys(['a610a8e036135f992c6edfb10ca9f4e9', 'e252736c6babb7c097ab6692dbcb2a5a'], true)
                    );

                    return $loader;
                })();

                return $loader;

                PHP,
        ];

        yield 'global functions recorded' => [
            SymbolsRegistry::create(
                [
                    ['bar', 'Humbug\bar'],
                    ['foo', 'Humbug\foo'],
                ],
            ),
            [],
            <<<'PHP'
                <?php

                // scoper-autoload.php @generated by PhpScoper

                $loader = (static function () {
                    // Backup the autoloaded Composer files
                    $existingComposerAutoloadFiles = isset($GLOBALS['__composer_autoload_files']) ? $GLOBALS['__composer_autoload_files'] : [];

                    $loader = require_once __DIR__.'/autoload.php';
                    // Ensure InstalledVersions is available
                    $installedVersionsPath = __DIR__.'/composer/InstalledVersions.php';
                    if (file_exists($installedVersionsPath)) require_once $installedVersionsPath;

                    // Restore the backup and ensure the excluded files are properly marked as loaded
                    $GLOBALS['__composer_autoload_files'] = \array_merge(
                        $existingComposerAutoloadFiles,
                        \array_fill_keys([], true)
                    );

                    return $loader;
                })();

                // Function aliases. For more information see:
                // https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#function-aliases
                if (!function_exists('bar')) { function bar() { return \Humbug\bar(...func_get_args()); } }
                if (!function_exists('foo')) { function foo() { return \Humbug\foo(...func_get_args()); } }

                return $loader;

                PHP,
        ];

        yield 'global functions recorded unordered' => [
            SymbolsRegistry::create(
                [
                    ['foo', 'Humbug\foo'],
                    ['bar', 'Humbug\bar'],
                ],
            ),
            [],
            <<<'PHP'
                <?php

                // scoper-autoload.php @generated by PhpScoper

                $loader = (static function () {
                    // Backup the autoloaded Composer files
                    $existingComposerAutoloadFiles = isset($GLOBALS['__composer_autoload_files']) ? $GLOBALS['__composer_autoload_files'] : [];

                    $loader = require_once __DIR__.'/autoload.php';
                    // Ensure InstalledVersions is available
                    $installedVersionsPath = __DIR__.'/composer/InstalledVersions.php';
                    if (file_exists($installedVersionsPath)) require_once $installedVersionsPath;

                    // Restore the backup and ensure the excluded files are properly marked as loaded
                    $GLOBALS['__composer_autoload_files'] = \array_merge(
                        $existingComposerAutoloadFiles,
                        \array_fill_keys([], true)
                    );

                    return $loader;
                })();

                // Function aliases. For more information see:
                // https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#function-aliases
                if (!function_exists('bar')) { function bar() { return \Humbug\bar(...func_get_args()); } }
                if (!function_exists('foo')) { function foo() { return \Humbug\foo(...func_get_args()); } }

                return $loader;

                PHP,
        ];

        yield 'namespaced functions recorded' => [
            SymbolsRegistry::create(
                [
                    ['Acme\bar', 'Humbug\Acme\bar'],
                    ['Acme\foo', 'Humbug\Acme\foo'],
                    ['Emca\baz', 'Humbug\Emca\baz'],
                    ['Acme\Emca\foo', 'Humbug\Acme\Emca\foo'],
                ],
            ),
            [],
            <<<'PHP'
                <?php

                // scoper-autoload.php @generated by PhpScoper

                namespace {
                    $loader = (static function () {
                        // Backup the autoloaded Composer files
                        $existingComposerAutoloadFiles = isset($GLOBALS['__composer_autoload_files']) ? $GLOBALS['__composer_autoload_files'] : [];

                        $loader = require_once __DIR__.'/autoload.php';
                        // Ensure InstalledVersions is available
                        $installedVersionsPath = __DIR__.'/composer/InstalledVersions.php';
                        if (file_exists($installedVersionsPath)) require_once $installedVersionsPath;

                        // Restore the backup and ensure the excluded files are properly marked as loaded
                        $GLOBALS['__composer_autoload_files'] = \array_merge(
                            $existingComposerAutoloadFiles,
                            \array_fill_keys([], true)
                        );

                        return $loader;
                    })();
                }

                // Function aliases. For more information see:
                // https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#function-aliases
                namespace Acme\Emca {
                    if (!function_exists('Acme\Emca\foo')) { function foo() { return \Humbug\Acme\Emca\foo(...func_get_args()); } }
                }
                
                namespace Acme {
                    if (!function_exists('Acme\bar')) { function bar() { return \Humbug\Acme\bar(...func_get_args()); } }
                    if (!function_exists('Acme\foo')) { function foo() { return \Humbug\Acme\foo(...func_get_args()); } }
                }

                namespace Emca {
                    if (!function_exists('Emca\baz')) { function baz() { return \Humbug\Emca\baz(...func_get_args()); } }
                }

                namespace {
                    return $loader;
                }

                PHP,
        ];

        yield 'namespaced functions recorded with hashes' => [
            SymbolsRegistry::create(
                [
                    ['Acme\bar', 'Humbug\Acme\bar'],
                    ['Acme\foo', 'Humbug\Acme\foo'],
                    ['Emca\baz', 'Humbug\Emca\baz'],
                ],
            ),
            ['a610a8e036135f992c6edfb10ca9f4e9', 'e252736c6babb7c097ab6692dbcb2a5a'],
            <<<'PHP'
                <?php

                // scoper-autoload.php @generated by PhpScoper

                namespace {
                    $loader = (static function () {
                        // Backup the autoloaded Composer files
                        $existingComposerAutoloadFiles = isset($GLOBALS['__composer_autoload_files']) ? $GLOBALS['__composer_autoload_files'] : [];

                        $loader = require_once __DIR__.'/autoload.php';
                        // Ensure InstalledVersions is available
                        $installedVersionsPath = __DIR__.'/composer/InstalledVersions.php';
                        if (file_exists($installedVersionsPath)) require_once $installedVersionsPath;

                        // Restore the backup and ensure the excluded files are properly marked as loaded
                        $GLOBALS['__composer_autoload_files'] = \array_merge(
                            $existingComposerAutoloadFiles,
                            \array_fill_keys(['a610a8e036135f992c6edfb10ca9f4e9', 'e252736c6babb7c097ab6692dbcb2a5a'], true)
                        );

                        return $loader;
                    })();
                }

                // Function aliases. For more information see:
                // https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#function-aliases
                namespace Acme {
                    if (!function_exists('Acme\bar')) { function bar() { return \Humbug\Acme\bar(...func_get_args()); } }
                    if (!function_exists('Acme\foo')) { function foo() { return \Humbug\Acme\foo(...func_get_args()); } }
                }

                namespace Emca {
                    if (!function_exists('Emca\baz')) { function baz() { return \Humbug\Emca\baz(...func_get_args()); } }
                }

                namespace {
                    return $loader;
                }

                PHP,
        ];

        yield 'namespaced functions recorded unordered' => [
            SymbolsRegistry::create(
                [
                    ['Acme\foo', 'Humbug\Acme\foo'],
                    ['Emca\baz', 'Humbug\Emca\baz'],
                    ['Acme\bar', 'Humbug\Acme\bar'],
                ],
            ),
            [],
            <<<'PHP'
                <?php

                // scoper-autoload.php @generated by PhpScoper

                namespace {
                    $loader = (static function () {
                        // Backup the autoloaded Composer files
                        $existingComposerAutoloadFiles = isset($GLOBALS['__composer_autoload_files']) ? $GLOBALS['__composer_autoload_files'] : [];

                        $loader = require_once __DIR__.'/autoload.php';
                        // Ensure InstalledVersions is available
                        $installedVersionsPath = __DIR__.'/composer/InstalledVersions.php';
                        if (file_exists($installedVersionsPath)) require_once $installedVersionsPath;

                        // Restore the backup and ensure the excluded files are properly marked as loaded
                        $GLOBALS['__composer_autoload_files'] = \array_merge(
                            $existingComposerAutoloadFiles,
                            \array_fill_keys([], true)
                        );

                        return $loader;
                    })();
                }

                // Function aliases. For more information see:
                // https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#function-aliases
                namespace Acme {
                    if (!function_exists('Acme\bar')) { function bar() { return \Humbug\Acme\bar(...func_get_args()); } }
                    if (!function_exists('Acme\foo')) { function foo() { return \Humbug\Acme\foo(...func_get_args()); } }
                }

                namespace Emca {
                    if (!function_exists('Emca\baz')) { function baz() { return \Humbug\Emca\baz(...func_get_args()); } }
                }

                namespace {
                    return $loader;
                }

                PHP,
        ];

        yield 'classes recorded' => [
            SymbolsRegistry::create(
                classes: [
                    ['A\Foo', 'Humbug\A\Foo'],
                ],
            ),
            [],
            <<<'PHP'
                <?php

                // scoper-autoload.php @generated by PhpScoper

                $loader = (static function () {
                    // Backup the autoloaded Composer files
                    $existingComposerAutoloadFiles = isset($GLOBALS['__composer_autoload_files']) ? $GLOBALS['__composer_autoload_files'] : [];

                    $loader = require_once __DIR__.'/autoload.php';
                    // Ensure InstalledVersions is available
                    $installedVersionsPath = __DIR__.'/composer/InstalledVersions.php';
                    if (file_exists($installedVersionsPath)) require_once $installedVersionsPath;

                    // Restore the backup and ensure the excluded files are properly marked as loaded
                    $GLOBALS['__composer_autoload_files'] = \array_merge(
                        $existingComposerAutoloadFiles,
                        \array_fill_keys([], true)
                    );

                    return $loader;
                })();

                // Class aliases. For more information see:
                // https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#class-aliases
                if (!function_exists('humbug_phpscoper_expose_class')) {
                    function humbug_phpscoper_expose_class($exposed, $prefixed) {
                        if (!class_exists($exposed, false) && !interface_exists($exposed, false) && !trait_exists($exposed, false)) {
                            spl_autoload_call($prefixed);
                        }
                    }
                }
                humbug_phpscoper_expose_class('A\Foo', 'Humbug\A\Foo');

                return $loader;

                PHP,
        ];

        yield 'global classes recorded' => [
            SymbolsRegistry::create(
                classes: [
                    ['Foo', 'Humbug\Foo'],
                    ['Bar', 'Humbug\Bar'],
                ],
            ),
            [],
            <<<'PHP'
                <?php

                // scoper-autoload.php @generated by PhpScoper

                $loader = (static function () {
                    // Backup the autoloaded Composer files
                    $existingComposerAutoloadFiles = isset($GLOBALS['__composer_autoload_files']) ? $GLOBALS['__composer_autoload_files'] : [];

                    $loader = require_once __DIR__.'/autoload.php';
                    // Ensure InstalledVersions is available
                    $installedVersionsPath = __DIR__.'/composer/InstalledVersions.php';
                    if (file_exists($installedVersionsPath)) require_once $installedVersionsPath;

                    // Restore the backup and ensure the excluded files are properly marked as loaded
                    $GLOBALS['__composer_autoload_files'] = \array_merge(
                        $existingComposerAutoloadFiles,
                        \array_fill_keys([], true)
                    );

                    return $loader;
                })();

                // Class aliases. For more information see:
                // https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#class-aliases
                if (!function_exists('humbug_phpscoper_expose_class')) {
                    function humbug_phpscoper_expose_class($exposed, $prefixed) {
                        if (!class_exists($exposed, false) && !interface_exists($exposed, false) && !trait_exists($exposed, false)) {
                            spl_autoload_call($prefixed);
                        }
                    }
                }
                humbug_phpscoper_expose_class('Foo', 'Humbug\Foo');
                humbug_phpscoper_expose_class('Bar', 'Humbug\Bar');

                return $loader;

                PHP,
        ];

        yield 'nominal' => [
            SymbolsRegistry::create(
                [
                    ['bar', 'Humbug\bar'],
                    ['foo', 'Humbug\foo'],
                    ['Acme\foo', 'Humbug\Acme\foo'],
                    ['Acme\bar', 'Humbug\Acme\bar'],
                    ['Emca\baz', 'Humbug\Emca\baz'],
                ],
                [
                    ['A\Foo', 'Humbug\A\Foo'],
                ],
            ),
            [],
            <<<'PHP'
                <?php

                // scoper-autoload.php @generated by PhpScoper

                namespace {
                    $loader = (static function () {
                        // Backup the autoloaded Composer files
                        $existingComposerAutoloadFiles = isset($GLOBALS['__composer_autoload_files']) ? $GLOBALS['__composer_autoload_files'] : [];

                        $loader = require_once __DIR__.'/autoload.php';
                        // Ensure InstalledVersions is available
                        $installedVersionsPath = __DIR__.'/composer/InstalledVersions.php';
                        if (file_exists($installedVersionsPath)) require_once $installedVersionsPath;

                        // Restore the backup and ensure the excluded files are properly marked as loaded
                        $GLOBALS['__composer_autoload_files'] = \array_merge(
                            $existingComposerAutoloadFiles,
                            \array_fill_keys([], true)
                        );

                        return $loader;
                    })();
                }

                // Class aliases. For more information see:
                // https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#class-aliases
                namespace {
                    if (!function_exists('humbug_phpscoper_expose_class')) {
                        function humbug_phpscoper_expose_class($exposed, $prefixed) {
                            if (!class_exists($exposed, false) && !interface_exists($exposed, false) && !trait_exists($exposed, false)) {
                                spl_autoload_call($prefixed);
                            }
                        }
                    }
                    humbug_phpscoper_expose_class('A\Foo', 'Humbug\A\Foo');
                }

                // Function aliases. For more information see:
                // https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#function-aliases
                namespace Acme {
                    if (!function_exists('Acme\bar')) { function bar() { return \Humbug\Acme\bar(...func_get_args()); } }
                    if (!function_exists('Acme\foo')) { function foo() { return \Humbug\Acme\foo(...func_get_args()); } }
                }

                namespace Emca {
                    if (!function_exists('Emca\baz')) { function baz() { return \Humbug\Emca\baz(...func_get_args()); } }
                }

                namespace {
                    if (!function_exists('bar')) { function bar() { return \Humbug\bar(...func_get_args()); } }
                    if (!function_exists('foo')) { function foo() { return \Humbug\foo(...func_get_args()); } }
                }

                namespace {
                    return $loader;
                }

                PHP,
        ];

        // https://github.com/humbug/php-scoper/issues/267
        yield '__autoload global function with no namespaced functions' => [
            SymbolsRegistry::create(
                [
                    ['__autoload', 'Humbug\__autoload'],
                ],
            ),
            [],
            <<<'PHP'
                <?php

                // scoper-autoload.php @generated by PhpScoper

                $loader = (static function () {
                    // Backup the autoloaded Composer files
                    $existingComposerAutoloadFiles = isset($GLOBALS['__composer_autoload_files']) ? $GLOBALS['__composer_autoload_files'] : [];

                    $loader = require_once __DIR__.'/autoload.php';
                    // Ensure InstalledVersions is available
                    $installedVersionsPath = __DIR__.'/composer/InstalledVersions.php';
                    if (file_exists($installedVersionsPath)) require_once $installedVersionsPath;

                    // Restore the backup and ensure the excluded files are properly marked as loaded
                    $GLOBALS['__composer_autoload_files'] = \array_merge(
                        $existingComposerAutoloadFiles,
                        \array_fill_keys([], true)
                    );

                    return $loader;
                })();

                // Function aliases. For more information see:
                // https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#function-aliases
                if (!function_exists('__autoload')) { function __autoload($className) { return \Humbug\__autoload(...func_get_args()); } }

                return $loader;

                PHP,
        ];

        // https://github.com/humbug/php-scoper/issues/267
        yield '__autoload global function with namespaced functions' => [
            SymbolsRegistry::create(
                [
                    ['__autoload', 'Humbug\__autoload'],
                    ['Acme\foo', 'Humbug\Acme\foo'],
                ],
            ),
            [],
            <<<'PHP'
                <?php

                // scoper-autoload.php @generated by PhpScoper

                namespace {
                    $loader = (static function () {
                        // Backup the autoloaded Composer files
                        $existingComposerAutoloadFiles = isset($GLOBALS['__composer_autoload_files']) ? $GLOBALS['__composer_autoload_files'] : [];

                        $loader = require_once __DIR__.'/autoload.php';
                        // Ensure InstalledVersions is available
                        $installedVersionsPath = __DIR__.'/composer/InstalledVersions.php';
                        if (file_exists($installedVersionsPath)) require_once $installedVersionsPath;

                        // Restore the backup and ensure the excluded files are properly marked as loaded
                        $GLOBALS['__composer_autoload_files'] = \array_merge(
                            $existingComposerAutoloadFiles,
                            \array_fill_keys([], true)
                        );

                        return $loader;
                    })();
                }

                // Function aliases. For more information see:
                // https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md#function-aliases
                namespace Acme {
                    if (!function_exists('Acme\foo')) { function foo() { return \Humbug\Acme\foo(...func_get_args()); } }
                }

                namespace {
                    if (!function_exists('__autoload')) { function __autoload($className) { return \Humbug\__autoload(...func_get_args()); } }
                }

                namespace {
                    return $loader;
                }

                PHP,
        ];
    }
}
