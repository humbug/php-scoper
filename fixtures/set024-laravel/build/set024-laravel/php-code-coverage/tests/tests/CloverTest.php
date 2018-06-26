<?php

/*
 * This file is part of the php-code-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report;

use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\TestCase;
/**
 * @covers SebastianBergmann\CodeCoverage\Report\Clover
 */
class CloverTest extends \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\TestCase
{
    public function testCloverForBankAccountTest()
    {
        $clover = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Clover();
        $this->assertStringMatchesFormatFile(TEST_FILES_PATH . 'BankAccount-clover.xml', $clover->process($this->getCoverageForBankAccount(), null, 'BankAccount'));
    }
    public function testCloverForFileWithIgnoredLines()
    {
        $clover = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Clover();
        $this->assertStringMatchesFormatFile(TEST_FILES_PATH . 'ignored-lines-clover.xml', $clover->process($this->getCoverageForFileWithIgnoredLines()));
    }
    public function testCloverForClassWithAnonymousFunction()
    {
        $clover = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Clover();
        $this->assertStringMatchesFormatFile(TEST_FILES_PATH . 'class-with-anonymous-function-clover.xml', $clover->process($this->getCoverageForClassWithAnonymousFunction()));
    }
}
