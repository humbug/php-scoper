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

use Humbug\PhpScoper\Scoper\Spec\Meta;

return [
    'meta' => new Meta(
        title: 'Nowdoc',
    ),

    'string' => <<<'PHP'
        <?php

        $x = '
        <?php

        use Acme\Foo;

        ';

        ----
        <?php

        namespace Humbug;

        $x = '
        <?php

        use Acme\\Foo;

        ';

        PHP,

    'string with invalid PHP' => <<<'PHP'
        <?php

        $x = '
        <?php

        private foo() {}

        ';

        ----
        <?php

        namespace Humbug;

        $x = '
        <?php

        private foo() {}

        ';

        PHP,

    'Nowdoc' => <<<'PHP'
        <?php

        $x = <<<'PHP_NOWDOC'
        <?php

        use Acme\Foo;

        PHP_NOWDOC;
        ----
        <?php

        namespace Humbug;

        $x = <<<'PHP_NOWDOC'
        <?php

        namespace Humbug;

        use Humbug\Acme\Foo;

        PHP_NOWDOC
        ;

        PHP,

    'Nowdoc with non PHP' => <<<'PHP'
        <?php

        $x = <<<'PHP_NOWDOC'
        Not.php
        PHP_NOWDOC;

        ----
        <?php

        namespace Humbug;

        $x = <<<'PHP_NOWDOC'
        Not.php
        PHP_NOWDOC
        ;

        PHP,

    'Nowdoc with invalid PHP' => <<<'PHP'
        <?php

        $x = <<<'PHP_NOWDOC'
        <?php

        static foo() {}
        PHP_NOWDOC;

        ----
        <?php

        namespace Humbug;

        $x = <<<'PHP_NOWDOC'
        <?php

        static foo() {}
        PHP_NOWDOC
        ;

        PHP,

    'Empty nowdoc' => <<<'PHP'
        <?php

        $x = <<<'PHP_NOWDOC'
        PHP_NOWDOC;

        ----
        <?php

        namespace Humbug;

        $x = <<<'PHP_NOWDOC'
        PHP_NOWDOC
        ;

        PHP,

    'Heredoc' => <<<'PHP'
        <?php

        $x = <<<PHP_HEREDOC
        <?php

        use Acme\Foo;

        PHP_HEREDOC;

        ----
        <?php

        namespace Humbug;

        $x = <<<PHP_HEREDOC
        <?php

        use Acme\\Foo;

        PHP_HEREDOC
        ;

        PHP,
];
