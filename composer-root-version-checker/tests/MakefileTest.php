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

namespace Humbug\PhpScoperComposerRootChecker\Tests;

use Fidry\Makefile\Test\BaseMakefileTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * @internal
 */
#[CoversNothing]
final class MakefileTest extends BaseMakefileTestCase
{
    protected static function getMakefilePath(): string
    {
        return __DIR__.'/../Makefile';
    }

    protected function getExpectedHelpOutput(): string
    {
        return <<<'EOF'
            [33mUsage:[0m
              make TARGET

            [32m#
            # Commands
            #---------------------------------------------------------------------------[0m

            [33mcheck_root_version:[0m  Checks that the Composer root version is up to date
            [33mdump_root_version:[0m   Dumps the latest Composer root version
            [33mautoreview:[0m    	     Runs the AutoReview checks
            [33mcs:[0m		     Runs the fixers
            [33mcs_lint:[0m	     Runs the linters
            [33mtest:[0m	    	     Runs the tests

            EOF;
    }
}
