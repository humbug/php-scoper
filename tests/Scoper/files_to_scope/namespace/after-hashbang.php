<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Namespace declarations after a hashbang',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    <<<'PHP'
#!/usr/bin/env php
<?php

namespace Foo;

----
#!/usr/bin/env php
<?php 
namespace Humbug\Foo;


PHP
    ,
];
