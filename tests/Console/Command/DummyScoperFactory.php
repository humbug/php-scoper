<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Console\Command;

use Humbug\PhpScoper\Configuration\Configuration;
use Humbug\PhpScoper\Scoper\Scoper;
use Humbug\PhpScoper\Scoper\ScoperFactory;
use PhpParser\Parser;

final class DummyScoperFactory extends ScoperFactory
{
    private Scoper $scoper;

    public function __construct(Parser $parser, Scoper $scoper)
    {
        parent::__construct($parser);

        $this->scoper = $scoper;
    }

    public function createScoper(Configuration $configuration): Scoper
    {
        return $this->scoper;
    }
}
