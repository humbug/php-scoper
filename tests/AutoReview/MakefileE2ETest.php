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

namespace Humbug\PhpScoper\AutoReview;

use PHPUnit\Framework\TestCase;
use function array_filter;
use function array_map;
use function array_unique;
use function array_values;
use function explode;
use function Safe\file_get_contents;
use function Safe\preg_match;
use function Safe\preg_match_all;

/**
 * @coversNothing
 *
 * @internal
 */
class MakefileE2ETest extends TestCase
{
    private const MAKEFILE_PATH = __DIR__.'/../../Makefile';

    private static string $makeFileContents;

    public static function setUpBeforeClass(): void
    {
        self::$makeFileContents = file_get_contents(self::MAKEFILE_PATH);
    }

    public function test_the_e2e_test_executes_all_the_e2e_sub_rules(): void
    {
        $mainE2ERule = self::retrieveE2ERule(self::$makeFileContents);
        $e2eSubRules = self::retrieveSubE2ERules(self::$makeFileContents);

        self::assertSame($e2eSubRules, $mainE2ERule);
    }

    public function test_it_lists_all_e2e_tests(): void
    {
        $expected = E2ECollector::getE2ENames();
        $actual = self::retrieveE2ERule(self::$makeFileContents);

        self::assertEqualsCanonicalizing($expected, $actual);
    }

    /**
     * @return list<string>
     */
    private static function retrieveE2ERule(string $makefileContents): array
    {
        if (1 !== preg_match(
            '/e2e:(?<steps>[\p{L}\d_ ]+)/u',
            $makefileContents,
            $matches,
        )) {
            self::assertFalse(false, 'Expected the string input to match the regex');
        }

        return array_values(
            array_filter(
                array_map(
                    'trim',
                    explode(
                        ' ',
                        $matches['steps'],
                    ),
                ),
            ),
        );
    }

    /**
     * @return list<string>
     */
    private static function retrieveSubE2ERules(string $makefileContents): array
    {
        if (1 !== preg_match_all(
            '/(?<step>e2e_\d+):/u',
            $makefileContents,
            $matches,
        )) {
            self::assertFalse(false, 'Expected the string input to match the regex');
        }

        return array_values(
            array_unique(
                array_filter(
                    array_map(
                        'trim',
                        $matches['step'],
                    ),
                ),
            ),
        );
    }
}
