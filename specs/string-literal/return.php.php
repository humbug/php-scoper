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

use Humbug\PhpScoper\Scoper\Spec\Meta;

return [
    'meta' => new Meta(
        title: 'Scalar literal returned',
    ),

    'String argument' => <<<'PHP'
        <?php

        function () {
            return 'Symfony\\Component\\Yaml\\Ya_1';
        };

        function () {
            return '\\Symfony\\Component\\Yaml\\Ya_1';
        };

        function () {
            return 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        };

        function () {
            return '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        };

        function () {
            return 'Closure';
        };

        function () {
            return 'usedAttributes';
        };

        function () {
            return 'FOO';
        };

        function () {
            return 'PHP_EOL';
        };

        ----
        <?php

        namespace Humbug;

        function () {
            return 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        };
        function () {
            return 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        };
        function () {
            return 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        };
        function () {
            return 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        };
        function () {
            return 'Closure';
        };
        function () {
            return 'usedAttributes';
        };
        function () {
            return 'FOO';
        };
        function () {
            return 'PHP_EOL';
        };

        PHP,
];
