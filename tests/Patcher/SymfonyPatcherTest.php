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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SymfonyPatcher::class)]
class SymfonyPatcherTest extends TestCase
{
    #[DataProvider('provideFiles')]
    public function test_patch_the_symfony_dependency_injection_container_php_dumper(string $filePath, string $contents, string $expected): void
    {
        $actual = (new SymfonyPatcher())->__invoke($filePath, 'Humbug', $contents);

        self::assertSame($expected, $actual);
    }

    public static function provideFiles(): iterable
    {
        $validPaths = [
            'src/Symfony/Component/DependencyInjection/Dumper/PhpDumper.php',
            'symfony/dependency-injection/Dumper/PhpDumper.php',
            'vendor/symfony/symfony/src/Symfony/Component/DependencyInjection/Dumper/PhpDumper.php',
            'vendor/symfony/dependency-injection/Dumper/PhpDumper.php',
        ];

        $invalidPaths = [
            'DependencyInjection/Dumper/PhpDumper.php',
            'dependency-injection/Dumper/PhpDumper.php',
            'Dumper/PhpDumper.php',
        ];

        foreach (self::provideCodeSamples() as [$input, $scopedOutput]) {
            foreach ($validPaths as $path) {
                yield [$path, $input, $scopedOutput];
            }

            foreach ($invalidPaths as $path) {
                yield [$path, $input, $input];
            }
        }
    }

    private static function provideCodeSamples(): iterable
    {
        yield [
            <<<'PHP'
                    private function startClass(string $class, string $baseClass, string $baseClassWithNamespace): string
                    {
                        $namespaceLine = !$this->asFiles && $this->namespace ? "\nnamespace {$this->namespace};\n" : '';

                        $code = <<<EOF
                <?php
                $namespaceLine
                use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
                use Symfony\Component\DependencyInjection\ContainerInterface;
                use Symfony\Component\DependencyInjection\Container;
                use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
                use Symfony\Component\DependencyInjection\Exception\LogicException;
                use Symfony\Component\DependencyInjection\Exception\RuntimeException;
                use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

                /*{$this->docStar}
                 * This class has been auto-generated
                 * by the Symfony Dependency Injection Component.
                 *
                 * @final since Symfony 3.3
                 */
                class $class extends $baseClass
                {
                    private \$parameters;
                    private \$targetDirs = array();

                    /*{$this->docStar}
                     * @internal but protected for BC on cache:clear
                     */
                    protected \$privates = array();

                    public function __construct()
                    {

                EOF;
                PHP,
            <<<'PHP'
                    private function startClass(string $class, string $baseClass, string $baseClassWithNamespace): string
                    {
                        $namespaceLine = !$this->asFiles && $this->namespace ? "\nnamespace {$this->namespace};\n" : '';

                        $code = <<<EOF
                <?php
                $namespaceLine
                use Humbug\Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
                use Humbug\Symfony\Component\DependencyInjection\ContainerInterface;
                use Humbug\Symfony\Component\DependencyInjection\Container;
                use Humbug\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
                use Humbug\Symfony\Component\DependencyInjection\Exception\LogicException;
                use Humbug\Symfony\Component\DependencyInjection\Exception\RuntimeException;
                use Humbug\Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

                /*{$this->docStar}
                 * This class has been auto-generated
                 * by the Symfony Dependency Injection Component.
                 *
                 * @final since Symfony 3.3
                 */
                class $class extends $baseClass
                {
                    private \$parameters;
                    private \$targetDirs = array();

                    /*{$this->docStar}
                     * @internal but protected for BC on cache:clear
                     */
                    protected \$privates = array();

                    public function __construct()
                    {

                EOF;
                PHP,
        ];

        yield [
            <<<'PHP'
                    private function startClass(string $class, string $baseClass, string $baseClassWithNamespace): string
                    {
                        $namespaceLine = !$this->asFiles && $this->namespace ? "\nnamespace {$this->namespace};\n" : '';

                        $code = <<<EOF
                <?php
                $namespaceLine
                use Symfony\\Component\\DependencyInjection\\Argument\\RewindableGenerator;
                use Symfony\\Component\\DependencyInjection\\ContainerInterface;
                use Symfony\\Component\\DependencyInjection\\Container;
                use Symfony\\Component\\DependencyInjection\\Exception\\InvalidArgumentException;
                use Symfony\\Component\\DependencyInjection\\Exception\\LogicException;
                use Symfony\\Component\\DependencyInjection\\Exception\\RuntimeException;
                use Symfony\\Component\\DependencyInjection\\ParameterBag\\FrozenParameterBag;

                /*{$this->docStar}
                 * This class has been auto-generated
                 * by the Symfony Dependency Injection Component.
                 *
                 * @final since Symfony 3.3
                 */
                class $class extends $baseClass
                {
                    private \$parameters;
                    private \$targetDirs = array();

                    /*{$this->docStar}
                     * @internal but protected for BC on cache:clear
                     */
                    protected \$privates = array();

                    public function __construct()
                    {

                EOF;
                PHP,
            <<<'PHP'
                    private function startClass(string $class, string $baseClass, string $baseClassWithNamespace): string
                    {
                        $namespaceLine = !$this->asFiles && $this->namespace ? "\nnamespace {$this->namespace};\n" : '';

                        $code = <<<EOF
                <?php
                $namespaceLine
                use Humbug\\Symfony\\Component\\DependencyInjection\\Argument\\RewindableGenerator;
                use Humbug\\Symfony\\Component\\DependencyInjection\\ContainerInterface;
                use Humbug\\Symfony\\Component\\DependencyInjection\\Container;
                use Humbug\\Symfony\\Component\\DependencyInjection\\Exception\\InvalidArgumentException;
                use Humbug\\Symfony\\Component\\DependencyInjection\\Exception\\LogicException;
                use Humbug\\Symfony\\Component\\DependencyInjection\\Exception\\RuntimeException;
                use Humbug\\Symfony\\Component\\DependencyInjection\\ParameterBag\\FrozenParameterBag;

                /*{$this->docStar}
                 * This class has been auto-generated
                 * by the Symfony Dependency Injection Component.
                 *
                 * @final since Symfony 3.3
                 */
                class $class extends $baseClass
                {
                    private \$parameters;
                    private \$targetDirs = array();

                    /*{$this->docStar}
                     * @internal but protected for BC on cache:clear
                     */
                    protected \$privates = array();

                    public function __construct()
                    {

                EOF;
                PHP,
        ];

        yield [
            <<<'PHP'
                    private function startClass(string $class, string $baseClass, string $baseClassWithNamespace): string
                    {
                        $namespaceLine = !$this->asFiles && $this->namespace ? "\nnamespace {$this->namespace};\n" : '';

                        $code = <<<EOF
                <?php
                $namespaceLine
                use Humbug\Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
                use Humbug\Symfony\Component\DependencyInjection\ContainerInterface;
                use Humbug\Symfony\Component\DependencyInjection\Container;
                use Humbug\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
                use Humbug\Symfony\Component\DependencyInjection\Exception\LogicException;
                use Humbug\Symfony\Component\DependencyInjection\Exception\RuntimeException;
                use Humbug\Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

                /*{$this->docStar}
                 * This class has been auto-generated
                 * by the Symfony Dependency Injection Component.
                 *
                 * @final since Symfony 3.3
                 */
                class $class extends $baseClass
                {
                    private \$parameters;
                    private \$targetDirs = array();

                    /*{$this->docStar}
                     * @internal but protected for BC on cache:clear
                     */
                    protected \$privates = array();

                    public function __construct()
                    {

                EOF;
                PHP,
            <<<'PHP'
                    private function startClass(string $class, string $baseClass, string $baseClassWithNamespace): string
                    {
                        $namespaceLine = !$this->asFiles && $this->namespace ? "\nnamespace {$this->namespace};\n" : '';

                        $code = <<<EOF
                <?php
                $namespaceLine
                use Humbug\Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
                use Humbug\Symfony\Component\DependencyInjection\ContainerInterface;
                use Humbug\Symfony\Component\DependencyInjection\Container;
                use Humbug\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
                use Humbug\Symfony\Component\DependencyInjection\Exception\LogicException;
                use Humbug\Symfony\Component\DependencyInjection\Exception\RuntimeException;
                use Humbug\Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

                /*{$this->docStar}
                 * This class has been auto-generated
                 * by the Symfony Dependency Injection Component.
                 *
                 * @final since Symfony 3.3
                 */
                class $class extends $baseClass
                {
                    private \$parameters;
                    private \$targetDirs = array();

                    /*{$this->docStar}
                     * @internal but protected for BC on cache:clear
                     */
                    protected \$privates = array();

                    public function __construct()
                    {

                EOF;
                PHP,
        ];
    }
}
