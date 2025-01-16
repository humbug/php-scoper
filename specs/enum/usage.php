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
        title: 'Enum declaration',
    ),

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
