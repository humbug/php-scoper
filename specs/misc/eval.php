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
        title: 'Eval',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'string' => <<<'PHP'
    <?php
    
    eval('
    <?php
    
    use Acme\Foo;
    
    ');
    
    ----
    <?php
    
    namespace Humbug;
    
    eval('
    <?php 
    namespace Humbug;
    
    use Humbug\\Acme\\Foo;
    ');
    
    PHP,

    'string with invalid PHP' => <<<'PHP'
    <?php
    
    eval('invalid PHP');
    
    ----
    <?php
    
    namespace Humbug;
    
    eval('invalid PHP');
    
    PHP,

    'concatenated string' => <<<'PHP'
    <?php
    
    eval('<?php'.' echo "Hello!";');
    
    ----
    <?php
    
    namespace Humbug;
    
    eval('<?php' . ' echo "Hello!";');
    
    PHP,

    'Nowdoc' => <<<'PHP'
    <?php
    
    eval(<<<'PHP_NOWDOC'
    <?php
    
    use Acme\Foo;
    
    PHP_NOWDOC
    );

    eval(<<<'PHP_NOWDOC'
    <?php
    
    use Acme\Foo;
    PHP_NOWDOC
    );

    ----
    <?php

    namespace Humbug;

    eval(<<<'PHP_NOWDOC'
    <?php
    
    namespace Humbug;
    
    use Humbug\Acme\Foo;
    
    PHP_NOWDOC
    );
    eval(<<<'PHP_NOWDOC'
    <?php
    
    namespace Humbug;
    
    use Humbug\Acme\Foo;
    PHP_NOWDOC
    );

    PHP,

    'Nowdoc with invalid PHP' => <<<'PHP'
    <?php
    
    eval(<<<'PHP_NOWDOC'
    Not.php
    PHP_NOWDOC
    );

    ----
    <?php

    namespace Humbug;

    eval(<<<'PHP_NOWDOC'
    Not.php
    PHP_NOWDOC
    );

    PHP,

    'Heredoc' => <<<'PHP'
    <?php
    
    eval(<<<PHP_HEREDOC
    <?php
    
    use Acme\Foo;
    
    PHP_HEREDOC
    );

    ----
    <?php

    namespace Humbug;

    eval(<<<PHP_HEREDOC
    <?php
    
    namespace Humbug;
    
    use Humbug\\Acme\\Foo;
    
    PHP_HEREDOC
    );

    PHP,

     'string with exposed function' => [
         exposeFunctions: ['Acme\foo'],
         expectedRecordedFunctions: [
             ['Acme\foo', 'Humbug\Acme\foo'],
         ],
         'payload' => <<<'PHP'
        <?php

        eval('
        <?php

        use Acme\Foo;

        ');

        ----
        <?php

        namespace Humbug;

        eval('
        <?php
        namespace Humbug;

        use Humbug\\Acme\\Foo;
        ');

        PHP,

    'string with invalid PHP' => <<<'PHP'
        <?php

        eval('invalid PHP');

        ----
        <?php

        namespace Humbug;

        eval('invalid PHP');

        PHP,

    'concatenated string' => <<<'PHP'
        <?php

        eval('<?php'.' echo "Hello!";');

        ----
        <?php

        namespace Humbug;

        eval('<?php' . ' echo "Hello!";');

        PHP,

    'Nowdoc' => <<<'PHP'
        <?php

        eval(<<<'PHP_NOWDOC'
        <?php

        use Acme\Foo;

        PHP_NOWDOC
        );

        eval(<<<'PHP_NOWDOC'
        <?php

        use Acme\Foo;
        PHP_NOWDOC
        );

        ----
        <?php

        namespace Humbug;

        eval(<<<'PHP_NOWDOC'
        <?php

        namespace Humbug;

        use Humbug\Acme\Foo;

        PHP_NOWDOC
        );
        eval(<<<'PHP_NOWDOC'
        <?php

        namespace Humbug;

        use Humbug\Acme\Foo;
        PHP_NOWDOC
        );

        PHP,

    'Nowdoc with invalid PHP' => <<<'PHP'
        <?php

        eval(<<<'PHP_NOWDOC'
        Not.php
        PHP_NOWDOC
        );

        ----
        <?php

        namespace Humbug;

        eval(<<<'PHP_NOWDOC'
        Not.php
        PHP_NOWDOC
        );

        PHP,

    'Heredoc' => <<<'PHP'
        <?php

        eval(<<<PHP_HEREDOC
        <?php

        use Acme\Foo;

        PHP_HEREDOC
        );

        ----
        <?php

        namespace Humbug;

        eval(<<<PHP_HEREDOC
        <?php

        namespace Humbug;

        use Humbug\\Acme\\Foo;

        PHP_HEREDOC
        );

        PHP,

    'string with exposed function' => [
        'expose-functions' => ['Acme\foo'],
        'expected-recorded-functions' => [
            ['Acme\foo', 'Humbug\Acme\foo'],
        ],
        'payload' => <<<'PHP'
            <?php

            eval('<?php

            namespace Acme;

            function foo() {}

            ');

            ----
            <?php

            namespace Humbug;

            eval('<?php

            namespace Humbug\\Acme;

            function foo()
            {
            }
            ');

            PHP,
    ],
];
