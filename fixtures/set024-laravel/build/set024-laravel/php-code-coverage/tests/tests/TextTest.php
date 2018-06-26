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
 * @covers SebastianBergmann\CodeCoverage\Report\Text
 */
class TextTest extends \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\TestCase
{
    public function testTextForBankAccountTest()
    {
        $text = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Text(50, 90, \false, \false);
        $this->assertStringMatchesFormatFile(TEST_FILES_PATH . 'BankAccount-text.txt', \str_replace(\PHP_EOL, "\n", $text->process($this->getCoverageForBankAccount())));
    }
    public function testTextForFileWithIgnoredLines()
    {
        $text = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Text(50, 90, \false, \false);
        $this->assertStringMatchesFormatFile(TEST_FILES_PATH . 'ignored-lines-text.txt', \str_replace(\PHP_EOL, "\n", $text->process($this->getCoverageForFileWithIgnoredLines())));
    }
    public function testTextForClassWithAnonymousFunction()
    {
        $text = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Text(50, 90, \false, \false);
        $this->assertStringMatchesFormatFile(TEST_FILES_PATH . 'class-with-anonymous-function-text.txt', \str_replace(\PHP_EOL, "\n", $text->process($this->getCoverageForClassWithAnonymousFunction())));
    }
}
