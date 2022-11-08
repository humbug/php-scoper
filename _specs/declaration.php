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

    'minimal enum declaration' => <<<'PHP'
    <?php
    
    enum Status {
        DRAFT,
        PUBLISHED,
        ARCHIVED;
    }
    
    ----
    <?php
    
    namespace Humbug;

    enum PostStatus {
        DRAFT,
        PUBLISHED,
        ARCHIVED;
    }  
    
    PHP,

    'enum with methods' => <<<'PHP'
    <?php
    
    enum Status {
        DRAFT,
        PUBLISHED,
        ARCHIVED;
        
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

    enum PostStatus {
        DRAFT,
        PUBLISHED,
        ARCHIVED;
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
];
