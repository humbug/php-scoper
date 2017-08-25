<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Interface declarations',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'interface declaration' =>  <<<'PHP'
<?php

interface A extends C, D {
    public function a();
}
----
<?php

interface A extends C, D
{
    public function a();
}

PHP
    ,

    'namespaced interface declaration' =>  <<<'PHP'
<?php

namespace Foo;

interface A extends C, D
{
    public function a();
}
----
<?php

namespace Humbug\Foo;

interface A extends C, D
{
    public function a();
}

PHP
    ,
];
