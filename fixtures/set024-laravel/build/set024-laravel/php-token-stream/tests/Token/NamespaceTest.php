<?php

namespace _PhpScoper5b2c11ee6df50;

/*
 * This file is part of php-token-stream.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase;
class PHP_Token_NamespaceTest extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase
{
    /**
     * @covers PHP_Token_NAMESPACE::getName
     */
    public function testGetName()
    {
        $tokenStream = new \_PhpScoper5b2c11ee6df50\PHP_Token_Stream(\TEST_FILES_PATH . 'classInNamespace.php');
        foreach ($tokenStream as $token) {
            if ($token instanceof \_PhpScoper5b2c11ee6df50\PHP_Token_NAMESPACE) {
                $this->assertSame('_PhpScoper5b2c11ee6df50\\Foo\\Bar', $token->getName());
            }
        }
    }
    public function testGetStartLineWithUnscopedNamespace()
    {
        $tokenStream = new \_PhpScoper5b2c11ee6df50\PHP_Token_Stream(\TEST_FILES_PATH . 'classInNamespace.php');
        foreach ($tokenStream as $token) {
            if ($token instanceof \_PhpScoper5b2c11ee6df50\PHP_Token_NAMESPACE) {
                $this->assertSame(2, $token->getLine());
            }
        }
    }
    public function testGetEndLineWithUnscopedNamespace()
    {
        $tokenStream = new \_PhpScoper5b2c11ee6df50\PHP_Token_Stream(\TEST_FILES_PATH . 'classInNamespace.php');
        foreach ($tokenStream as $token) {
            if ($token instanceof \_PhpScoper5b2c11ee6df50\PHP_Token_NAMESPACE) {
                $this->assertSame(2, $token->getEndLine());
            }
        }
    }
    public function testGetStartLineWithScopedNamespace()
    {
        $tokenStream = new \_PhpScoper5b2c11ee6df50\PHP_Token_Stream(\TEST_FILES_PATH . 'classInScopedNamespace.php');
        foreach ($tokenStream as $token) {
            if ($token instanceof \_PhpScoper5b2c11ee6df50\PHP_Token_NAMESPACE) {
                $this->assertSame(2, $token->getLine());
            }
        }
    }
    public function testGetEndLineWithScopedNamespace()
    {
        $tokenStream = new \_PhpScoper5b2c11ee6df50\PHP_Token_Stream(\TEST_FILES_PATH . 'classInScopedNamespace.php');
        foreach ($tokenStream as $token) {
            if ($token instanceof \_PhpScoper5b2c11ee6df50\PHP_Token_NAMESPACE) {
                $this->assertSame(8, $token->getEndLine());
            }
        }
    }
}
