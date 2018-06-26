<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Builder;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub as BaseStub;
/**
 * Builder interface for stubs which are actions replacing an invocation.
 */
interface Stub extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Builder\Identity
{
    /**
     * Stubs the matching method with the stub object $stub. Any invocations of
     * the matched method will now be handled by the stub instead.
     *
     * @param BaseStub $stub
     *
     * @return Identity
     */
    public function will(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub $stub);
}
