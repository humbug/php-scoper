<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'class constant reference in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // Won't do anything here as this class is part of the global namespace.
    'single-part' =>  <<<'PHP'
<?php

Command::MAIN_CONST;
----
<?php

\Command::MAIN_CONST;

PHP
    ,

    // Won't do anything here as this class is part of the global namespace.
    'FQ single-part' =>  <<<'PHP'
<?php

\Command::MAIN_CONST;
----
<?php

\Command::MAIN_CONST;

PHP
    ,

    // See `scope.inc.php` for the built-in global whitelisted classes
    '(global) whitelisted single-part' =>  <<<'PHP'
<?php

AppKernel::MAIN_CONST;
----
<?php

\Humbug\AppKernel::MAIN_CONST;

PHP
    ,

    // See `scope.inc.php` for the built-in global whitelisted classes
    '(global) whitelisted FQ single-part' =>  <<<'PHP'
<?php

\AppKernel::MAIN_CONST;
----
<?php

\Humbug\AppKernel::MAIN_CONST;

PHP
    ,
];
