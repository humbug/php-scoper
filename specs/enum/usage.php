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

return [
    'meta' => [
        'title' => 'Enum declaration',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',

        'expose-global-constants' => false,
        'expose-global-classes' => false,
        'expose-global-functions' => false,
        'expose-namespaces' => [],
        'expose-constants' => [],
        'expose-classes' => [],
        'expose-functions' => [],

        'exclude-namespaces' => [],
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],

        'expected-recorded-classes' => [],
        'expected-recorded-functions' => [],
    ],

    'typehint and create an enum' => <<<'PHP'
    <?php

    namespace Acme;
    
    use Status;

    class BlogPost
    {
        public function __construct(
            public Status $status, 
        ) {}
    }
    $post = new BlogPost(Status::DRAFT);

    ----
    <?php
    
    namespace Humbug\Acme;
    
    use Humbug\Status;
    class BlogPost
    {
        public function __construct(public Status $status)
        {
        }
    }
    $post = new BlogPost(Status::DRAFT);
    
    PHP,

    'use an enum method' => <<<'PHP'
    <?php

    namespace Acme;
    
    use Status;

    $status = Status::ARCHIVED;
    $status->color();

    ----
    <?php
    
    namespace Humbug\Acme;
    
    use Humbug\Status;
    $status = Status::ARCHIVED;
    $status->color();
    
    PHP,

    'use instance of enum' => <<<'PHP'
    <?php

    namespace Acme;
    
    $statusC instanceof \Post\Status;

    ----
    <?php
    
    namespace Humbug\Acme;

    $statusC instanceof \Humbug\Post\Status;

    PHP,
];
