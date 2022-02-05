<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Console;

use PHPUnit\Framework\Test;
use Symfony\Component\Console\Tester\ApplicationTester;

interface AppTesterTestCase extends Test
{
    public function getAppTester(): ApplicationTester;
}
