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
        title: 'Ensures the exposed symbols follow the required hierarchy.',
        exposeClasses: ['/.h*/'],
    ),

    'PHP 8.1 Polyfill (right order)' => SpecWithConfig::create(
        spec: <<<'PHP'
            <?php

            interface Stringeable {}
            class PhpTokens implements Stringeable {}

            ----
            <?php

            namespace Humbug;

            interface Stringeable
            {
            }
            \class_alias('Humbug\Stringeable', 'Stringeable', \false);
            class PhpTokens implements \Humbug\Stringeable
            {
            }
            \class_alias('Humbug\PhpTokens', 'PhpTokens', \false);

            PHP,
        expectedRecordedClasses: [
            ['Stringeable', 'Humbug\Stringeable'],
            ['PhpTokens', 'Humbug\PhpTokens'],
        ],
    ),

    'PHP 8.1 Polyfill (wrong order)' => SpecWithConfig::create(
        spec: <<<'PHP'
            <?php

            class PhpTokens implements Stringeable {}
            interface Stringeable {}

            ----
            <?php

            namespace Humbug;
            
            class PhpTokens implements \Humbug\Stringeable
            {
            }
            \class_alias('Humbug\PhpTokens', 'PhpTokens', \false);
            interface Stringeable
            {
            }
            \class_alias('Humbug\Stringeable', 'Stringeable', \false);

            PHP,
        expectedRecordedClasses: [
            ['Stringeable', 'Humbug\Stringeable'],
            ['PhpTokens', 'Humbug\PhpTokens'],
        ],
    ),

    'simple case with extend' => SpecWithConfig::create(
        spec: <<<'PHP'
            <?php

            class Frame extends Window {}
            abstract class Window implements ObjectInterface {}
            interface Component extends FrameInterface, ObjectInterface {}
            interface FrameInterface {}
            interface ObjectInterface {}

            ----
            <?php

            namespace Humbug;
            
            class PhpTokens implements \Humbug\Stringeable
            {
            }
            \class_alias('Humbug\PhpTokens', 'PhpTokens', \false);
            interface Stringeable
            {
            }
            \class_alias('Humbug\Stringeable', 'Stringeable', \false);

            PHP,
        expectedRecordedClasses: [
            ['Stringeable', 'Humbug\Stringeable'],
            ['PhpTokens', 'Humbug\PhpTokens'],
        ],
    ),
];
