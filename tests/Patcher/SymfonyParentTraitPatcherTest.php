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

use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\Patcher\SymfonyParentTraitPatcher
 *
 * @internal
 */
class SymfonyParentTraitPatcherTest extends TestCase
{
    /**
     * @dataProvider provideFiles
     */
    public function test_patch_the_symfony_parent_trait(string $filePath, string $contents, string $expected): void
    {
        $actual = (new SymfonyParentTraitPatcher())->__invoke($filePath, 'Humbug', $contents);

        self::assertSame($expected, $actual);
    }

    public static function provideFiles(): iterable
    {
        $validPaths = [
            'src/Symfony/Component/DependencyInjection/Loader/Configurator/Traits/ParentTrait.php',
            'symfony/dependency-injection/Loader/Configurator/Traits/ParentTrait.php',
            'vendor/symfony/symfony/src/Symfony/Component/DependencyInjection/Loader/Configurator/Traits/ParentTrait.php',
            'vendor/symfony/dependency-injection/Loader/Configurator/Traits/ParentTrait.php',
        ];

        $invalidPaths = [
            'Loader/Configurator/Traits/ParentTrait.php',
            'Configurator/Traits/ParentTrait.php',
            'Traits/ParentTrait.php',
            'ParentTrait.php',
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
        yield 'non-scoped content' => [
            <<<'PHP'
                <?php

                /*
                 * This file is part of the Symfony package.
                 *
                 * (c) Fabien Potencier <fabien@symfony.com>
                 *
                 * For the full copyright and license information, please view the LICENSE
                 * file that was distributed with this source code.
                 */

                namespace Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

                use Symfony\Component\DependencyInjection\ChildDefinition;
                use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

                trait ParentTrait
                {
                    /**
                     * Sets the Definition to inherit from.
                     *
                     * @return $this
                     *
                     * @throws InvalidArgumentException when parent cannot be set
                     */
                    final public function parent(string $parent): static
                    {
                        if (!$this->allowParent) {
                            throw new InvalidArgumentException(sprintf('A parent cannot be defined when either "_instanceof" or "_defaults" are also defined for service prototype "%s".', $this->id));
                        }

                        if ($this->definition instanceof ChildDefinition) {
                            $this->definition->setParent($parent);
                        } else {
                            // cast Definition to ChildDefinition
                            $definition = serialize($this->definition);
                            $definition = substr_replace($definition, '53', 2, 2);
                            $definition = substr_replace($definition, 'Child', 44, 0);
                            $definition = unserialize($definition);

                            $this->definition = $definition->setParent($parent);
                        }

                        return $this;
                    }
                }

                PHP,
            <<<'PHP'
                <?php

                /*
                 * This file is part of the Symfony package.
                 *
                 * (c) Fabien Potencier <fabien@symfony.com>
                 *
                 * For the full copyright and license information, please view the LICENSE
                 * file that was distributed with this source code.
                 */

                namespace Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

                use Symfony\Component\DependencyInjection\ChildDefinition;
                use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

                trait ParentTrait
                {
                    /**
                     * Sets the Definition to inherit from.
                     *
                     * @return $this
                     *
                     * @throws InvalidArgumentException when parent cannot be set
                     */
                    final public function parent(string $parent): static
                    {
                        if (!$this->allowParent) {
                            throw new InvalidArgumentException(sprintf('A parent cannot be defined when either "_instanceof" or "_defaults" are also defined for service prototype "%s".', $this->id));
                        }

                        if ($this->definition instanceof ChildDefinition) {
                            $this->definition->setParent($parent);
                        } else {
                            // cast Definition to ChildDefinition
                            $definition = serialize($this->definition);
                            $definition = substr_replace($definition, '60', 2, 2);
                            $definition = substr_replace($definition, 'Child', 51, 0);
                            $definition = unserialize($definition);

                            $this->definition = $definition->setParent($parent);
                        }

                        return $this;
                    }
                }

                PHP,
        ];

        yield 'scoped content' => [
            <<<'PHP'
                <?php

                /*
                 * This file is part of the Symfony package.
                 *
                 * (c) Fabien Potencier <fabien@symfony.com>
                 *
                 * For the full copyright and license information, please view the LICENSE
                 * file that was distributed with this source code.
                 */
                namespace Humbug\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

                use Humbug\Symfony\Component\DependencyInjection\ChildDefinition;
                use Humbug\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
                use function strlen;

                trait ParentTrait
                {
                    /**
                     * Sets the Definition to inherit from.
                     *
                     * @return $this
                     *
                     * @throws InvalidArgumentException when parent cannot be set
                     */
                    public final function parent(string $parent) : static
                    {
                        if (!$this->allowParent) {
                            throw new InvalidArgumentException(\sprintf('A parent cannot be defined when either "_instanceof" or "_defaults" are also defined for service prototype "%s".', $this->id));
                        }
                        if ($this->definition instanceof ChildDefinition) {
                            $this->definition->setParent($parent);
                        } else {
                            // cast Definition to ChildDefinition
                            $definition = \serialize($this->definition);
                            $definition = \substr_replace($definition, '53', 2, 2);
                            $definition = \substr_replace($definition, 'Child', 44, 0);
                            $definition = \unserialize($definition);
                            $this->definition = $definition->setParent($parent);
                        }
                        return $this;
                    }
                }

                PHP,
            <<<'PHP'
                <?php

                /*
                 * This file is part of the Symfony package.
                 *
                 * (c) Fabien Potencier <fabien@symfony.com>
                 *
                 * For the full copyright and license information, please view the LICENSE
                 * file that was distributed with this source code.
                 */
                namespace Humbug\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

                use Humbug\Symfony\Component\DependencyInjection\ChildDefinition;
                use Humbug\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
                use function strlen;

                trait ParentTrait
                {
                    /**
                     * Sets the Definition to inherit from.
                     *
                     * @return $this
                     *
                     * @throws InvalidArgumentException when parent cannot be set
                     */
                    public final function parent(string $parent) : static
                    {
                        if (!$this->allowParent) {
                            throw new InvalidArgumentException(\sprintf('A parent cannot be defined when either "_instanceof" or "_defaults" are also defined for service prototype "%s".', $this->id));
                        }
                        if ($this->definition instanceof ChildDefinition) {
                            $this->definition->setParent($parent);
                        } else {
                            // cast Definition to ChildDefinition
                            $definition = \serialize($this->definition);
                            $definition = \substr_replace($definition, '60', 2, 2);
                            $definition = \substr_replace($definition, 'Child', 51, 0);
                            $definition = \unserialize($definition);
                            $this->definition = $definition->setParent($parent);
                        }
                        return $this;
                    }
                }

                PHP,
        ];
    }
}
