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

namespace Humbug\PhpScoper\Patcher;

use Closure;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class SymfonyPhpServicesPatcherTest extends TestCase
{
    private const FILE_PATH = 'path/to/config.php';

    private Closure $patcher;

    protected function setUp(): void
    {
        $this->patcher = (require __DIR__.'/../../res/create-symfony-php-services-patcher.php')([self::FILE_PATH]);
    }

    /**
     * @dataProvider symfonyConfigFileProvider
     */
    public function test_it_can_patch_a_symfony_service_file(
        string $prefix,
        string $contents,
        string $expected,
    ): void {
        $actual = ($this->patcher)(self::FILE_PATH, $prefix, $contents);

        self::assertSame($expected, $actual);
    }

    public static function symfonyConfigFileProvider(): iterable
    {
        $prefix = 'Prefix';

        yield 'load statement' => [
            $prefix,
            <<<'PHP'
                use SomeNamespace\SomeClass;

                return static function (ContainerConfigurator $containerConfigurator) {
                    $services = $containerConfigurator->services();

                    $services->load('SomeNamespace\ConsoleColorDiff\\', __DIR__ . '/../src');

                    $services->set(SomeClass::class);
                }
                PHP,
            <<<'PHP'
                use SomeNamespace\SomeClass;

                return static function (ContainerConfigurator $containerConfigurator) {
                    $services = $containerConfigurator->services();

                    $services->load('Prefix\SomeNamespace\ConsoleColorDiff\\', __DIR__ . '/../src');

                    $services->set(SomeClass::class);
                }
                PHP,
        ];

        yield 'multiline load statement' => [
            $prefix,
            <<<'PHP'
                use SomeNamespace\SomeClass;

                return static function (ContainerConfigurator $containerConfigurator) {
                    $services = $containerConfigurator->services();

                    $services->load(
                        'SomeNamespace\ConsoleColorDiff\\',
                        __DIR__ . '/../src',
                    );

                    $services->set(SomeClass::class);
                }
                PHP,
            <<<'PHP'
                use SomeNamespace\SomeClass;

                return static function (ContainerConfigurator $containerConfigurator) {
                    $services = $containerConfigurator->services();

                    $services->load(
                        'Prefix\SomeNamespace\ConsoleColorDiff\\',
                        __DIR__ . '/../src',
                    );

                    $services->set(SomeClass::class);
                }
                PHP,
        ];
    }
}
