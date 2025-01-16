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

namespace Humbug\PhpScoper\Console;

/**
 * @internal
 */
final readonly class File
{
    public function __construct(
        public string $inputFilePath,
        public string $inputContents,
        public string $outputFilePath,
    ) {
    }

    public function withScopedContent(string $scopedContent): self
    {
        return new self(
            $this->inputFilePath,
            $scopedContent,
            $this->outputFilePath,
        );
    }
}
