<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Constraint;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsEqual;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation as BaseInvocation;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper;
/**
 * Invocation matcher which looks for a specific method name in the invocations.
 *
 * Checks the method name all incoming invocations, the name is checked against
 * the defined constraint $constraint. If the constraint is met it will return
 * true in matches().
 */
class MethodName extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\StatelessInvocation
{
    /**
     * @var Constraint
     */
    private $constraint;
    /**
     * @param  Constraint|string
     * @param mixed $constraint
     *
     * @throws Constraint
     * @throws \PHPUnit\Framework\Exception
     */
    public function __construct($constraint)
    {
        if (!$constraint instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Constraint) {
            if (!\is_string($constraint)) {
                throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'string');
            }
            $constraint = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsEqual($constraint, 0, 10, \false, \true);
        }
        $this->constraint = $constraint;
    }
    /**
     * @return string
     */
    public function toString() : string
    {
        return 'method name ' . $this->constraint->toString();
    }
    /**
     * @param BaseInvocation $invocation
     *
     * @return bool
     */
    public function matches(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        return $this->constraint->evaluate($invocation->getMethodName(), '', \true);
    }
}
