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

    'Declaration in the global namespace' => <<<'PHP'
    <?php
    
    enum Status {
        case Draft;
        case Published;
        case Archived;
    }
    
    interface Colorful {
        public function color(): string;
    }
    
    enum Suit implements Colorful {
        case Hearts;
        case Diamonds;
        case Clubs;
        case Spades;
    }
    
    ----
    <?php
    
    namespace Humbug;
    
    enum Status
    {
        case Draft;
        case Published;
        case Archived;
    }
    interface Colorful
    {
        public function color() : string;
    }
    enum Suit implements Colorful
    {
        case Hearts;
        case Diamonds;
        case Clubs;
        case Spades;
    }

    PHP,

    'Declaration of string enum in the global namespace' => <<<'PHP'
    <?php
    
    enum Suit: string {
        case Hearts = 'H';
        case Diamonds = 'D';
        case Clubs = 'C';
        case Spades = 'S';
    }
    
    interface Colorful {
        public function color(): string;
    }
    
    enum Suit: string implements Colorful {
        case Hearts = 'H';
        case Diamonds = 'D';
        case Clubs = 'C';
        case Spades = 'S';
    }
    
    ----
    <?php
    
    namespace Humbug;
    
    enum Suit : string
    {
        case Hearts = 'H';
        case Diamonds = 'D';
        case Clubs = 'C';
        case Spades = 'S';
    }
    interface Colorful
    {
        public function color() : string;
    }
    enum Suit : string implements Colorful
    {
        case Hearts = 'H';
        case Diamonds = 'D';
        case Clubs = 'C';
        case Spades = 'S';
    }
    
    PHP,

    'Declaration in the global namespace with global classes exposed' => [
        'expose-global-classes' => true,
        'payload' => <<<'PHP'
        <?php
        
        enum Status {
            case Draft;
            case Published;
            case Archived;
        }
        ----
        <?php
        
        namespace Humbug;
        
        enum Status
        {
            case Draft;
            case Published;
            case Archived;
        }
        
        PHP,
    ],

    'Declaration in a namespace' => <<<'PHP'
    <?php
    
    namespace Foo;
    
    enum Status {
        case Draft;
        case Published;
        case Archived;
    }
    ----
    <?php
    
    namespace Humbug\Foo;
    
    enum Status
    {
        case Draft;
        case Published;
        case Archived;
    }
    
    PHP,

    'Declaration of an "exposed" enum' => [
        'expose-classes' => ['Foo\Status'],
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo;
        
        enum Status {
            case Draft;
            case Published;
            case Archived;
        }
        ----
        <?php
        
        namespace Humbug\Foo;
        
        enum Status
        {
            case Draft;
            case Published;
            case Archived;
        }

        PHP,
    ],
];
