<?php

declare(strict_types=1);

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
}