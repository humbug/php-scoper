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
        'title' => 'Use statements for traits',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',

        'expose-global-constants' => true,
        'expose-global-classes' => false,
        'expose-global-functions' => true,
        'expose-namespaces' => [],
        'expose-constants' => [],
        'expose-classes' => [],
        'expose-functions' => [],

        'exclude-namespaces' => [],
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],

        'expected-recorded-classes' => [],
        'expected-recorded-functions' => [],
    ],

    // https://github.com/humbug/php-scoper/issues/455
    'Use statement of a FQCN trait' => <<<'PHP'
        <?php

        namespace IvoPetkov;

        class HTML5DOMDocument {
            use \IvoPetkov\Internal\QuerySelector;
        }

        ----
        <?php

        namespace Humbug\IvoPetkov;

        class HTML5DOMDocument
        {
            use \Humbug\IvoPetkov\Internal\QuerySelector;
        }

        PHP,

    'Use statement of an imported trait' => <<<'PHP'
        <?php

        namespace IvoPetkov;

        use IvoPetkov\Internal\QuerySelector;

        class HTML5DOMDocument {
            use QuerySelector;
        }

        ----
        <?php

        namespace Humbug\IvoPetkov;

        use Humbug\IvoPetkov\Internal\QuerySelector;
        class HTML5DOMDocument
        {
            use QuerySelector;
        }

        PHP,
];
