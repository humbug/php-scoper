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

use Humbug\PhpScoper\SpecFramework\Config\Meta;

return [
    'meta' => new Meta(
        title: 'First-class callables',
    ),

    'PHP native symbols' => <<<'PHP'
        <?php

        namespace Acme;

        use function something;
        use X\A;

        something(...);
        \something(...);

        A::foo(...);
        \X\A::foo(...);

        new A(...);
        new \X\A(...);

        $this->foo(...);
        $this?->foo(...);

        #[A(...)]
        function b() {}

        ----
        <?php

        namespace Humbug\Acme;

        use function Humbug\something;
        use Humbug\X\A;
        something(...);
        \Humbug\something(...);
        A::foo(...);
        \Humbug\X\A::foo(...);
        new A(...);
        new \Humbug\X\A(...);
        $this->foo(...);
        $this?->foo(...);
        #[A(...)]
        function b()
        {
        }

        PHP,
];
