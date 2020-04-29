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

use function array_filter;
use function array_map;
use function array_unique;
use function array_values;
use function explode;
use function file_get_contents;
use PHPUnit\Framework\TestCase;
use function preg_match;
use function preg_match_all;

/**
 * @coversNothing
 */
class MakefileE2ETest extends TestCase
{
    public function test_the_e2e_test_executes_all_the_e2e_sub_rules(): void
    {
        $contents = file_get_contents(__DIR__.'/../Makefile');

        $mainE2ERule = $this->retrieveE2ERule($contents);
        $e2eSubRules = $this->retrieveSubE2ERules($contents);

        $this->assertSame($e2eSubRules, $mainE2ERule);
    }

    /**
     * @return string[]
     */
    private function retrieveE2ERule(string $makefileContents): array
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
                    explode(
                        ' ',
                        $matches['steps']
                    )
                )
            )
        );
    }

    /**
     * @return string[]
     */
    private function retrieveSubE2ERules(string $makefileContents): array
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
