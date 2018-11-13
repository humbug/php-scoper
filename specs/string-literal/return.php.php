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
        'title' => 'Scalar literal returned',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

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

PHP
    ,
];
