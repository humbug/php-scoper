<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Traits',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'trait declaration' =>  <<<'PHP'
<?php

trait A {
    public function a() {}
}

class B {
    use C;
    use D {
        a as protected b;
        c as d;
        e as private;
    }
    use E, F, G {
        E::a insteadof F, G;
        E::b as protected c;
        E::d as e;
        E::f as private;
    }
}
----
<?php

trait A
{
    public function a()
    {
    }
}
class B
{
    use C;
    use D {
        a as protected b;
        c as d;
        e as private;
    }
    use E, F, G {
        E::a insteadof F, G;
        E::b as protected c;
        E::d as e;
        E::f as private;
    }
}

PHP
    ,

    'namespaced trait declaration' =>  <<<'PHP'
<?php

namespace Foo;

trait A {
    public function a() {}
}

class B {
    use C;
    use D {
        a as protected b;
        c as d;
        e as private;
    }
    use E, F, G {
        E::a insteadof F, G;
        E::b as protected c;
        E::d as e;
        E::f as private;
    }
}
----
<?php

namespace Humbug\Foo;

trait A
{
    public function a()
    {
    }
}
class B
{
    use C;
    use D {
        a as protected b;
        c as d;
        e as private;
    }
    use E, F, G {
        E::a insteadof F, G;
        E::b as protected c;
        E::d as e;
        E::f as private;
    }
}

PHP
    ,
];
