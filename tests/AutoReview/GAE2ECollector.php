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

use Humbug\PhpScoper\NotInstantiable;
use Symfony\Component\Yaml\Yaml;
use function sort;
use const SORT_STRING;

final class GAE2ECollector
{
    use NotInstantiable;

    private const GA_FILE = __DIR__.'/../../.github/workflows/e2e-tests.yaml';

    /**
     * @return list<string>
     */
    public static function getExecutedE2ETests(): array
    {
        static $names;

        if (!isset($names)) {
            $names = self::findE2ENames();
        }

        return $names;
    }

    /**
     * @return list<string>
     */
    private static function findE2ENames(): array
    {
        $parsedYaml = Yaml::parseFile(self::GA_FILE);

        /** @var string[] $names */
        $names = $parsedYaml['jobs']['e2e-tests']['strategy']['matrix']['e2e'];

        sort($names, SORT_STRING);

        return $names;
    }
}
