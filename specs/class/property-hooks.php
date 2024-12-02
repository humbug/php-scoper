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
use Humbug\PhpScoper\SpecFramework\Config\SpecWithConfig;

return [
    'meta' => new Meta(
        title: 'Class declaration with property hooks',
    ),

    'Declaration in the global namespace' => <<<'PHP'
        <?php
        
        // https://stitcher.io/blog/new-in-php-84#property-hooks-rfc

        class BookViewModel
        {
            public function __construct(
                private array $authors,
            ) {}
        
            public string $credits {
                get {
                    return implode(', ', array_map(
                        fn (Author $author) => $author->name, 
                        $this->authors,
                    ));
                }
            }
            
            public Author $mainAuthor {
                set (Author $mainAuthor) {
                    $this->authors[] = $mainAuthor;
                    $this->mainAuthor = $mainAuthor;
                }
                
                get => $this->mainAuthor;
            }
        }
        ----
        <?php

        namespace Humbug;

        // https://stitcher.io/blog/new-in-php-84#property-hooks-rfc
        class BookViewModel
        {
            public function __construct(private array $authors)
            {
            }
            public string $credits {
                get {
                    return \implode(', ', \array_map(fn(Author $author) => $author->name, $this->authors));
                }
            }
            public Author $mainAuthor {
                set(Author $mainAuthor) {
                    $this->authors[] = $mainAuthor;
                    $this->mainAuthor = $mainAuthor;
                }
                get => $this->mainAuthor;
            }
        }

        PHP,

    'Declaration in the global namespace with global classes exposed' => SpecWithConfig::create(
        exposeGlobalClasses: true,
        expectedRecordedClasses: [
            ['BookViewModel', 'Humbug\BookViewModel'],
        ],
        spec: <<<'PHP'
            <?php

            class BookViewModel
            {
                public function __construct(
                    private array $authors,
                ) {}
            
                public string $credits {
                    get {
                        return implode(', ', array_map(
                            fn (Author $author) => $author->name, 
                            $this->authors,
                        ));
                    }
                }
                
                public Author $mainAuthor {
                    set (Author $mainAuthor) {
                        $this->authors[] = $mainAuthor;
                        $this->mainAuthor = $mainAuthor;
                    }
                    
                    get => $this->mainAuthor;
                }
            }
            ----
            <?php

            namespace Humbug;

            class BookViewModel
            {
                public function __construct(private array $authors)
                {
                }
                public string $credits {
                    get {
                        return \implode(', ', \array_map(fn(Author $author) => $author->name, $this->authors));
                    }
                }
                public Author $mainAuthor {
                    set(Author $mainAuthor) {
                        $this->authors[] = $mainAuthor;
                        $this->mainAuthor = $mainAuthor;
                    }
                    get => $this->mainAuthor;
                }
            }
            \class_alias('Humbug\BookViewModel', 'BookViewModel', \false);

            PHP,
    ),

    'Declaration in a namespace' => <<<'PHP'
        <?php

        namespace Foo;

        class BookViewModel
        {
            public function __construct(
                private array $authors,
            ) {}
        
            public string $credits {
                get {
                    return implode(', ', array_map(
                        fn (Author $author) => $author->name, 
                        $this->authors,
                    ));
                }
            }
            
            public Author $mainAuthor {
                set (Author $mainAuthor) {
                    $this->authors[] = $mainAuthor;
                    $this->mainAuthor = $mainAuthor;
                }
                
                get => $this->mainAuthor;
            }
        }
        ----
        <?php

        namespace Humbug\Foo;

        class BookViewModel
        {
            public function __construct(private array $authors)
            {
            }
            public string $credits {
                get {
                    return implode(', ', array_map(fn(Author $author) => $author->name, $this->authors));
                }
            }
            public Author $mainAuthor {
                set(Author $mainAuthor) {
                    $this->authors[] = $mainAuthor;
                    $this->mainAuthor = $mainAuthor;
                }
                get => $this->mainAuthor;
            }
        }

        PHP,

    'Declaration in a namespace with global classes exposed' => SpecWithConfig::create(
        exposeGlobalClasses: true,
        spec: <<<'PHP'
            <?php

            namespace Foo;

            class BookViewModel
            {
                public function __construct(
                    private array $authors,
                ) {}
            
                public string $credits {
                    get {
                        return implode(', ', array_map(
                            fn (Author $author) => $author->name, 
                            $this->authors,
                        ));
                    }
                }
                
                public Author $mainAuthor {
                    set (Author $mainAuthor) {
                        $this->authors[] = $mainAuthor;
                        $this->mainAuthor = $mainAuthor;
                    }
                    
                    get => $this->mainAuthor;
                }
            }
            ----
            <?php

            namespace Humbug\Foo;

            class BookViewModel
            {
                public function __construct(private array $authors)
                {
                }
                public string $credits {
                    get {
                        return implode(', ', array_map(fn(Author $author) => $author->name, $this->authors));
                    }
                }
                public Author $mainAuthor {
                    set(Author $mainAuthor) {
                        $this->authors[] = $mainAuthor;
                        $this->mainAuthor = $mainAuthor;
                    }
                    get => $this->mainAuthor;
                }
            }

            PHP,
    ),

    'Declaration of a namespaced exposed class' => SpecWithConfig::create(
        exposeClasses: [
            'Foo\BookViewModel',
            'Foo\Author',
        ],
        expectedRecordedClasses: [
            ['Foo\BookViewModel', 'Humbug\Foo\BookViewModel'],
        ],
        spec: <<<'PHP'
            <?php

            namespace Foo;

            class BookViewModel
            {
                public function __construct(
                    private array $authors,
                ) {}
            
                public string $credits {
                    get {
                        return implode(', ', array_map(
                            fn (Author $author) => $author->name, 
                            $this->authors,
                        ));
                    }
                }
                
                public Author $mainAuthor {
                    set (Author $mainAuthor) {
                        $this->authors[] = $mainAuthor;
                        $this->mainAuthor = $mainAuthor;
                    }
                    
                    get => $this->mainAuthor;
                }
            }
            ----
            <?php

            namespace Humbug\Foo;

            class BookViewModel
            {
                public function __construct(private array $authors)
                {
                }
                public string $credits {
                    get {
                        return implode(', ', array_map(fn(Author $author) => $author->name, $this->authors));
                    }
                }
                public Author $mainAuthor {
                    set(Author $mainAuthor) {
                        $this->authors[] = $mainAuthor;
                        $this->mainAuthor = $mainAuthor;
                    }
                    get => $this->mainAuthor;
                }
            }
            \class_alias('Humbug\Foo\BookViewModel', 'Foo\BookViewModel', \false);

            PHP,
    ),

    'Declaration in a namespace with use statements' => <<<'PHP'
        <?php

        namespace Foo;

        use Bar\C;
        use DateTimeImmutable;

        class BookViewModel
        {
            public function __construct(
                private array $authors,
            ) {}
        
            public string $credits {
                get {
                    return implode(', ', array_map(
                        fn (Author $author) => $author->name, 
                        $this->authors,
                    ));
                }
            }
            
            public Author $mainAuthor {
                set (Author $mainAuthor) {
                    $this->authors[] = $mainAuthor;
                    $this->mainAuthor = $mainAuthor;
                }
                
                get => $this->mainAuthor;
            }
            
            public \Baz\Author $mainAuthor {
                set (\Baz\Author $mainAuthor) {
                    $this->authors[] = $mainAuthor;
                    $this->mainAuthor = $mainAuthor;
                }
                
                get => $this->mainAuthor;
            }
            
            public C $mainAuthor {
                set(C $mainAuthor) {
                    $this->authors[] = $mainAuthor;
                    $this->mainAuthor = $mainAuthor;
                }

                get => $this->mainAuthor;
            }
        }
        ----
        <?php

        namespace Humbug\Foo;

        use Humbug\Bar\C;
        use DateTimeImmutable;
        class BookViewModel
        {
            public function __construct(private array $authors)
            {
            }
            public string $credits {
                get {
                    return implode(', ', array_map(fn(Author $author) => $author->name, $this->authors));
                }
            }
            public Author $mainAuthor {
                set(Author $mainAuthor) {
                    $this->authors[] = $mainAuthor;
                    $this->mainAuthor = $mainAuthor;
                }
                get => $this->mainAuthor;
            }
            public \Humbug\Baz\Author $mainAuthor {
                set(\Humbug\Baz\Author $mainAuthor) {
                    $this->authors[] = $mainAuthor;
                    $this->mainAuthor = $mainAuthor;
                }
                get => $this->mainAuthor;
            }
            public C $mainAuthor {
                set(C $mainAuthor) {
                    $this->authors[] = $mainAuthor;
                    $this->mainAuthor = $mainAuthor;
                }
                get => $this->mainAuthor;
            }
        }

        PHP,
];
