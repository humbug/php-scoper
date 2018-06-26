<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Error;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception;
/**
 * Wrapper for PHP errors.
 */
class Error extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception
{
    public function __construct(string $message, int $code, string $file, int $line, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->file = $file;
        $this->line = $line;
    }
}
