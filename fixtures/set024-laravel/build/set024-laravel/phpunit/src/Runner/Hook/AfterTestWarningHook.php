<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Runner;

interface AfterTestWarningHook extends \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestHook
{
    public function executeAfterTestWarning(string $test, string $message, float $time) : void;
}
