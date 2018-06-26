<?php

/*
 * This file is part of phpunit/php-timer.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\SebastianBergmann\Timer;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase;
/**
 * @covers \SebastianBergmann\Timer\Timer
 */
class TimerTest extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase
{
    public function testStartStop() : void
    {
        $this->assertInternalType('float', \_PhpScoper5b2c11ee6df50\SebastianBergmann\Timer\Timer::stop());
    }
    /**
     * @dataProvider secondsProvider
     */
    public function testSecondsToTimeString(string $string, string $seconds) : void
    {
        $this->assertEquals($string, \_PhpScoper5b2c11ee6df50\SebastianBergmann\Timer\Timer::secondsToTimeString($seconds));
    }
    public function testTimeSinceStartOfRequest() : void
    {
        $this->assertStringMatchesFormat('%f %s', \_PhpScoper5b2c11ee6df50\SebastianBergmann\Timer\Timer::timeSinceStartOfRequest());
    }
    public function testTimeSinceStartOfRequest2() : void
    {
        if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            unset($_SERVER['REQUEST_TIME_FLOAT']);
        }
        $this->assertStringMatchesFormat('%f %s', \_PhpScoper5b2c11ee6df50\SebastianBergmann\Timer\Timer::timeSinceStartOfRequest());
    }
    /**
     * @backupGlobals     enabled
     */
    public function testTimeSinceStartOfRequest3() : void
    {
        if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            unset($_SERVER['REQUEST_TIME_FLOAT']);
        }
        if (isset($_SERVER['REQUEST_TIME'])) {
            unset($_SERVER['REQUEST_TIME']);
        }
        $this->expectException(\_PhpScoper5b2c11ee6df50\SebastianBergmann\Timer\RuntimeException::class);
        \_PhpScoper5b2c11ee6df50\SebastianBergmann\Timer\Timer::timeSinceStartOfRequest();
    }
    public function testResourceUsage() : void
    {
        $this->assertStringMatchesFormat('Time: %s, Memory: %fMB', \_PhpScoper5b2c11ee6df50\SebastianBergmann\Timer\Timer::resourceUsage());
    }
    public function secondsProvider()
    {
        return [['0 ms', 0], ['1 ms', 0.001], ['10 ms', 0.01], ['100 ms', 0.1], ['999 ms', 0.999], ['1 second', 0.9999], ['1 second', 1], ['2 seconds', 2], ['59.9 seconds', 59.9], ['59.99 seconds', 59.99], ['59.99 seconds', 59.999], ['1 minute', 59.9999], ['59 seconds', 59.001], ['59.01 seconds', 59.01], ['1 minute', 60], ['1.01 minutes', 61], ['2 minutes', 120], ['2.01 minutes', 121], ['59.99 minutes', 3599.9], ['59.99 minutes', 3599.99], ['59.99 minutes', 3599.999], ['1 hour', 3599.9999], ['59.98 minutes', 3599.001], ['59.98 minutes', 3599.01], ['1 hour', 3600], ['1 hour', 3601], ['1 hour', 3601.9], ['1 hour', 3601.99], ['1 hour', 3601.999], ['1 hour', 3601.9999], ['1.01 hours', 3659.9999], ['1.01 hours', 3659.001], ['1.01 hours', 3659.01], ['2 hours', 7199.9999]];
    }
}
