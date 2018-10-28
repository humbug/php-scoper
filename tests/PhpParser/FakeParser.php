<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Humbug\PhpScoper\PhpParser;

use LogicException;
use PhpParser\ErrorHandler;
use PhpParser\Parser;

final class FakeParser implements Parser
{
    /**
     * @inheritdoc
     */
    public function parse(string $code, ErrorHandler $errorHandler = null)
    {
        throw new LogicException();
    }
}
