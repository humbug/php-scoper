<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Runner\Filter;

use FilterIterator;
use InvalidArgumentException;
use Iterator;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite;
use ReflectionClass;
class Factory
{
    /**
     * @var array
     */
    private $filters = [];
    /**
     * @param ReflectionClass $filter
     * @param mixed           $args
     *
     * @throws InvalidArgumentException
     */
    public function addFilter(\ReflectionClass $filter, $args) : void
    {
        if (!$filter->isSubclassOf(\RecursiveFilterIterator::class)) {
            throw new \InvalidArgumentException(\sprintf('Class "%s" does not extend RecursiveFilterIterator', $filter->name));
        }
        $this->filters[] = [$filter, $args];
    }
    public function factory(\Iterator $iterator, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite $suite) : \FilterIterator
    {
        foreach ($this->filters as $filter) {
            [$class, $args] = $filter;
            $iterator = $class->newInstance($iterator, $args, $suite);
        }
        return $iterator;
    }
}
