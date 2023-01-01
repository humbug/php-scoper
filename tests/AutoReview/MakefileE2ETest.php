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
use function array_filter;
use function array_map;
use function array_values;
use function current;
use function Safe\file_get_contents;
use function str_starts_with;

/**
 * @coversNothing
 *
 * @internal
 */
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

            [33mcheck:[0m	  Runs all checks
            [33mclean:[0m	  Cleans all created artifacts
            [33mupdate_root_version:[0m  Checks the latest GitHub release and update COMPOSER_ROOT_VERSION accordingly
            [33mcs:[0m	  Fixes CS
            [33mcs_lint:[0m  Checks CS
            [33mphpstan:[0m  Runs PHPStan
            [33mbuild:[0m	  Builds the PHAR
            [33moutdated_fixtures:[0m  Reports outdated dependencies
            [33mtest:[0m		    Runs all the tests
            [33mvalidate_package:[0m   Validates the composer.json
            [33mcheck_composer_root_version:[0m  Checks that the COMPOSER_ROOT_VERSION is up to date
            [33mcovers_validator:[0m   Checks PHPUnit @coves tag
            [33mphpunit:[0m	    Runs PHPUnit tests
            [33mphpunit_coverage_infection:[0m  Runs PHPUnit tests with test coverage
            [33mphpunit_coverage_html:[0m	     Runs PHPUnit with code coverage with HTML report
            [33minfection:[0m	    Runs Infection
            [33mblackfire:[0m	    Runs Blackfire profiling
            [33me2e:[0m	  Runs end-to-end tests
            [33me2e_004:[0m  Runs end-to-end tests for the fixture set 004 — Minimalistic codebase
            [33me2e_005:[0m  Runs end-to-end tests for the fixture set 005 — Codebase with third-party code
            [33me2e_011:[0m  Runs end-to-end tests for the fixture set 011 — Codebase with exposed symbols
            [33me2e_013:[0m  Runs end-to-end tests for the fixture set 013 — Test the init command
            [33me2e_014:[0m  Runs end-to-end tests for the fixture set 014 — Codebase with PSR-0 autoloading
            [33me2e_015:[0m  Runs end-to-end tests for the fixture set 015 — Codebase with third-party code using PSR-0 autoloading
            [33me2e_016:[0m  Runs end-to-end tests for the fixture set 016 — Scoping of the Symfony Finder component
            [33me2e_017:[0m  Runs end-to-end tests for the fixture set 017 — Scoping of the Symfony DependencyInjection component
            [33me2e_018:[0m  Runs end-to-end tests for the fixture set 018 — Scoping of nikic/php-parser
            [33me2e_019:[0m  Runs end-to-end tests for the fixture set 019 — Scoping of the Symfony Console component
            [33me2e_020:[0m  Runs end-to-end tests for the fixture set 020 — Scoping of Infection
            [33me2e_024:[0m  Runs end-to-end tests for the fixture set 024 — Scoping of a codebase with global functions exposed
            [33me2e_025:[0m  Runs end-to-end tests for the fixture set 025 — Scoping of a codebase using third-party exposed functions
            [33me2e_027:[0m  Runs end-to-end tests for the fixture set 027 — Scoping of a Laravel
            [33me2e_028:[0m  Runs end-to-end tests for the fixture set 028 — Scoping of a Symfony project
            [33me2e_029:[0m  Runs end-to-end tests for the fixture set 029 — Scoping of the EasyRdf project
            [33me2e_030:[0m  Runs end-to-end tests for the fixture set 030 — Scoping of a codebase with globally registered functions
            [33me2e_031:[0m  Runs end-to-end tests for the fixture set 031 — Scoping of a codebase using symbols of a non-loaded PHP extension
            [33me2e_032:[0m  Runs end-to-end tests for the fixture set 032 — Scoping of a codebase using the isolated finder in the configuration
            [33me2e_033:[0m  Runs end-to-end tests for the fixture set 033 — Scoping of a codebase a function registered in the global namespace
            [33me2e_034:[0m  Runs end-to-end tests for the fixture set 034 — Leverage Composer InstalledVersions
            [33me2e_035:[0m  Runs end-to-end tests for the fixture set 035 — Tests tha composer autoloaded files are working fine

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
