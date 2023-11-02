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
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use function array_filter;
use function array_map;
use function in_array;
use function iterator_to_array;
use function sort;
use function substr;
use const SORT_STRING;

final class E2ECollector
{
    use NotInstantiable;

    private const E2E_DIR = __DIR__.'/../../fixtures';

    private const NON_E2E_TESTS = [
        'set000',
        'set001',
        'set002',
        'set003',
        'set006',
        'set007',
        'set008',
        'set009',
        'set010',
        'set012',
    ];

    private const E2E_TEST_WITHOUT_FIXTURE_DIR = [
        'e2e_038',
    ];

    /**
     * @return list<string>
     */
    public static function getE2ENames(): array
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
        $finder = Finder::create()
            ->directories()
            ->in(self::E2E_DIR)
            ->depth(0);

        $names = array_filter(
            array_map(
                self::extractName(...),
                iterator_to_array($finder, false),
            ),
        );
        $names = [...$names, ...self::E2E_TEST_WITHOUT_FIXTURE_DIR];

        sort($names, SORT_STRING);

        return $names;
    }

    private static function extractName(SplFileInfo $fileInfo): ?string
    {
        $filename = $fileInfo->getFilename();

        if (in_array($filename, self::NON_E2E_TESTS, true)) {
            return null;
        }

        $setNumber = substr($filename, 3, 3);

        return 'e2e_'.$setNumber;
    }
}
