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

namespace Humbug\PhpScoper;

use PHPUnit\Framework\TestCase;
use function array_filter;
use function array_map;
use function array_unique;
use function array_values;
use function explode;
use function file_get_contents;
use function preg_match;
use function preg_match_all;

/**
 * @coversNothing
 */
class MakefileE2ETest extends TestCase
{
    public function test_the_e2e_test_executes_all_the_e2e_subsets()
    {
        $contents = file_get_contents(__DIR__.'/../Makefile');

        $e2e = $this->retrieveE2EStep($contents);
        $sube2e = $this->retrieveE2ESubSteps($contents);

        $this->assertSame($sube2e, $e2e);
    }

    /**
     * @return string[]
     */
    private function retrieveE2EStep(string $makefileContents): array
    {
        if (1 !== preg_match(
            '/e2e:(?<steps>[\p{L}\d_ ]+)/u',
            $makefileContents,
            $matches
        )) {
            $this->assertFalse(false, 'Expected the string input to match the regex');
        }

        return array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(' ', $matches['steps']
                    )
                )
            )
        );
    }

    /**
     * @return string[]
     */
    private function retrieveE2ESubSteps(string $makefileContents): array
    {
        if (1 !== preg_match_all(
            '/(?<step>e2e_\d+):/u',
            $makefileContents,
            $matches
        )) {
            $this->assertFalse(false, 'Expected the string input to match the regex');
        }

        return array_values(
            array_unique(
                array_filter(
                    array_map(
                        'trim',
                        $matches['step']
                    )
                )
            )
        );
    }
}
