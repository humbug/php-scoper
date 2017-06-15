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

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\Scoper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use function Humbug\PhpScoper\escape_path;
use function Humbug\PhpScoper\make_tmp_dir;
use function Humbug\PhpScoper\remove_dir;

/**
 * @covers \Humbug\PhpScoper\Scoper\ComposerScoper
 */
class ComposerScoperTest extends TestCase
{
    /**
     * @var string
     */
    private $cwd;

    /**
     * @var string
     */
    private $tmp;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        if (null == $this->tmp) {
            $this->cwd = getcwd();
            $this->tmp = make_tmp_dir('scoper', __CLASS__);
        }

        chdir($this->tmp);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        chdir($this->cwd);

        remove_dir($this->tmp);
    }

    public function test_it_is_a_Scoper()
    {
        $this->assertTrue(is_a(ComposerScoper::class, Scoper::class, true));
    }

    public function test_delegates_scoping_to_the_decorated_scoper_if_is_not_a_composer_file()
    {
        $filePath = escape_path($this->tmp.'/file.php');
        $prefix = 'Humbug';

        touch($filePath);
        file_put_contents($filePath, '');

        /** @var Scoper|ObjectProphecy $decoratedScoperProphecy */
        $decoratedScoperProphecy = $this->prophesize(Scoper::class);
        $decoratedScoperProphecy
            ->scope($filePath, $prefix)
            ->willReturn(
                $expected = 'Scoped content'
            )
        ;
        /** @var Scoper $decoratedScoper */
        $decoratedScoper = $decoratedScoperProphecy->reveal();

        $scoper = new ComposerScoper($decoratedScoper);

        $actual = $scoper->scope($filePath, $prefix);

        $this->assertSame($expected, $actual);

        $decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    /**
     * @dataProvider provideComposerFiles
     */
    public function test_it_prefixes_the_composer_autoloader(string $fileContent, string $expected)
    {
        touch($filePath = escape_path($this->tmp.'/composer.json'));
        file_put_contents($filePath, $fileContent);

        $scoper = new ComposerScoper(new FakeScoper());

        $actual = $scoper->scope($filePath, 'Foo');

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideComposerLockFiles
     */
    public function test_it_prefixes_the_composer_dependencies_autoloader(string $fileContent, string $expected)
    {
        touch($filePath = escape_path($this->tmp.'/composer.lock'));
        file_put_contents($filePath, $fileContent);

        $scoper = new ComposerScoper(new FakeScoper());

        $actual = $scoper->scope($filePath, 'Foo');

        $this->assertSame($expected, $actual);
    }

    public function provideComposerFiles()
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
        ]
    },
    "autoload-dev": {
        "psr-4": {
            "Humbug\\PhpScoper\\": "tests/"
        },
        "files": [
            "tests/functions.php"
        ]
    }
}

JSON
            ,
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
        ]
    },
    "autoload-dev": {
        "psr-4": {
            "Foo\\Humbug\\PhpScoper\\": "tests\/"
        },
        "files": [
            "tests\/functions.php"
        ]
    }
}
JSON
        ];
    }

    public function provideComposerLockFiles()
    {
        yield [
            <<<'JSON'
{
    "_readme": [
        "This file locks the dependencies of your project to a known state",
        "Read more about it at https://getcomposer.org/doc/01-basic-usage.md#composer-lock-the-lock-file",
        "This file is @generated automatically"
    ],
    "content-hash": "86085ec14509d593461f6364ebca9e2b",
    "packages": [
        {
            "name": "beberlei/assert",
            "version": "v2.7.6",
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
            "type": "library",
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
            "keywords": [
                "assert",
                "assertion",
                "validation"
            ],
            "time": "2017-05-04T02:00:24+00:00"
        }
    ],
    "packages-dev": [
        {
            "name": "composer/ca-bundle",
            "version": "1.0.7",
            "source": {
                "type": "git",
                "url": "https://github.com/composer/ca-bundle.git",
                "reference": "b17e6153cb7f33c7e44eb59578dc12eee5dc8e12"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/composer/ca-bundle/zipball/b17e6153cb7f33c7e44eb59578dc12eee5dc8e12",
                "reference": "b17e6153cb7f33c7e44eb59578dc12eee5dc8e12",
                "shasum": ""
            },
            "require": {
                "ext-openssl": "*",
                "ext-pcre": "*",
                "php": "^5.3.2 || ^7.0"
            },
            "require-dev": {
                "phpunit/phpunit": "^4.5",
                "psr/log": "^1.0",
                "symfony/process": "^2.5 || ^3.0"
            },
            "suggest": {
                "symfony/process": "This is necessary to reliably check whether openssl_x509_parse is vulnerable on older php versions, but can be ignored on PHP 5.5.6+"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "1.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Composer\\CaBundle\\": "src"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Jordi Boggiano",
                    "email": "j.boggiano@seld.be",
                    "homepage": "http://seld.be"
                }
            ],
            "description": "Lets you find a path to the system CA bundle, and includes a fallback to the Mozilla CA bundle.",
            "keywords": [
                "cabundle",
                "cacert",
                "certificate",
                "ssl",
                "tls"
            ],
            "time": "2017-03-06T11:59:08+00:00"
        },
        {
            "name": "padraic/humbug_get_contents",
            "version": "1.1.0",
            "source": {
                "type": "git",
                "url": "https://github.com/humbug/file_get_contents.git",
                "reference": "279ff67be5b3bd0229326a917c3e262c644b98d6"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/humbug/file_get_contents/zipball/279ff67be5b3bd0229326a917c3e262c644b98d6",
                "reference": "279ff67be5b3bd0229326a917c3e262c644b98d6",
                "shasum": ""
            },
            "require": {
                "composer/ca-bundle": "^1.0",
                "ext-openssl": "*",
                "php": "^5.3 || ^7.0"
            },
            "require-dev": {
                "bamarni/composer-bin-plugin": "^1.1",
                "phpunit/phpunit": "^4.0 || ^5.0 || ^6.0"
            },
            "type": "library",
            "extra": {
                "bamarni-bin": {
                    "bin-links": false
                },
                "branch-alias": {
                    "dev-master": "2.0-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Humbug\\": "src/"
                },
                "files": [
                    "src/function.php",
                    "src/functions.php"
                ]
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "BSD-3-Clause"
            ],
            "authors": [
                {
                    "name": "Pádraic Brady",
                    "email": "padraic.brady@gmail.com",
                    "homepage": "http://blog.astrumfutura.com"
                }
            ],
            "description": "Secure wrapper for accessing HTTPS resources with file_get_contents for PHP 5.3+",
            "homepage": "https://github.com/padraic/file_get_contents",
            "keywords": [
                "download",
                "file_get_contents",
                "http",
                "https",
                "ssl",
                "tls"
            ],
            "time": "2017-06-02T10:18:25+00:00"
        }
    ],
    "aliases": [],
    "minimum-stability": "stable",
    "stability-flags": [],
    "prefer-stable": false,
    "prefer-lowest": false,
    "platform": [],
    "platform-dev": []
}


JSON
            ,
            <<<'JSON'
{
    "_readme": [
        "This file locks the dependencies of your project to a known state",
        "Read more about it at https:\/\/getcomposer.org\/doc\/01-basic-usage.md#composer-lock-the-lock-file",
        "This file is @generated automatically"
    ],
    "content-hash": "86085ec14509d593461f6364ebca9e2b",
    "packages": [
        {
            "name": "beberlei\/assert",
            "version": "v2.7.6",
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
            "type": "library",
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
            "keywords": [
                "assert",
                "assertion",
                "validation"
            ],
            "time": "2017-05-04T02:00:24+00:00"
        }
    ],
    "packages-dev": [
        {
            "name": "composer\/ca-bundle",
            "version": "1.0.7",
            "source": {
                "type": "git",
                "url": "https:\/\/github.com\/composer\/ca-bundle.git",
                "reference": "b17e6153cb7f33c7e44eb59578dc12eee5dc8e12"
            },
            "dist": {
                "type": "zip",
                "url": "https:\/\/api.github.com\/repos\/composer\/ca-bundle\/zipball\/b17e6153cb7f33c7e44eb59578dc12eee5dc8e12",
                "reference": "b17e6153cb7f33c7e44eb59578dc12eee5dc8e12",
                "shasum": ""
            },
            "require": {
                "ext-openssl": "*",
                "ext-pcre": "*",
                "php": "^5.3.2 || ^7.0"
            },
            "require-dev": {
                "phpunit\/phpunit": "^4.5",
                "psr\/log": "^1.0",
                "symfony\/process": "^2.5 || ^3.0"
            },
            "suggest": {
                "symfony\/process": "This is necessary to reliably check whether openssl_x509_parse is vulnerable on older php versions, but can be ignored on PHP 5.5.6+"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "1.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Foo\\Composer\\CaBundle\\": "src"
                }
            },
            "notification-url": "https:\/\/packagist.org\/downloads\/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Jordi Boggiano",
                    "email": "j.boggiano@seld.be",
                    "homepage": "http:\/\/seld.be"
                }
            ],
            "description": "Lets you find a path to the system CA bundle, and includes a fallback to the Mozilla CA bundle.",
            "keywords": [
                "cabundle",
                "cacert",
                "certificate",
                "ssl",
                "tls"
            ],
            "time": "2017-03-06T11:59:08+00:00"
        },
        {
            "name": "padraic\/humbug_get_contents",
            "version": "1.1.0",
            "source": {
                "type": "git",
                "url": "https:\/\/github.com\/humbug\/file_get_contents.git",
                "reference": "279ff67be5b3bd0229326a917c3e262c644b98d6"
            },
            "dist": {
                "type": "zip",
                "url": "https:\/\/api.github.com\/repos\/humbug\/file_get_contents\/zipball\/279ff67be5b3bd0229326a917c3e262c644b98d6",
                "reference": "279ff67be5b3bd0229326a917c3e262c644b98d6",
                "shasum": ""
            },
            "require": {
                "composer\/ca-bundle": "^1.0",
                "ext-openssl": "*",
                "php": "^5.3 || ^7.0"
            },
            "require-dev": {
                "bamarni\/composer-bin-plugin": "^1.1",
                "phpunit\/phpunit": "^4.0 || ^5.0 || ^6.0"
            },
            "type": "library",
            "extra": {
                "bamarni-bin": {
                    "bin-links": false
                },
                "branch-alias": {
                    "dev-master": "2.0-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Foo\\Humbug\\": "src\/"
                },
                "files": [
                    "src\/function.php",
                    "src\/functions.php"
                ]
            },
            "notification-url": "https:\/\/packagist.org\/downloads\/",
            "license": [
                "BSD-3-Clause"
            ],
            "authors": [
                {
                    "name": "P\u00e1draic Brady",
                    "email": "padraic.brady@gmail.com",
                    "homepage": "http:\/\/blog.astrumfutura.com"
                }
            ],
            "description": "Secure wrapper for accessing HTTPS resources with file_get_contents for PHP 5.3+",
            "homepage": "https:\/\/github.com\/padraic\/file_get_contents",
            "keywords": [
                "download",
                "file_get_contents",
                "http",
                "https",
                "ssl",
                "tls"
            ],
            "time": "2017-06-02T10:18:25+00:00"
        }
    ],
    "aliases": [],
    "minimum-stability": "stable",
    "stability-flags": [],
    "prefer-stable": false,
    "prefer-lowest": false,
    "platform": [],
    "platform-dev": []
}
JSON
        ];
    }
}
