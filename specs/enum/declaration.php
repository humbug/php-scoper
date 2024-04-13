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
        title: 'Enum declaration',
    ),

    'minimal enum declaration' => <<<'PHP'
        <?php

        enum Status {
            case DRAFT;
            case PUBLISHED;
            case ARCHIVED;
        }

        ----
        <?php

        namespace Humbug;

        enum Status
        {
            case DRAFT;
            case PUBLISHED;
            case ARCHIVED;
        }

        PHP,

    'enum with methods' => <<<'PHP'
        <?php

        enum Status {
            case DRAFT;
            case PUBLISHED;
            case ARCHIVED;

            public function color(): string {
                return match($this) {
                    Status::DRAFT => 'grey',
                    Status::PUBLISHED => 'green',
                    self::ARCHIVED => 'red',
                };
            }
        }

        ----
        <?php

        namespace Humbug;

        enum Status
        {
            case DRAFT;
            case PUBLISHED;
            case ARCHIVED;
            public function color() : string
            {
                return match ($this) {
                    Status::DRAFT => 'grey',
                    Status::PUBLISHED => 'green',
                    self::ARCHIVED => 'red',
                };
            }
        }

        PHP,

    'enum with interface' => <<<'PHP'
        <?php

        enum Status implements HasColor {
            case DRAFT = 'draft';
            case PUBLISHED = 'published';
            case ARCHIVED = 'archived';
        }

        ----
        <?php

        namespace Humbug;

        enum Status implements \HasColor
        {
            case DRAFT = 'draft';
            case PUBLISHED = 'published';
            case ARCHIVED = 'archived';
        }

        PHP,

    'class with Enum name' => <<<'PHP'
        <?php

        class Enum {}

        ----
        <?php

        namespace Humbug;

        class Enum
        {
        }

        PHP,

    'backed enum' => <<<'PHP'
        <?php

        enum Status: string {
            case DRAFT = 'draft';
            case PUBLISHED = 'published';
            case ARCHIVED = 'archived';
        }

        ----
        <?php

        namespace Humbug;

        enum Status : string
        {
            case DRAFT = 'draft';
            case PUBLISHED = 'published';
            case ARCHIVED = 'archived';
        }

        PHP,

    'excluded enum (doesn\'t work)' => [
        'exclude-classes' => ['Status'],
        'payload' => <<<'PHP'
            <?php

            enum Status {
                case DRAFT;
                case PUBLISHED;
                case ARCHIVED;
            }

            ----
            <?php

            namespace Humbug;

            enum Status
            {
                case DRAFT;
                case PUBLISHED;
                case ARCHIVED;
            }

            PHP,
    ],

    'exposed enum (doesn\'t work)' => [
        'expose-classes' => ['Status'],
        'payload' => <<<'PHP'
            <?php

            enum Status {
                case DRAFT;
                case PUBLISHED;
                case ARCHIVED;
            }

            ----
            <?php

            namespace Humbug;

            enum Status
            {
                case DRAFT;
                case PUBLISHED;
                case ARCHIVED;
            }

            PHP,
    ],
];
