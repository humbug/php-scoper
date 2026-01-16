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

namespace Humbug\PhpScoper\Scoper\Composer;

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Scoper\Scoper;
use Humbug\PhpScoper\Scoper\ScoperStub;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\Reflector;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function is_a;

/**
 * @internal
 */
#[CoversClass(AutoloadPrefixer::class)]
#[CoversClass(JsonFileScoper::class)]
class JsonFileScoperTest extends TestCase
{
    private const PREFIX = 'Foo';

    private ScoperStub $decoratedScoper;

    private Scoper $scoper;

    protected function setUp(): void
    {
        $autoloadPrefixer = new AutoloadPrefixer(
            self::PREFIX,
            new EnrichedReflector(
                Reflector::createEmpty(),
                SymbolsConfiguration::create(),
            ),
        );

        $this->decoratedScoper = new ScoperStub();

        $this->scoper = new JsonFileScoper(
            $this->decoratedScoper,
            $autoloadPrefixer,
        );
    }

    public function test_it_is_a_scoper(): void
    {
        self::assertTrue(is_a(JsonFileScoper::class, Scoper::class, true));
    }

    public function test_delegates_scoping_to_the_decorated_scoper_if_is_not_a_composer_file(): void
    {
        $filePath = 'file.php';
        $fileContents = '';

        $this->decoratedScoper->addConfig(
            $filePath,
            $fileContents,
            $expected = 'Scoped content',
        );

        $actual = $this->scoper->scope($filePath, $fileContents);

        self::assertSame($expected, $actual);
    }

    #[DataProvider('provideComposerFiles')]
    public function test_it_prefixes_the_composer_autoloaders(string $fileContents, string $expected): void
    {
        $actual = $this->scoper->scope(
            'composer.json',
            $fileContents,
        );

        self::assertSame($expected, $actual);
    }

    public static function provideComposerFiles(): iterable
    {
        yield [
            <<<'JSON'
                {
                    "bin": ["bin/php-scoper"],
                    "autoload": {
                        "psr-4": {
                            "Humbug\\PhpScoper\\": "src/"
                        },
                        "files": [
                            "src/functions.php"
                        ],
                        "classmap": []
                    },
                    "autoload-dev": {
                        "psr-4": {
                            "Humbug\\PhpScoper\\": "tests/"
                        },
                        "files": [
                            "tests/functions.php"
                        ]
                    },
                    "config": {}
                }

                JSON,
            <<<'JSON'
                {
                    "bin": [
                        "bin\/php-scoper"
                    ],
                    "autoload": {
                        "psr-4": {
                            "Foo\\Humbug\\PhpScoper\\": "src\/"
                        },
                        "files": [
                            "src\/functions.php"
                        ],
                        "classmap": []
                    },
                    "autoload-dev": {
                        "psr-4": {
                            "Foo\\Humbug\\PhpScoper\\": "tests\/"
                        },
                        "files": [
                            "tests\/functions.php"
                        ]
                    },
                    "config": {}
                }
                JSON,
        ];
    }

    public function test_it_requires_valid_composer2_files(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected the decoded JSON to be an stdClass instance, got "array" instead');

        $this->scoper->scope('composer.json', '[]');
    }

    #[DataProvider('providePSR0ComposerFiles')]
    public function test_it_prefixes_psr0_autoloaders(string $fileContents, string $expected): void
    {
        $actual = $this->scoper->scope(
            'composer.json',
            $fileContents,
        );

        self::assertSame($expected, $actual);
    }

    public static function providePSR0ComposerFiles(): iterable
    {
        yield [
            <<<'JSON'
                {
                    "bin": ["bin/php-scoper"],
                    "autoload": {
                        "psr-0": {
                            "Humbug\\PhpScoper\\": "src/"
                        },
                        "psr-4": {
                            "BarFoo\\": [
                                "lib/",
                                "dev/"
                            ]
                        },
                        "files": [
                            "src/functions.php"
                        ]
                    },
                    "autoload-dev": {
                        "psr-0": {
                            "Humbug\\PhpScoper\\": "tests/"
                        },
                        "psr-4": {
                            "Bar\\": "folder\/"
                        },
                        "files": [
                            "tests/functions.php"
                        ]
                    }
                }

                JSON,
            <<<'JSON'
                {
                    "bin": [
                        "bin\/php-scoper"
                    ],
                    "autoload": {
                        "psr-4": {
                            "Foo\\BarFoo\\": [
                                "lib\/",
                                "dev\/"
                            ],
                            "Foo\\Humbug\\PhpScoper\\": "src\/Humbug\/PhpScoper\/"
                        },
                        "files": [
                            "src\/functions.php"
                        ]
                    },
                    "autoload-dev": {
                        "psr-4": {
                            "Foo\\Bar\\": "folder\/",
                            "Foo\\Humbug\\PhpScoper\\": "tests\/Humbug\/PhpScoper\/"
                        },
                        "files": [
                            "tests\/functions.php"
                        ]
                    }
                }
                JSON,
        ];

        yield 'PSR-0 and four with the same namespace get merged' => [
            <<<'JSON'
                {
                    "autoload": {
                        "psr-0": {
                            "Bar\\": "src/"
                        },
                        "psr-4": {
                            "Bar\\": "lib/"
                        }
                     }
                }
                JSON,
            <<<'JSON'
                {
                    "autoload": {
                        "psr-4": {
                            "Foo\\Bar\\": [
                                "lib\/",
                                "src\/Bar\/"
                            ]
                        }
                    }
                }
                JSON,
        ];

        yield 'PSR-0 and four get merged if either of them have multiple entries' => [
            <<<'JSON'
                {
                    "autoload": {
                        "psr-4": {
                            "Bar\\": [
                                "lib/",
                                "src/"
                            ]
                        },
                        "psr-0": {
                            "Bar\\": "test"
                        }
                    },
                    "autoload-dev": {
                        "psr-0": {
                            "Baz\\": [
                                "folder/",
                                "check/"
                            ]
                        },
                        "psr-4": {
                            "Baz\\": "loader/"
                        }
                    }
                }
                JSON,
            <<<'JSON'
                {
                    "autoload": {
                        "psr-4": {
                            "Foo\\Bar\\": [
                                "lib\/",
                                "src\/",
                                "test\/Bar\/"
                            ]
                        }
                    },
                    "autoload-dev": {
                        "psr-4": {
                            "Foo\\Baz\\": [
                                "folder\/Baz\/",
                                "check\/Baz\/",
                                "loader\/"
                            ]
                        }
                    }
                }
                JSON,
        ];

        yield 'PSR-0 gets converted to PSR-4' => [
            <<<'JSON'
                {
                    "autoload": {
                        "psr-0": {
                            "Bar\\": "src/"
                        }
                    }
                }
                JSON,
            <<<'JSON'
                {
                    "autoload": {
                        "psr-4": {
                            "Foo\\Bar\\": "src\/Bar\/"
                        }
                    }
                }
                JSON,
        ];

        yield 'PSR-0 and four get merged when both are arrays' => [
            <<<'JSON'
                {
                    "autoload": {
                        "psr-4": {
                            "Bar\\": [
                                "lib/",
                                "src/"
                            ]
                        },
                        "psr-0": {
                            "Bar": [
                                "build",
                                "internal/"
                            ]
                        }
                    }
                }
                JSON,
            <<<'JSON'
                {
                    "autoload": {
                        "psr-4": {
                            "Foo\\Bar\\": [
                                "lib\/",
                                "src\/",
                                "build\/Bar\/",
                                "internal\/Bar\/"
                            ]
                        }
                    }
                }
                JSON,
        ];

        yield 'PSR-0 with underscores gets converted to classmap' => [
            <<<'JSON'
                {
                    "autoload": {
                        "psr-0": {
                            "EasyRdf_": "lib"
                        }
                    }
                }

                JSON,
            <<<'JSON'
                {
                    "autoload": {
                        "classmap": [
                            "lib"
                        ]
                    }
                }
                JSON,
        ];

        yield [
            <<<'JSON'
                {
                    "autoload": {
                        "psr-0": {
                            "EasyRdf_": "lib/"
                        }
                    }
                }

                JSON,
            <<<'JSON'
                {
                    "autoload": {
                        "classmap": [
                            "lib\/"
                        ]
                    }
                }
                JSON,
        ];

        yield [
            <<<'JSON'
                {
                    "autoload": {
                        "classmap": ["src"]
                    }
                }

                JSON,
            <<<'JSON'
                {
                    "autoload": {
                        "classmap": [
                            "src"
                        ]
                    }
                }
                JSON,
        ];
    }
}
