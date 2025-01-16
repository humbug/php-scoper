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
        title: 'Exposed class declaration',
        exposeClasses: ['A'],
        expectedRecordedClasses: [
            ['A', 'Humbug\A'],
        ],
    ),

    'Exposed class within an if block' => <<<'PHP'
        <?php

        if ($condition) {
            class A {
                public function a() {}
            }
        }
        ----
        <?php

        namespace Humbug;

        if ($condition) {
            class A
            {
                public function a()
                {
                }
            }
            \class_alias('Humbug\A', 'A', \false);
        }

        PHP,

    'Exposed interface within an if block' => <<<'PHP'
        <?php

        if ($condition) {
            interface A {
                public function a();
            }
        }
        ----
        <?php

        namespace Humbug;

        if ($condition) {
            interface A
            {
                public function a();
            }
            \class_alias('Humbug\A', 'A', \false);
        }

        PHP,

    'Exposed class within a nested if block' => <<<'PHP'
        <?php

        if ($condition) {
            if ($anotherCondition) {
                class A {
                    public function a() {}
                }
            }
        }
        ----
        <?php

        namespace Humbug;

        if ($condition) {
            if ($anotherCondition) {
                class A
                {
                    public function a()
                    {
                    }
                }
                \class_alias('Humbug\A', 'A', \false);
            }
        }

        PHP,

    'Exposed class within an else' => <<<'PHP'
        <?php

        if ($condition) {
        } else {
            class A {
                public function a() {}
            }
        }
        ----
        <?php

        namespace Humbug;

        if ($condition) {
        } else {
            class A
            {
                public function a()
                {
                }
            }
            \class_alias('Humbug\A', 'A', \false);
        }

        PHP,

    'Exposed class within an elseif' => <<<'PHP'
        <?php

        if ($condition) {
        } elseif ($anotherCondition) {
            class A {
                public function a() {}
            }
        }
        ----
        <?php

        namespace Humbug;

        if ($condition) {
        } elseif ($anotherCondition) {
            class A
            {
                public function a()
                {
                }
            }
            \class_alias('Humbug\A', 'A', \false);
        }

        PHP,

    'Exposed class within a switch case' => <<<'PHP'
        <?php

        switch ($condition) {
            case $case1Condition:
                class A {
                    public function a1() {}
                }
                break;

            case $case2Condition:
                class A {
                    public function a2() {}
                }
                break;
        }
        ----
        <?php

        namespace Humbug;

        switch ($condition) {
            case $case1Condition:
                class A
                {
                    public function a1()
                    {
                    }
                }
                \class_alias('Humbug\A', 'A', \false);
                break;
            case $case2Condition:
                class A
                {
                    public function a2()
                    {
                    }
                }
                \class_alias('Humbug\A', 'A', \false);
                break;
        }

        PHP,

    'Exposed class within a try statement' => <<<'PHP'
        <?php

        try {
            class A {
                public function a() {}
            }
        } catch (\Error) {
        }
        ----
        <?php

        namespace Humbug;

        try {
            class A
            {
                public function a()
                {
                }
            }
            \class_alias('Humbug\A', 'A', \false);
        } catch (\Error) {
        }

        PHP,

    'Exposed class within a catch statement' => <<<'PHP'
        <?php

        try {
        } catch (\Error) {
            class A {
                public function a() {}
            }
        }
        ----
        <?php

        namespace Humbug;

        try {
        } catch (\Error) {
            class A
            {
                public function a()
                {
                }
            }
            \class_alias('Humbug\A', 'A', \false);
        }

        PHP,

    'Exposed class within a finally statement' => <<<'PHP'
        <?php

        try {
        } finally {
            class A {
                public function a() {}
            }
        }
        ----
        <?php

        namespace Humbug;

        try {
        } finally {
            class A
            {
                public function a()
                {
                }
            }
            \class_alias('Humbug\A', 'A', \false);
        }

        PHP,
];
