<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExpectationFailedException;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\AnyInvokedCount;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\AnyParameters;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\Invocation as MatcherInvocation;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedCount;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\MethodName;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\Parameters;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestFailure;
/**
 * Main matcher which defines a full expectation using method, parameter and
 * invocation matchers.
 * This matcher encapsulates all the other matchers and allows the builder to
 * set the specific matchers when the appropriate methods are called (once(),
 * where() etc.).
 *
 * All properties are public so that they can easily be accessed by the builder.
 */
class Matcher implements \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\Invocation
{
    /**
     * @var MatcherInvocation
     */
    private $invocationMatcher;
    /**
     * @var mixed
     */
    private $afterMatchBuilderId;
    /**
     * @var bool
     */
    private $afterMatchBuilderIsInvoked = \false;
    /**
     * @var MethodName
     */
    private $methodNameMatcher;
    /**
     * @var Parameters
     */
    private $parametersMatcher;
    /**
     * @var Stub
     */
    private $stub;
    /**
     * @param MatcherInvocation $invocationMatcher
     */
    public function __construct(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\Invocation $invocationMatcher)
    {
        $this->invocationMatcher = $invocationMatcher;
    }
    public function hasMatchers() : bool
    {
        return $this->invocationMatcher !== null && !$this->invocationMatcher instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\AnyInvokedCount;
    }
    public function hasMethodNameMatcher() : bool
    {
        return $this->methodNameMatcher !== null;
    }
    public function getMethodNameMatcher() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\MethodName
    {
        return $this->methodNameMatcher;
    }
    public function setMethodNameMatcher(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\MethodName $matcher) : void
    {
        $this->methodNameMatcher = $matcher;
    }
    public function hasParametersMatcher() : bool
    {
        return $this->parametersMatcher !== null;
    }
    public function getParametersMatcher() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\Parameters
    {
        return $this->parametersMatcher;
    }
    public function setParametersMatcher($matcher) : void
    {
        $this->parametersMatcher = $matcher;
    }
    public function setStub($stub) : void
    {
        $this->stub = $stub;
    }
    public function setAfterMatchBuilderId($id) : void
    {
        $this->afterMatchBuilderId = $id;
    }
    /**
     * @param Invocation $invocation
     *
     * @throws \Exception
     * @throws RuntimeException
     * @throws ExpectationFailedException
     *
     * @return mixed
     */
    public function invoked(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        if ($this->invocationMatcher === null) {
            throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\RuntimeException('No invocation matcher is set');
        }
        if ($this->methodNameMatcher === null) {
            throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\RuntimeException('No method matcher is set');
        }
        if ($this->afterMatchBuilderId !== null) {
            $builder = $invocation->getObject()->__phpunit_getInvocationMocker()->lookupId($this->afterMatchBuilderId);
            if (!$builder) {
                throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\RuntimeException(\sprintf('No builder found for match builder identification <%s>', $this->afterMatchBuilderId));
            }
            $matcher = $builder->getMatcher();
            if ($matcher && $matcher->invocationMatcher->hasBeenInvoked()) {
                $this->afterMatchBuilderIsInvoked = \true;
            }
        }
        $this->invocationMatcher->invoked($invocation);
        try {
            if ($this->parametersMatcher !== null && !$this->parametersMatcher->matches($invocation)) {
                $this->parametersMatcher->verify();
            }
        } catch (\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExpectationFailedException $e) {
            throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExpectationFailedException(\sprintf("Expectation failed for %s when %s\n%s", $this->methodNameMatcher->toString(), $this->invocationMatcher->toString(), $e->getMessage()), $e->getComparisonFailure());
        }
        if ($this->stub) {
            return $this->stub->invoke($invocation);
        }
        return $invocation->generateReturnValue();
    }
    /**
     * @param Invocation $invocation
     *
     * @throws RuntimeException
     * @throws ExpectationFailedException
     *
     * @return bool
     */
    public function matches(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        if ($this->afterMatchBuilderId !== null) {
            $builder = $invocation->getObject()->__phpunit_getInvocationMocker()->lookupId($this->afterMatchBuilderId);
            if (!$builder) {
                throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\RuntimeException(\sprintf('No builder found for match builder identification <%s>', $this->afterMatchBuilderId));
            }
            $matcher = $builder->getMatcher();
            if (!$matcher) {
                return \false;
            }
            if (!$matcher->invocationMatcher->hasBeenInvoked()) {
                return \false;
            }
        }
        if ($this->invocationMatcher === null) {
            throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\RuntimeException('No invocation matcher is set');
        }
        if ($this->methodNameMatcher === null) {
            throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\RuntimeException('No method matcher is set');
        }
        if (!$this->invocationMatcher->matches($invocation)) {
            return \false;
        }
        try {
            if (!$this->methodNameMatcher->matches($invocation)) {
                return \false;
            }
        } catch (\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExpectationFailedException $e) {
            throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExpectationFailedException(\sprintf("Expectation failed for %s when %s\n%s", $this->methodNameMatcher->toString(), $this->invocationMatcher->toString(), $e->getMessage()), $e->getComparisonFailure());
        }
        return \true;
    }
    /**
     * @throws RuntimeException
     * @throws ExpectationFailedException
     */
    public function verify() : void
    {
        if ($this->invocationMatcher === null) {
            throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\RuntimeException('No invocation matcher is set');
        }
        if ($this->methodNameMatcher === null) {
            throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\RuntimeException('No method matcher is set');
        }
        try {
            $this->invocationMatcher->verify();
            if ($this->parametersMatcher === null) {
                $this->parametersMatcher = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\AnyParameters();
            }
            $invocationIsAny = $this->invocationMatcher instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\AnyInvokedCount;
            $invocationIsNever = $this->invocationMatcher instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedCount && $this->invocationMatcher->isNever();
            if (!$invocationIsAny && !$invocationIsNever) {
                $this->parametersMatcher->verify();
            }
        } catch (\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExpectationFailedException $e) {
            throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExpectationFailedException(\sprintf("Expectation failed for %s when %s.\n%s", $this->methodNameMatcher->toString(), $this->invocationMatcher->toString(), \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestFailure::exceptionToString($e)));
        }
    }
    /**
     * @return string
     */
    public function toString() : string
    {
        $list = [];
        if ($this->invocationMatcher !== null) {
            $list[] = $this->invocationMatcher->toString();
        }
        if ($this->methodNameMatcher !== null) {
            $list[] = 'where ' . $this->methodNameMatcher->toString();
        }
        if ($this->parametersMatcher !== null) {
            $list[] = 'and ' . $this->parametersMatcher->toString();
        }
        if ($this->afterMatchBuilderId !== null) {
            $list[] = 'after ' . $this->afterMatchBuilderId;
        }
        if ($this->stub !== null) {
            $list[] = 'will ' . $this->stub->toString();
        }
        return \implode(' ', $list);
    }
}
