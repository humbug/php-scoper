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
        'title' => 'single-part new statements in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // Won't do anything here as this class is part of the global namespace.
    'single-part' => <<<'PHP'
<?php

new Foo();
----
<?php

new \Foo();

PHP
    ,

    // Won't do anything here as this class is part of the global namespace.
    'FQ single-part' => <<<'PHP'
<?php

new \Foo();
----
<?php

new \Foo();

PHP
    ,

    // See `scope.inc.php` for the built-in global whitelisted classes
    '(global) whitelisted single-part' => <<<'PHP'
<?php

new AppKernel();
----
<?php

new \Humbug\AppKernel();

PHP
    ,

    // See `scope.inc.php` for the built-in global whitelisted classes
    '(global) whitelisted FQ single-part' => <<<'PHP'
<?php

new \AppKernel();
----
<?php

new \Humbug\AppKernel();

PHP
    ,
];
