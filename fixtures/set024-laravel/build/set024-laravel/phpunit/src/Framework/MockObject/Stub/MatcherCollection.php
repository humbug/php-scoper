<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\Invocation;
/**
 * Stubs a method by returning a user-defined value.
 */
interface MatcherCollection
{
    /**
     * Adds a new matcher to the collection which can be used as an expectation
     * or a stub.
     *
     * @param Invocation $matcher Matcher for invocations to mock objects
     */
    public function addMatcher(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\Invocation $matcher);
}
