<?php

namespace _PhpScoper5b2c11ee6df50;

require_once 'BankAccount.php';
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase;
class BankAccountTest extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase
{
    protected $ba;
    protected function setUp()
    {
        $this->ba = new \_PhpScoper5b2c11ee6df50\BankAccount();
    }
    /**
     * @covers BankAccount::getBalance
     */
    public function testBalanceIsInitiallyZero()
    {
        $this->assertEquals(0, $this->ba->getBalance());
    }
    /**
     * @covers BankAccount::withdrawMoney
     */
    public function testBalanceCannotBecomeNegative()
    {
        try {
            $this->ba->withdrawMoney(1);
        } catch (\RuntimeException $e) {
            $this->assertEquals(0, $this->ba->getBalance());
            return;
        }
        $this->fail();
    }
    /**
     * @covers BankAccount::depositMoney
     */
    public function testBalanceCannotBecomeNegative2()
    {
        try {
            $this->ba->depositMoney(-1);
        } catch (\RuntimeException $e) {
            $this->assertEquals(0, $this->ba->getBalance());
            return;
        }
        $this->fail();
    }
    /**
     * @covers BankAccount::getBalance
     * @covers BankAccount::depositMoney
     * @covers BankAccount::withdrawMoney
     */
    public function testDepositWithdrawMoney()
    {
        $this->assertEquals(0, $this->ba->getBalance());
        $this->ba->depositMoney(1);
        $this->assertEquals(1, $this->ba->getBalance());
        $this->ba->withdrawMoney(1);
        $this->assertEquals(0, $this->ba->getBalance());
    }
}
