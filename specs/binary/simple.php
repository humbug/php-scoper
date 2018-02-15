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
        'title' => 'Simple binary file',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'some statements made directly in the global namespace: wrap them in a namespace statement' => <<<'PHP'
<?php declare(strict_types=1);

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (\true) {
    echo "yo";
}

if (\false) {
    echo "oy";
}

----
<?php

declare (strict_types=1);
namespace Humbug;

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
if (\true) {
    echo "yo";
}
if (\false) {
    echo "oy";
}

PHP
    ,

    'some statements made directly in the global namespace with a shebang: wrap them in a namespace statement' => <<<'PHP'
#!/usr/bin/env php
<?php declare(strict_types=1);

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (\true) {
    echo "yo";
}

if (\false) {
    echo "oy";
}

----
#!/usr/bin/env php
<?php 
declare (strict_types=1);
namespace Humbug;

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
if (\true) {
    echo "yo";
}
if (\false) {
    echo "oy";
}

PHP
    ,
];
