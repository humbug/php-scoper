<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'global constant reference in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // Won't do anything here as this class is part of the global namespace.
    'single-part' =>  <<<'PHP'
<?php

DUMMY_CONST;
----
<?php

\DUMMY_CONST;

PHP
    ,

    // Won't do anything here as this class is part of the global namespace.
    'FQ single-part' =>  <<<'PHP'
<?php

\DUMMY_CONST;
----
<?php

\DUMMY_CONST;

PHP
    ,
];
