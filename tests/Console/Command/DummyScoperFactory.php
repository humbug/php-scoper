<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Console\Command;

use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\ScoperFactory;
use PhpParser\Parser;

final class DummyScoperFactory extends ScoperFactory
{
    private Scoper $scoper;

    public function __construct(Parser $parser, Scoper $scoper)
    {
        parent::__construct($parser);

        $this->scoper = $scoper;
    }

    public function createScoper(): Scoper
    {
        return $this->scoper;
    }
}
