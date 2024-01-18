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
#[CoversClass(ComposerPatcher::class)]
class ComposerPatcherTest extends TestCase
{
    #[DataProvider('provideFiles')]
    public function test_patch_the_symfony_dependency_injection_container_php_dumper(string $filePath, string $contents, string $expected): void
    {
        $actual = (new ComposerPatcher())->__invoke($filePath, 'Humbug', $contents);

        self::assertSame($expected, $actual);
    }

    public static function provideFiles(): iterable
    {
        $validPaths = [
            'src/Composer/Package/Loader/ArrayLoader.php',
            'composer/composer/src/Composer/Package/Loader/ArrayLoader.php',
            'vendor/acme/foo/src/Composer/Package/Loader/ArrayLoader.php',
            'vendor/composer/composer/src/Composer/Package/Loader/ArrayLoader.php',

            'src/Composer/Package/Loader/RootPackageLoader.php',
            'composer/composer/src/Composer/Package/Loader/RootPackageLoader.php',
            'vendor/acme/foo/src/Composer/Package/Loader/RootPackageLoader.php',
            'vendor/composer/composer/src/Composer/Package/Loader/RootPackageLoader.php',
        ];

        $invalidPaths = [
            'ComposerPackage/Loader/ArrayLoader.php',
            'Loader/ArrayLoader.php',
            'ArrayLoader.php',

            'ComposerPackage/Loader/RootPackageLoader.php',
            'Loader/RootPackageLoader.php',
            'RootPackageLoader.php',
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
        yield 'nominal' => [
            <<<'PHP'
                function load(
                    $rootPackageClassName = 'Composer\Package\RootPackage',
                    $escapedRootPackageClassName = 'Composer\\Package\\RootPackage',
                    $rootPackageSubClassName = 'Composer\Package\RootPackage\Foo',
                    $rootPackageMessage = 'the class Composer\Package\RootPackage mentioned somewhere',
                    $completePackageClassName = 'Composer\Package\CompletePackage',
                    $escapedCompletePackageClassName = 'Composer\\Package\\CompletePackage',
                    $completePackageSubClassName = 'Composer\Package\CompletePackage\Foo',
                    $completePackageMessage = 'the class Composer\Package\CompletePackage mentioned somewhere',
                )
                PHP,
            <<<'PHP'
                function load(
                    $rootPackageClassName = 'Humbug\Composer\Package\RootPackage',
                    $escapedRootPackageClassName = 'Humbug\\Composer\\Package\\RootPackage',
                    $rootPackageSubClassName = 'Composer\Package\RootPackage\Foo',
                    $rootPackageMessage = 'the class Humbug\Composer\Package\RootPackage mentioned somewhere',
                    $completePackageClassName = 'Humbug\Composer\Package\CompletePackage',
                    $escapedCompletePackageClassName = 'Humbug\\Composer\\Package\\CompletePackage',
                    $completePackageSubClassName = 'Composer\Package\CompletePackage\Foo',
                    $completePackageMessage = 'the class Humbug\Composer\Package\CompletePackage mentioned somewhere',
                )
                PHP,
        ];

        yield 'Composer code excerpt' => [
            <<<'PHP'
                <?php

                namespace Composer\Package\Loader;

                // ...

                /**
                 * @author Konstantin Kudryashiv <ever.zet@gmail.com>
                 * @author Jordi Boggiano <j.boggiano@seld.be>
                 */
                class ArrayLoader implements LoaderInterface
                {
                    // ...

                    /**
                     * @inheritDoc
                     */
                    public function load(array $config, $class = 'Composer\Package\CompletePackage')
                    {
                        if ($class !== 'Composer\Package\CompletePackage' && $class !== 'Composer\Package\RootPackage') {
                            trigger_error('The $class arg is deprecated, please reach out to Composer maintainers ASAP if you still need this.', E_USER_DEPRECATED);
                        }

                        // ...
                    }

                    /**
                     * @param list<array<mixed>> $versions
                     *
                     * @return list<CompletePackage|CompleteAliasPackage>
                     */
                    public function loadPackages(array $versions)
                    {
                        $packages = array();
                        $linkCache = array();

                        foreach ($versions as $version) {
                            $package = $this->createObject($version, 'Composer\Package\CompletePackage');

                            $this->configureCachedLinks($linkCache, $package, $version);
                            $package = $this->configureObject($package, $version);

                            $packages[] = $package;
                        }

                        return $packages;
                    }

                    /**
                     * @param CompletePackage $package
                     * @param mixed[]         $config package data
                     *
                     * @return RootPackage|RootAliasPackage|CompletePackage|CompleteAliasPackage
                     */
                    private function configureObject(PackageInterface $package, array $config)
                    {
                        if (!$package instanceof CompletePackage) {
                            throw new \LogicException('ArrayLoader expects instances of the Composer\Package\CompletePackage class to function correctly');
                        }

                        //...
                    }

                    /**
                     * @param array<string, array<string, array<string, array<string, array{string, Link}>>>> $linkCache
                     * @param PackageInterface                                                                $package
                     * @param mixed[]                                                                         $config
                     *
                     * @return void
                     */
                    private function configureCachedLinks(&$linkCache, $package, array $config)
                    {
                        $name = $package->getName();
                        $prettyVersion = $package->getPrettyVersion();

                        foreach (BasePackage::$supportedLinkTypes as $type => $opts) {
                            if (isset($config[$type])) {
                                $method = 'set'.ucfirst($opts['method']);

                                $links = array();
                                foreach ($config[$type] as $prettyTarget => $constraint) {
                                    $target = strtolower($prettyTarget);

                                    // recursive links are not supported
                                    if ($target === $name) {
                                        continue;
                                    }

                                    if ($constraint === 'self.version') {
                                        $links[$target] = $this->createLink($name, $prettyVersion, $opts['method'], $target, $constraint);
                                    } else {
                                        if (!isset($linkCache[$name][$type][$target][$constraint])) {
                                            $linkCache[$name][$type][$target][$constraint] = array($target, $this->createLink($name, $prettyVersion, $opts['method'], $target, $constraint));
                                        }

                                        list($target, $link) = $linkCache[$name][$type][$target][$constraint];
                                        $links[$target] = $link;
                                    }
                                }

                                $package->{$method}($links);
                            }
                        }
                    }

                    /**
                     * @param  string                $source        source package name
                     * @param  string                $sourceVersion source package version (pretty version ideally)
                     * @param  string                $description   link description (e.g. requires, replaces, ..)
                     * @param  array<string, string> $links         array of package name => constraint mappings
                     *
                     * @return Link[]
                     *
                     * @phpstan-param Link::TYPE_* $description
                     */
                    public function parseLinks($source, $sourceVersion, $description, $links)
                    {
                        $res = array();
                        foreach ($links as $target => $constraint) {
                            $target = strtolower($target);
                            $res[$target] = $this->createLink($source, $sourceVersion, $description, $target, $constraint);
                        }

                        return $res;
                    }
                }
                PHP,
            <<<'PHP'
                <?php

                namespace Composer\Package\Loader;

                // ...

                /**
                 * @author Konstantin Kudryashiv <ever.zet@gmail.com>
                 * @author Jordi Boggiano <j.boggiano@seld.be>
                 */
                class ArrayLoader implements LoaderInterface
                {
                    // ...

                    /**
                     * @inheritDoc
                     */
                    public function load(array $config, $class = 'Humbug\Composer\Package\CompletePackage')
                    {
                        if ($class !== 'Humbug\Composer\Package\CompletePackage' && $class !== 'Humbug\Composer\Package\RootPackage') {
                            trigger_error('The $class arg is deprecated, please reach out to Composer maintainers ASAP if you still need this.', E_USER_DEPRECATED);
                        }

                        // ...
                    }

                    /**
                     * @param list<array<mixed>> $versions
                     *
                     * @return list<CompletePackage|CompleteAliasPackage>
                     */
                    public function loadPackages(array $versions)
                    {
                        $packages = array();
                        $linkCache = array();

                        foreach ($versions as $version) {
                            $package = $this->createObject($version, 'Humbug\Composer\Package\CompletePackage');

                            $this->configureCachedLinks($linkCache, $package, $version);
                            $package = $this->configureObject($package, $version);

                            $packages[] = $package;
                        }

                        return $packages;
                    }

                    /**
                     * @param CompletePackage $package
                     * @param mixed[]         $config package data
                     *
                     * @return RootPackage|RootAliasPackage|CompletePackage|CompleteAliasPackage
                     */
                    private function configureObject(PackageInterface $package, array $config)
                    {
                        if (!$package instanceof CompletePackage) {
                            throw new \LogicException('ArrayLoader expects instances of the Humbug\Composer\Package\CompletePackage class to function correctly');
                        }

                        //...
                    }

                    /**
                     * @param array<string, array<string, array<string, array<string, array{string, Link}>>>> $linkCache
                     * @param PackageInterface                                                                $package
                     * @param mixed[]                                                                         $config
                     *
                     * @return void
                     */
                    private function configureCachedLinks(&$linkCache, $package, array $config)
                    {
                        $name = $package->getName();
                        $prettyVersion = $package->getPrettyVersion();

                        foreach (BasePackage::$supportedLinkTypes as $type => $opts) {
                            if (isset($config[$type])) {
                                $method = 'set'.ucfirst($opts['method']);

                                $links = array();
                                foreach ($config[$type] as $prettyTarget => $constraint) {
                                    $target = strtolower($prettyTarget);

                                    // recursive links are not supported
                                    if ($target === $name) {
                                        continue;
                                    }

                                    if ($constraint === 'self.version') {
                                        $links[$target] = $this->createLink($name, $prettyVersion, $opts['method'], $target, $constraint);
                                    } else {
                                        if (!isset($linkCache[$name][$type][$target][$constraint])) {
                                            $linkCache[$name][$type][$target][$constraint] = array($target, $this->createLink($name, $prettyVersion, $opts['method'], $target, $constraint));
                                        }

                                        list($target, $link) = $linkCache[$name][$type][$target][$constraint];
                                        $links[$target] = $link;
                                    }
                                }

                                $package->{$method}($links);
                            }
                        }
                    }

                    /**
                     * @param  string                $source        source package name
                     * @param  string                $sourceVersion source package version (pretty version ideally)
                     * @param  string                $description   link description (e.g. requires, replaces, ..)
                     * @param  array<string, string> $links         array of package name => constraint mappings
                     *
                     * @return Link[]
                     *
                     * @phpstan-param Link::TYPE_* $description
                     */
                    public function parseLinks($source, $sourceVersion, $description, $links)
                    {
                        $res = array();
                        foreach ($links as $target => $constraint) {
                            $target = strtolower($target);
                            $res[$target] = $this->createLink($source, $sourceVersion, $description, $target, $constraint);
                        }

                        return $res;
                    }
                }
                PHP,
        ];
    }
}
