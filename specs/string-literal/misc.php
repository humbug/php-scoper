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
        title: 'String literal assigned to a variable',
    ),

    'PHP heredoc as argument' => SpecWithConfig::create(
        expectedRecordedAmbiguousFunctions: [
            ['sprintf', 'Humbug\sprintf'],
        ],
        spec: <<<'PHP'
            <?php

            declare(strict_types=1);

            namespace Acme;

            sprintf( <<<'_PHP'
            if (!function_exists('%1$s')) {
                function %1$s() {
                    return \%2$s(func_get_args());
                }
            }
            _PHP
                    ,
                    'foo',
                    'bar'
            );

            ----
            <?php

            declare (strict_types=1);
            namespace Humbug\Acme;

            sprintf(<<<'_PHP'
            if (!function_exists('%1$s')) {
                function %1$s() {
                    return \%2$s(func_get_args());
                }
            }
            _PHP
            , 'foo', 'bar');

            PHP,
    ),
];
