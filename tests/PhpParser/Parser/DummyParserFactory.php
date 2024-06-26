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

namespace Humbug\PhpScoper\PhpParser\Parser;

use PhpParser\Parser;
use PhpParser\PhpVersion;

final readonly class DummyParserFactory implements ParserFactory
{
    public function __construct(
        private Parser $parser,
    ) {
    }

    public function createParser(?PhpVersion $phpVersion = null): Parser
    {
        return $this->parser;
    }
}
