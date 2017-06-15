<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser;

use PhpParser\ErrorHandler;
use PhpParser\Node;
use PhpParser\Parser;

final class FakeParser implements Parser
{
    /**
     * @inheritdoc
     */
    public function parse($code, ErrorHandler $errorHandler = null)
    {
        throw new \LogicException();
    }
}