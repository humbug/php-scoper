<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Interface declaration',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'Declaration in the global namespace: do not do anything.' =>  <<<'PHP'
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

    'Declaration in a namespace: prefix the namespace.' =>  <<<'PHP'
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

    'Multiple declarations in different namespaces: prefix each namespace.' =>  <<<'PHP'
<?php

namespace X {
    interface A extends D, E
    {
        public function a();
    }
}

namespace Y {
    interface B extends D, E
    {
        public function a();
    }
}

namespace Z {
    interface C extends D, E
    {
        public function a();
    }
}
----
<?php

namespace Humbug\X {
    interface A extends D, E
    {
        public function a();
    }
}

namespace Humbug\Y {
    interface B extends D, E
    {
        public function a();
    }
}

namespace Humbug\Z {
    interface C extends D, E
    {
        public function a();
    }
}

PHP
    ,
];
