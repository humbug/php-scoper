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
use Humbug\PhpScoper\Symbol\NamespaceRegistry;
use Humbug\PhpScoper\Symbol\Reflector;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use function is_a;

/**
 * @covers \Humbug\PhpScoper\Scoper\Composer\AutoloadPrefixer
 * @covers \Humbug\PhpScoper\Scoper\Composer\InstalledPackagesScoper
 *
 * @internal
 */
class InstalledPackagesScoperTest extends TestCase
{
    private const PREFIX = 'Foo';

    private AutoloadPrefixer $autoloadPrefixer;

    private ScoperStub $decoratedScoper;

    private Scoper $scoper;

    protected function setUp(): void
    {
        $this->autoloadPrefixer = new AutoloadPrefixer(
            self::PREFIX,
            new EnrichedReflector(
                Reflector::createEmpty(),
                SymbolsConfiguration::create(),
            ),
        );

        $this->decoratedScoper = new ScoperStub();

        $this->scoper = new InstalledPackagesScoper(
            $this->decoratedScoper,
            $this->autoloadPrefixer,
        );
    }

    public function test_it_is_a_scoper(): void
    {
        self::assertTrue(is_a(InstalledPackagesScoper::class, Scoper::class, true));
    }

    public function test_delegates_scoping_to_the_decorated_scoper_if_is_not_a_installed_file(): void
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

    /**
     * @dataProvider provideInstalledPackagesFiles
     */
    public function test_it_prefixes_the_composer_autoloaders(
        EnrichedReflector $enrichedReflector,
        string $fileContents,
        string $expected
    ): void {
        $scoper = new InstalledPackagesScoper(
            $this->decoratedScoper,
            new AutoloadPrefixer(
                self::PREFIX,
                $enrichedReflector,
            ),
        );

        $actual = $scoper->scope(
            'composer/installed.json',
            $fileContents,
        );

        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideInvalidComposerFiles
     */
    public function test_it_requires_valid_composer2_files(
        string $contents,
        string $expectedExceptionMessage
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->scoper->scope('composer/installed.json', $contents);
    }

    public static function provideInstalledPackagesFiles(): iterable
    {
        $emptyEnrichedReflector = new EnrichedReflector(
            Reflector::createEmpty(),
            SymbolsConfiguration::create(),
        );

        yield 'fideloper/proxy excerpt' => [
            $emptyEnrichedReflector,
            <<<'JSON'
                {
                    "packages": [
                        {
                            "name": "fideloper\/proxy",
                            "version": "4.0.0",
                            "version_normalized": "4.0.0.0",
                            "source": {
                                "type": "git",
                                "url": "https:\/\/github.com\/fideloper\/TrustedProxy.git",
                                "reference": "cf8a0ca4b85659b9557e206c90110a6a4dba980a"
                            },
                            "dist": {
                                "type": "zip",
                                "url": "https:\/\/api.github.com\/repos\/fideloper\/TrustedProxy\/zipball\/cf8a0ca4b85659b9557e206c90110a6a4dba980a",
                                "reference": "cf8a0ca4b85659b9557e206c90110a6a4dba980a",
                                "shasum": ""
                            },
                            "require": {
                                "illuminate\/contracts": "~5.0",
                                "php": ">=5.4.0"
                            },
                            "require-dev": {
                                "illuminate\/http": "~5.6",
                                "mockery\/mockery": "~1.0",
                                "phpunit\/phpunit": "^6.0"
                            },
                            "time": "2018-02-07T20:20:57+00:00",
                            "type": "library",
                            "extra": {
                                "laravel": {
                                    "providers": [
                                        "Fideloper\\Proxy\\TrustedProxyServiceProvider"
                                    ]
                                }
                            },
                            "installation-source": "dist",
                            "autoload": {
                                "psr-4": {
                                    "Fideloper\\Proxy\\": "src\/"
                                }
                            },
                            "notification-url": "https:\/\/packagist.org\/downloads\/",
                            "license": [
                                "MIT"
                            ],
                            "authors": [
                                {
                                    "name": "Chris Fidao",
                                    "email": "fideloper@gmail.com"
                                }
                            ],
                            "description": "Set trusted proxies for Laravel",
                            "keywords": [
                                "load balancing",
                                "proxy",
                                "trusted proxy"
                            ]
                        }
                    ]
                }
                JSON,
            <<<'JSON'
                {
                    "packages": [
                        {
                            "name": "scoped-fideloper\/proxy",
                            "version": "4.0.0",
                            "version_normalized": "4.0.0.0",
                            "source": {
                                "type": "git",
                                "url": "https:\/\/github.com\/fideloper\/TrustedProxy.git",
                                "reference": "cf8a0ca4b85659b9557e206c90110a6a4dba980a"
                            },
                            "dist": {
                                "type": "zip",
                                "url": "https:\/\/api.github.com\/repos\/fideloper\/TrustedProxy\/zipball\/cf8a0ca4b85659b9557e206c90110a6a4dba980a",
                                "reference": "cf8a0ca4b85659b9557e206c90110a6a4dba980a",
                                "shasum": ""
                            },
                            "require": {
                                "illuminate\/contracts": "~5.0",
                                "php": ">=5.4.0"
                            },
                            "require-dev": {
                                "illuminate\/http": "~5.6",
                                "mockery\/mockery": "~1.0",
                                "phpunit\/phpunit": "^6.0"
                            },
                            "time": "2018-02-07T20:20:57+00:00",
                            "type": "library",
                            "extra": {
                                "laravel": {
                                    "providers": [
                                        "Foo\\Fideloper\\Proxy\\TrustedProxyServiceProvider"
                                    ]
                                }
                            },
                            "installation-source": "dist",
                            "autoload": {
                                "psr-4": {
                                    "Foo\\Fideloper\\Proxy\\": "src\/"
                                }
                            },
                            "notification-url": "https:\/\/packagist.org\/downloads\/",
                            "license": [
                                "MIT"
                            ],
                            "authors": [
                                {
                                    "name": "Chris Fidao",
                                    "email": "fideloper@gmail.com"
                                }
                            ],
                            "description": "Set trusted proxies for Laravel",
                            "keywords": [
                                "load balancing",
                                "proxy",
                                "trusted proxy"
                            ]
                        }
                    ]
                }
                JSON,
        ];

        yield 'beberlei/assert excerpt' => [
            $emptyEnrichedReflector,
            <<<'JSON'
                {
                    "dev": true,
                    "packages": [
                        {
                            "name": "beberlei/assert",
                            "version": "v2.7.6",
                            "version_normalized": "2.7.6.0",
                            "source": {
                                "type": "git",
                                "url": "https://github.com/beberlei/assert.git",
                                "reference": "8726e183ebbb0169cb6cb4832e22ebd355524563"
                            },
                            "dist": {
                                "type": "zip",
                                "url": "https://api.github.com/repos/beberlei/assert/zipball/8726e183ebbb0169cb6cb4832e22ebd355524563",
                                "reference": "8726e183ebbb0169cb6cb4832e22ebd355524563",
                                "shasum": ""
                            },
                            "require": {
                                "ext-mbstring": "*",
                                "php": ">=5.3"
                            },
                            "require-dev": {
                                "friendsofphp/php-cs-fixer": "^2.1.1",
                                "phpunit/phpunit": "^4|^5"
                            },
                            "time": "2017-05-04T02:00:24+00:00",
                            "type": "library",
                            "installation-source": "dist",
                            "autoload": {
                                "psr-4": {
                                    "Assert\\": "lib/Assert"
                                },
                                "files": [
                                    "lib/Assert/functions.php"
                                ]
                            },
                            "notification-url": "https://packagist.org/downloads/",
                            "license": [
                                "BSD-2-Clause"
                            ],
                            "authors": [
                                {
                                    "name": "Benjamin Eberlei",
                                    "email": "kontakt@beberlei.de",
                                    "role": "Lead Developer"
                                },
                                {
                                    "name": "Richard Quadling",
                                    "email": "rquadling@gmail.com",
                                    "role": "Collaborator"
                                }
                            ],
                            "description": "Thin assertion library for input validation in business models.",
                            "keywords": [],
                            "platform": {}
                        }
                    ]
                }

                JSON,
            <<<'JSON'
                {
                    "dev": true,
                    "packages": [
                        {
                            "name": "scoped-beberlei\/assert",
                            "version": "v2.7.6",
                            "version_normalized": "2.7.6.0",
                            "source": {
                                "type": "git",
                                "url": "https:\/\/github.com\/beberlei\/assert.git",
                                "reference": "8726e183ebbb0169cb6cb4832e22ebd355524563"
                            },
                            "dist": {
                                "type": "zip",
                                "url": "https:\/\/api.github.com\/repos\/beberlei\/assert\/zipball\/8726e183ebbb0169cb6cb4832e22ebd355524563",
                                "reference": "8726e183ebbb0169cb6cb4832e22ebd355524563",
                                "shasum": ""
                            },
                            "require": {
                                "ext-mbstring": "*",
                                "php": ">=5.3"
                            },
                            "require-dev": {
                                "friendsofphp\/php-cs-fixer": "^2.1.1",
                                "phpunit\/phpunit": "^4|^5"
                            },
                            "time": "2017-05-04T02:00:24+00:00",
                            "type": "library",
                            "installation-source": "dist",
                            "autoload": {
                                "psr-4": {
                                    "Foo\\Assert\\": "lib\/Assert"
                                },
                                "files": [
                                    "lib\/Assert\/functions.php"
                                ]
                            },
                            "notification-url": "https:\/\/packagist.org\/downloads\/",
                            "license": [
                                "BSD-2-Clause"
                            ],
                            "authors": [
                                {
                                    "name": "Benjamin Eberlei",
                                    "email": "kontakt@beberlei.de",
                                    "role": "Lead Developer"
                                },
                                {
                                    "name": "Richard Quadling",
                                    "email": "rquadling@gmail.com",
                                    "role": "Collaborator"
                                }
                            ],
                            "description": "Thin assertion library for input validation in business models.",
                            "keywords": [],
                            "platform": {}
                        }
                    ]
                }
                JSON,
        ];

        yield 'fideloper/proxy excerpt with excluded provider' => [
            new EnrichedReflector(
                Reflector::createEmpty(),
                SymbolsConfiguration::create(
                    excludedNamespaces: NamespaceRegistry::create(['Fideloper']),
                ),
            ),
            <<<'JSON'
                {
                    "packages": [
                        {
                            "name": "fideloper\/proxy",
                            "version": "4.0.0",
                            "version_normalized": "4.0.0.0",
                            "source": {
                                "type": "git",
                                "url": "https:\/\/github.com\/fideloper\/TrustedProxy.git",
                                "reference": "cf8a0ca4b85659b9557e206c90110a6a4dba980a"
                            },
                            "dist": {
                                "type": "zip",
                                "url": "https:\/\/api.github.com\/repos\/fideloper\/TrustedProxy\/zipball\/cf8a0ca4b85659b9557e206c90110a6a4dba980a",
                                "reference": "cf8a0ca4b85659b9557e206c90110a6a4dba980a",
                                "shasum": ""
                            },
                            "require": {
                                "illuminate\/contracts": "~5.0",
                                "php": ">=5.4.0"
                            },
                            "require-dev": {
                                "illuminate\/http": "~5.6",
                                "mockery\/mockery": "~1.0",
                                "phpunit\/phpunit": "^6.0"
                            },
                            "time": "2018-02-07T20:20:57+00:00",
                            "type": "library",
                            "extra": {
                                "laravel": {
                                    "providers": [
                                        "Fideloper\\Proxy\\TrustedProxyServiceProvider"
                                    ]
                                }
                            },
                            "installation-source": "dist",
                            "autoload": {
                                "psr-4": {
                                    "Fideloper\\Proxy\\": "src\/"
                                }
                            },
                            "notification-url": "https:\/\/packagist.org\/downloads\/",
                            "license": [
                                "MIT"
                            ],
                            "authors": [
                                {
                                    "name": "Chris Fidao",
                                    "email": "fideloper@gmail.com"
                                }
                            ],
                            "description": "Set trusted proxies for Laravel",
                            "keywords": [
                                "load balancing",
                                "proxy",
                                "trusted proxy"
                            ]
                        }
                    ]
                }
                JSON,
            <<<'JSON'
                {
                    "packages": [
                        {
                            "name": "scoped-fideloper\/proxy",
                            "version": "4.0.0",
                            "version_normalized": "4.0.0.0",
                            "source": {
                                "type": "git",
                                "url": "https:\/\/github.com\/fideloper\/TrustedProxy.git",
                                "reference": "cf8a0ca4b85659b9557e206c90110a6a4dba980a"
                            },
                            "dist": {
                                "type": "zip",
                                "url": "https:\/\/api.github.com\/repos\/fideloper\/TrustedProxy\/zipball\/cf8a0ca4b85659b9557e206c90110a6a4dba980a",
                                "reference": "cf8a0ca4b85659b9557e206c90110a6a4dba980a",
                                "shasum": ""
                            },
                            "require": {
                                "illuminate\/contracts": "~5.0",
                                "php": ">=5.4.0"
                            },
                            "require-dev": {
                                "illuminate\/http": "~5.6",
                                "mockery\/mockery": "~1.0",
                                "phpunit\/phpunit": "^6.0"
                            },
                            "time": "2018-02-07T20:20:57+00:00",
                            "type": "library",
                            "extra": {
                                "laravel": {
                                    "providers": [
                                        "Fideloper\\Proxy\\TrustedProxyServiceProvider"
                                    ]
                                }
                            },
                            "installation-source": "dist",
                            "autoload": {
                                "psr-4": {
                                    "Fideloper\\Proxy\\": "src\/"
                                }
                            },
                            "notification-url": "https:\/\/packagist.org\/downloads\/",
                            "license": [
                                "MIT"
                            ],
                            "authors": [
                                {
                                    "name": "Chris Fidao",
                                    "email": "fideloper@gmail.com"
                                }
                            ],
                            "description": "Set trusted proxies for Laravel",
                            "keywords": [
                                "load balancing",
                                "proxy",
                                "trusted proxy"
                            ]
                        }
                    ]
                }
                JSON,
        ];

        // TODO:add composer plugin case
    }

    public static function provideInvalidComposerFiles(): iterable
    {
        yield 'no packages entry' => [
            <<<'JSON'
                {}
                JSON,
            'Expected the decoded JSON to contain the list of installed packages',
        ];

        yield 'packages entry is not an array' => [
            <<<'JSON'
                {
                    "packages": "Foo"
                }
                JSON,
            'Expected the decoded JSON to contain the list of installed packages',
        ];

        yield 'invalid installed.json' => [
            <<<'JSON'
                []
                JSON,
            'Expected the decoded JSON to be an stdClass instance, got "array" instead',
        ];
    }
}
