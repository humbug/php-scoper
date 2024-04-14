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

use Humbug\PhpScoper\SpecFramework\Config\Meta;
use Humbug\PhpScoper\SpecFramework\Config\SpecWithConfig;

return [
    'meta' => new Meta(
        title: 'Example of simple spec file',
        prefix: 'Humbug',
        minPhpVersion: 72_000,
        maxPhpVersion: 83_000,
        exposeGlobalConstants: true,
        exposeGlobalClasses: true,
        exposeGlobalFunctions: true,
        exposeNamespaces: ['ExposedNamespace'],
        exposeConstants: ['EXPOSED_CONST'],
        exposeClasses: ['ExposedClass'],
        exposeFunctions: ['exposed_function'],
        excludeNamespaces: ['ExcludedNamespace'],
        excludeConstants: ['EXCLUDED_CONST'],
        excludeClasses: ['ExcludedClass'],
        excludeFunctions: ['excluded_function'],
        expectedRecordedClasses: ['Acme\RecordedClass', 'Humbug\Acme\RecordedClass'],
        expectedRecordedFunctions: ['Acme\recorded_function', 'Humbug\Acme\recorded_function'],
        expectedRecordedAmbiguousFunctions: ['recorded_ambiguous_function', 'Humbug\recorded_ambiguous_function'],
    ),

    'Spec with default meta values' => <<<'PHP'
        echo "Hello world!";

        ----
        namespace Humbug;

        echo "Hello world!";

        PHP,

    'Spec with the more verbose form' => SpecWithConfig::create(
        spec: <<<'PHP'
            echo "Hello world!";

            ----
            namespace Humbug;

            echo "Hello world!";

            PHP,
    ),

    'Spec with overridden meta values' => SpecWithConfig::create(
        prefix: 'AnotherPrefix',
        minPhpVersion: 73_000,
        maxPhpVersion: 82_000,
        exposeGlobalConstants: false,
        exposeGlobalClasses: false,
        exposeGlobalFunctions: false,
        exposeNamespaces: ['AnotherExposedNamespace'],
        exposeConstants: ['ANOTHER_EXPOSED_CONST'],
        exposeClasses: ['AnotherExposedClass'],
        exposeFunctions: ['another_exposed_function'],
        excludeNamespaces: ['AnotherExcludedNamespace'],
        excludeConstants: ['ANOTHER_EXCLUDED_CONST'],
        excludeClasses: ['AnotherExcludedClass'],
        excludeFunctions: ['another_excluded_function'],
        expectedRecordedClasses: ['AnotherRecordedClass'],
        expectedRecordedFunctions: ['AnotherRecordedFunction'],
        spec: <<<'PHP'
            echo "Hello world!";

            ----
            namespace Humbug;

            echo "Hello world!";

            PHP,
    ),
];
