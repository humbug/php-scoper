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

return [
    'meta' => [
        'title' => 'Example of simple spec file',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',

        'expose-global-constants' => true,
        'expose-global-classes' => true,
        'expose-global-functions' => true,
        'expose-namespaces' => ['ExposedNamespace'],
        'expose-constants' => ['EXPOSED_CONST'],
        'expose-classes' => ['ExposedClass'],
        'expose-functions' => ['exposed_function'],

        'exclude-namespaces' => ['ExcludedNamespace'],
        'exclude-constants' => ['EXCLUDED_CONST'],
        'exclude-classes' => ['ExcludedClass'],
        'exclude-functions' => ['excluded_function'],

        'expected-recorded-classes' => ['Acme\RecordedClass', 'Humbug\Acme\RecordedClass'],
        'expected-recorded-functions' => ['Acme\recorded_function', 'Humbug\Acme\recorded_function'],
    ],

    'Spec with default meta values' => <<<'PHP'
        echo "Hello world!";

        ----
        namespace Humbug;

        echo "Hello world!";

        PHP,

    'Spec with the more verbose form' => [
        'payload' => <<<'PHP'
            echo "Hello world!";

            ----
            namespace Humbug;

            echo "Hello world!";

            PHP,
    ],

    'Spec with overridden meta values' => [
        'prefix' => 'AnotherPrefix',

        'expose-global-constants' => false,
        'expose-global-classes' => false,
        'expose-global-functions' => false,
        'expose-namespaces' => ['AnotherExposedNamespace'],
        'expose-constants' => ['ANOTHER_EXPOSED_CONST'],
        'expose-classes' => ['AnotherExposedClass'],
        'expose-functions' => ['another_exposed_function'],

        'exclude-namespaces' => ['AnotherExcludedNamespace'],
        'exclude-constants' => ['ANOTHER_EXCLUDED_CONST'],
        'exclude-classes' => ['AnotherExcludedClass'],
        'exclude-functions' => ['another_excluded_function'],

        'expected-recorded-classes' => ['AnotherRecordedClass'],
        'expected-recorded-functions' => ['AnotherRecordedFunction'],

        'payload' => <<<'PHP'
            echo "Hello world!";

            ----
            namespace Humbug;

            echo "Hello world!";

            PHP,
    ],
];
