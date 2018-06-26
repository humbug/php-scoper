<?php

/*
 * This file is part of the php-code-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node;

use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Util;
/**
 * Base class for nodes in the code coverage information tree.
 */
abstract class AbstractNode implements \Countable
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $path;
    /**
     * @var array
     */
    private $pathArray;
    /**
     * @var AbstractNode
     */
    private $parent;
    /**
     * @var string
     */
    private $id;
    public function __construct(string $name, self $parent = null)
    {
        if (\substr($name, -1) == '/') {
            $name = \substr($name, 0, -1);
        }
        $this->name = $name;
        $this->parent = $parent;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getId() : string
    {
        if ($this->id === null) {
            $parent = $this->getParent();
            if ($parent === null) {
                $this->id = 'index';
            } else {
                $parentId = $parent->getId();
                if ($parentId === 'index') {
                    $this->id = \str_replace(':', '_', $this->name);
                } else {
                    $this->id = $parentId . '/' . $this->name;
                }
            }
        }
        return $this->id;
    }
    public function getPath() : string
    {
        if ($this->path === null) {
            if ($this->parent === null || $this->parent->getPath() === null || $this->parent->getPath() === \false) {
                $this->path = $this->name;
            } else {
                $this->path = $this->parent->getPath() . '/' . $this->name;
            }
        }
        return $this->path;
    }
    public function getPathAsArray() : array
    {
        if ($this->pathArray === null) {
            if ($this->parent === null) {
                $this->pathArray = [];
            } else {
                $this->pathArray = $this->parent->getPathAsArray();
            }
            $this->pathArray[] = $this;
        }
        return $this->pathArray;
    }
    public function getParent() : ?self
    {
        return $this->parent;
    }
    /**
     * Returns the percentage of classes that has been tested.
     *
     * @return int|string
     */
    public function getTestedClassesPercent(bool $asString = \true)
    {
        return \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Util::percent($this->getNumTestedClasses(), $this->getNumClasses(), $asString);
    }
    /**
     * Returns the percentage of traits that has been tested.
     *
     * @return int|string
     */
    public function getTestedTraitsPercent(bool $asString = \true)
    {
        return \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Util::percent($this->getNumTestedTraits(), $this->getNumTraits(), $asString);
    }
    /**
     * Returns the percentage of classes and traits that has been tested.
     *
     * @return int|string
     */
    public function getTestedClassesAndTraitsPercent(bool $asString = \true)
    {
        return \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Util::percent($this->getNumTestedClassesAndTraits(), $this->getNumClassesAndTraits(), $asString);
    }
    /**
     * Returns the percentage of functions that has been tested.
     *
     * @return int|string
     */
    public function getTestedFunctionsPercent(bool $asString = \true)
    {
        return \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Util::percent($this->getNumTestedFunctions(), $this->getNumFunctions(), $asString);
    }
    /**
     * Returns the percentage of methods that has been tested.
     *
     * @return int|string
     */
    public function getTestedMethodsPercent(bool $asString = \true)
    {
        return \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Util::percent($this->getNumTestedMethods(), $this->getNumMethods(), $asString);
    }
    /**
     * Returns the percentage of functions and methods that has been tested.
     *
     * @return int|string
     */
    public function getTestedFunctionsAndMethodsPercent(bool $asString = \true)
    {
        return \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Util::percent($this->getNumTestedFunctionsAndMethods(), $this->getNumFunctionsAndMethods(), $asString);
    }
    /**
     * Returns the percentage of executed lines.
     *
     * @return int|string
     */
    public function getLineExecutedPercent(bool $asString = \true)
    {
        return \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Util::percent($this->getNumExecutedLines(), $this->getNumExecutableLines(), $asString);
    }
    /**
     * Returns the number of classes and traits.
     */
    public function getNumClassesAndTraits() : int
    {
        return $this->getNumClasses() + $this->getNumTraits();
    }
    /**
     * Returns the number of tested classes and traits.
     */
    public function getNumTestedClassesAndTraits() : int
    {
        return $this->getNumTestedClasses() + $this->getNumTestedTraits();
    }
    /**
     * Returns the classes and traits of this node.
     */
    public function getClassesAndTraits() : array
    {
        return \array_merge($this->getClasses(), $this->getTraits());
    }
    /**
     * Returns the number of functions and methods.
     */
    public function getNumFunctionsAndMethods() : int
    {
        return $this->getNumFunctions() + $this->getNumMethods();
    }
    /**
     * Returns the number of tested functions and methods.
     */
    public function getNumTestedFunctionsAndMethods() : int
    {
        return $this->getNumTestedFunctions() + $this->getNumTestedMethods();
    }
    /**
     * Returns the functions and methods of this node.
     */
    public function getFunctionsAndMethods() : array
    {
        return \array_merge($this->getFunctions(), $this->getMethods());
    }
    /**
     * Returns the classes of this node.
     */
    public abstract function getClasses() : array;
    /**
     * Returns the traits of this node.
     */
    public abstract function getTraits() : array;
    /**
     * Returns the functions of this node.
     */
    public abstract function getFunctions() : array;
    /**
     * Returns the LOC/CLOC/NCLOC of this node.
     */
    public abstract function getLinesOfCode() : array;
    /**
     * Returns the number of executable lines.
     */
    public abstract function getNumExecutableLines() : int;
    /**
     * Returns the number of executed lines.
     */
    public abstract function getNumExecutedLines() : int;
    /**
     * Returns the number of classes.
     */
    public abstract function getNumClasses() : int;
    /**
     * Returns the number of tested classes.
     */
    public abstract function getNumTestedClasses() : int;
    /**
     * Returns the number of traits.
     */
    public abstract function getNumTraits() : int;
    /**
     * Returns the number of tested traits.
     */
    public abstract function getNumTestedTraits() : int;
    /**
     * Returns the number of methods.
     */
    public abstract function getNumMethods() : int;
    /**
     * Returns the number of tested methods.
     */
    public abstract function getNumTestedMethods() : int;
    /**
     * Returns the number of functions.
     */
    public abstract function getNumFunctions() : int;
    /**
     * Returns the number of tested functions.
     */
    public abstract function getNumTestedFunctions() : int;
}
