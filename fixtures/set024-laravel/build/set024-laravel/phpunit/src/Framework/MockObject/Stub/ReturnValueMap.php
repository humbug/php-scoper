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

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub;
/**
 * Stubs a method by returning a value from a map.
 */
class ReturnValueMap implements \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub
{
    /**
     * @var array
     */
    private $valueMap;
    public function __construct(array $valueMap)
    {
        $this->valueMap = $valueMap;
    }
    public function invoke(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        $parameterCount = \count($invocation->getParameters());
        foreach ($this->valueMap as $map) {
            if (!\is_array($map) || $parameterCount !== \count($map) - 1) {
                continue;
            }
            $return = \array_pop($map);
            if ($invocation->getParameters() === $map) {
                return $return;
            }
        }
    }
    public function toString() : string
    {
        return 'return value from a map';
    }
}
