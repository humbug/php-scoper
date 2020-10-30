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
        'minPhpVersion' => 70400,
        'title' => 'Arrow function in the global namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => false,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Global function call in the global scope' => <<<'PHP'
<?php

fn ($x) => $x;
fn (int $x) => $x;
fn (int $x): int => $x;
fn (Foo $x): Bar => $x;
fn (DateTimeImmutable $x): Closure => $x;
----
<?php

namespace Humbug;

fn($x) => $x;
fn(int $x) => $x;
fn(int $x): int => $x;
fn(\Humbug\Foo $x): \Humbug\Bar => $x;
fn(\DateTimeImmutable $x): \Closure => $x;

PHP
    ,

    'Global function call in the global scope with global symbols whitelisted' => [
        'whitelist-global-classes' => true,
        'whitelist-global-functions' => true,
        'payload' => <<<'PHP'
<?php

fn ($x) => $x;
fn (int $x) => $x;
fn (int $x): int => $x;
fn (Foo $x): Bar => $x;
fn (DateTimeImmutable $x): Closure => $x;
----
<?php

namespace Humbug;

fn($x) => $x;
fn(int $x) => $x;
fn(int $x): int => $x;
fn(\Humbug\Foo $x): \Humbug\Bar => $x;
fn(\DateTimeImmutable $x): \Closure => $x;

PHP
    ],

    'Global function call in the global scope with whitelisted symbols' => [
        'whitelist' => [
            'Foo',
            'Bar',
        ],
        'payload' => <<<'PHP'
<?php

fn ($x) => $x;
fn (int $x) => $x;
fn (int $x): int => $x;
fn (Foo $x): Bar => $x;
fn (DateTimeImmutable $x): Closure => $x;
----
<?php

namespace Humbug;

fn($x) => $x;
fn(int $x) => $x;
fn(int $x): int => $x;
fn(\Humbug\Foo $x): \Humbug\Bar => $x;
fn(\DateTimeImmutable $x): \Closure => $x;

PHP
    ],
];
