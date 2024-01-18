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

use Fidry\Makefile\Parser;
use Fidry\Makefile\Rule;
use Fidry\Makefile\Test\BaseMakefileTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use function array_filter;
use function array_map;
use function array_values;
use function current;
use function Safe\file_get_contents;
use function str_starts_with;

/**
 * @internal
 */
#[CoversNothing]
class MakefileE2ETest extends BaseMakefileTestCase
{
    protected static function getMakefilePath(): string
    {
        return __DIR__.'/../../Makefile';
    }

    protected function getExpectedHelpOutput(): string
    {
        return <<<'EOF'
            [33mUsage:[0m
              make TARGET

            [32m#
            # Commands
            #---------------------------------------------------------------------------[0m

            [33mcheck:[0m  Runs all checks
            [33mbuild:[0m  Builds the PHAR
            [33mfixtures_composer_outdated:[0m  Reports outdated dependencies
            [33mcs:[0m  Fixes CS
            [33mcs_lint:[0m  Checks CS
            [33mautoreview:[0m  Runs the AutoReview checks
            [33mtest:[0m  Runs all the tests
            [33mcomposer_root_version_check:[0m  Runs all checks for the ComposerRootVersion app
            [33mcomposer_root_version_lint:[0m  Checks that the COMPOSER_ROOT_VERSION is up to date
            [33mcomposer_root_version_update:[0m  Updates the COMPOSER_ROOT_VERSION
            [33mphpunit_coverage_html:[0m  Runs PHPUnit with code coverage with HTML report
            [33me2e:[0m  Runs end-to-end tests
            [33mblackfire:[0m  Runs Blackfire profiling
            [33mclean:[0m  Cleans all created artifacts

            EOF;
    }

    public function test_the_e2e_test_executes_all_the_e2e_sub_rules(): void
    {
        $mainE2ERule = self::retrieveE2ERule();
        $e2eSubRules = self::retrieveSubE2ERules();

        self::assertSame($e2eSubRules, $mainE2ERule);
    }

    public function test_it_lists_all_e2e_tests(): void
    {
        $expected = E2ECollector::getE2ENames();
        $actual = self::retrieveE2ERule();

        self::assertEqualsCanonicalizing($expected, $actual);
    }

    /**
     * @return list<string>
     */
    private static function retrieveE2ERule(): array
    {
        $e2eRules = array_filter(
            self::getParsedRules(),
            static fn (Rule $rule) => $rule->getTarget() === 'e2e' && !$rule->isComment(),
        );

        $e2eRule = current($e2eRules);
        self::assertNotFalse($e2eRule, 'Expected to find the e2e rule in the Makefile.');

        return $e2eRule->getPrerequisites();
    }

    /**
     * @return list<string>
     */
    private static function retrieveSubE2ERules(): array
    {
        $e2eParsedRules = Parser::parse(
            file_get_contents(__DIR__.'/../../.makefile/e2e.file'),
        );

        $e2eRules = array_filter(
            $e2eParsedRules,
            static fn (Rule $rule) => str_starts_with($rule->getTarget(), 'e2e_') && !$rule->isComment(),
        );

        return array_values(
            array_map(
                static fn (Rule $rule) => $rule->getTarget(),
                $e2eRules,
            ),
        );
    }
}
