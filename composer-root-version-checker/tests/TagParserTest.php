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

namespace Humbug\PhpScoperComposerRootChecker\Tests;

use Humbug\PhpScoperComposerRootChecker\TagParser;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \Humbug\PhpScoperComposerRootChecker\TagParser
 *
 * @internal
 */
final class TagParserTest extends TestCase
{
    /**
     * @dataProvider githubResponseContentProvider
     */
    public function test_it_can_parse_the_tag_from_the_github_response_content(
        string $responseContent,
        string $expected
    ): void {
        $actual = TagParser::parse($responseContent);

        self::assertSame($expected, $actual);
    }

    public static function githubResponseContentProvider(): iterable
    {
        yield 'unstable version' => [
            <<<'JSON'
                [
                  {
                    "name": "0.17.6",
                    "zipball_url": "https://api.github.com/repos/humbug/php-scoper/zipball/refs/tags/0.17.6",
                    "tarball_url": "https://api.github.com/repos/humbug/php-scoper/tarball/refs/tags/0.17.6",
                    "commit": {
                      "sha": "b528b87bdd8500cc243788971b6cc3e061c78e3f",
                      "url": "https://api.github.com/repos/humbug/php-scoper/commits/b528b87bdd8500cc243788971b6cc3e061c78e3f"
                    },
                    "node_id": "MDM6UmVmNDQzODQ0NDc6cmVmcy90YWdzLzAuMTcuNg=="
                  }
                ]
                JSON,
            '0.17.6',
        ];

        yield 'RC version' => [
            <<<'JSON'
                [
                  {
                    "name": "0.18.0-rc.0",
                    "zipball_url": "https://api.github.com/repos/humbug/php-scoper/zipball/refs/tags/0.18.0-rc.0",
                    "tarball_url": "https://api.github.com/repos/humbug/php-scoper/tarball/refs/tags/0.18.0-rc.0",
                    "commit": {
                      "sha": "f7bd92f2459f1d9a643313f6d324476b0e23e087",
                      "url": "https://api.github.com/repos/humbug/php-scoper/commits/f7bd92f2459f1d9a643313f6d324476b0e23e087"
                    },
                    "node_id": "MDM6UmVmNDQzODQ0NDc6cmVmcy90YWdzLzAuMTguMC1yYy4w"
                  }
                ]
                JSON,
            '0.18.0-rc.0',
        ];

        yield 'invalid version' => [
            <<<'JSON'
                [
                  {
                    "name": "this is not a tag",
                    "zipball_url": "https://api.github.com/repos/humbug/php-scoper/zipball/refs/tags/0.18.0-rc.0",
                    "tarball_url": "https://api.github.com/repos/humbug/php-scoper/tarball/refs/tags/0.18.0-rc.0",
                    "commit": {
                      "sha": "f7bd92f2459f1d9a643313f6d324476b0e23e087",
                      "url": "https://api.github.com/repos/humbug/php-scoper/commits/f7bd92f2459f1d9a643313f6d324476b0e23e087"
                    },
                    "node_id": "MDM6UmVmNDQzODQ0NDc6cmVmcy90YWdzLzAuMTguMC1yYy4w"
                  }
                ]
                JSON,
            'this is not a tag',
        ];

        yield 'incomplete body' => [
            <<<'JSON'
                [
                  {
                    "name": "0.18.0"
                  }
                ]
                JSON,
            '0.18.0',
        ];

        yield 'multiple versions' => [
            <<<'JSON'
                [
                  {
                    "name": "0.18.0-rc.0",
                    "zipball_url": "https://api.github.com/repos/humbug/php-scoper/zipball/refs/tags/0.18.0-rc.0",
                    "tarball_url": "https://api.github.com/repos/humbug/php-scoper/tarball/refs/tags/0.18.0-rc.0",
                    "commit": {
                      "sha": "f7bd92f2459f1d9a643313f6d324476b0e23e087",
                      "url": "https://api.github.com/repos/humbug/php-scoper/commits/f7bd92f2459f1d9a643313f6d324476b0e23e087"
                    },
                    "node_id": "MDM6UmVmNDQzODQ0NDc6cmVmcy90YWdzLzAuMTguMC1yYy4w"
                  },
                  {
                    "name": "0.17.7",
                    "zipball_url": "https://api.github.com/repos/humbug/php-scoper/zipball/refs/tags/0.17.7",
                    "tarball_url": "https://api.github.com/repos/humbug/php-scoper/tarball/refs/tags/0.17.7",
                    "commit": {
                      "sha": "0760c02bd666e0dc4918e4e7fb1c4c53c47bcf54",
                      "url": "https://api.github.com/repos/humbug/php-scoper/commits/0760c02bd666e0dc4918e4e7fb1c4c53c47bcf54"
                    },
                    "node_id": "MDM6UmVmNDQzODQ0NDc6cmVmcy90YWdzLzAuMTcuNw=="
                  },
                  {
                    "name": "0.17.6",
                    "zipball_url": "https://api.github.com/repos/humbug/php-scoper/zipball/refs/tags/0.17.6",
                    "tarball_url": "https://api.github.com/repos/humbug/php-scoper/tarball/refs/tags/0.17.6",
                    "commit": {
                      "sha": "b528b87bdd8500cc243788971b6cc3e061c78e3f",
                      "url": "https://api.github.com/repos/humbug/php-scoper/commits/b528b87bdd8500cc243788971b6cc3e061c78e3f"
                    },
                    "node_id": "MDM6UmVmNDQzODQ0NDc6cmVmcy90YWdzLzAuMTcuNg=="
                  }
                ]
                JSON,
            '0.18.0-rc.0',
        ];
    }

    /**
     * @dataProvider invalidGithubResponseContentProvider
     */
    public function test_it_cannot_parse_the_tag_from_an_invalid_github_response_content(
        string $responseContent,
        Exception $exception
    ): void {
        $this->expectException($exception::class);
        $this->expectExceptionMessage($exception->getMessage());

        TagParser::parse($responseContent);
    }

    public static function invalidGithubResponseContentProvider(): iterable
    {
        yield 'no version' => [
            <<<'JSON'
                []
                JSON,
            // TODO: change message
            new RuntimeException('No tag name could be found in: []'),
        ];

        yield 'non-JSON response' => [
            <<<'XML'
                <xml></xml>
                XML,
            // TODO: should not be able to decode this; change the message
            new RuntimeException('No tag name could be found in: <xml></xml>'),
        ];

        // TODO: fix this case
//        yield 'no name found' => [
//            <<<'JSON'
//            [
//              {
//                "zipball_url": "https://api.github.com/repos/humbug/php-scoper/zipball/refs/tags/0.18.0-rc.0",
//                "tarball_url": "https://api.github.com/repos/humbug/php-scoper/tarball/refs/tags/0.18.0-rc.0",
//                "commit": {
//                  "sha": "f7bd92f2459f1d9a643313f6d324476b0e23e087",
//                  "url": "https://api.github.com/repos/humbug/php-scoper/commits/f7bd92f2459f1d9a643313f6d324476b0e23e087"
//                },
//                "node_id": "MDM6UmVmNDQzODQ0NDc6cmVmcy90YWdzLzAuMTguMC1yYy4w"
//              }
//            ]
//            JSON,
//            new RuntimeException('foo'),
//        ];

        yield 'name is not a string' => [
            <<<'JSON'
                [
                  {
                    "name": true,
                    "zipball_url": "https://api.github.com/repos/humbug/php-scoper/zipball/refs/tags/0.18.0-rc.0",
                    "tarball_url": "https://api.github.com/repos/humbug/php-scoper/tarball/refs/tags/0.18.0-rc.0",
                    "commit": {
                      "sha": "f7bd92f2459f1d9a643313f6d324476b0e23e087",
                      "url": "https://api.github.com/repos/humbug/php-scoper/commits/f7bd92f2459f1d9a643313f6d324476b0e23e087"
                    },
                    "node_id": "MDM6UmVmNDQzODQ0NDc6cmVmcy90YWdzLzAuMTguMC1yYy4w"
                  }
                ]
                JSON,
            // TODO: change the error into something more practical
            new RuntimeException('No tag name could be found in: '),
        ];

        yield 'name is empty' => [
            <<<'JSON'
                [
                  {
                    "name": "",
                    "zipball_url": "https://api.github.com/repos/humbug/php-scoper/zipball/refs/tags/0.18.0-rc.0",
                    "tarball_url": "https://api.github.com/repos/humbug/php-scoper/tarball/refs/tags/0.18.0-rc.0",
                    "commit": {
                      "sha": "f7bd92f2459f1d9a643313f6d324476b0e23e087",
                      "url": "https://api.github.com/repos/humbug/php-scoper/commits/f7bd92f2459f1d9a643313f6d324476b0e23e087"
                    },
                    "node_id": "MDM6UmVmNDQzODQ0NDc6cmVmcy90YWdzLzAuMTguMC1yYy4w"
                  }
                ]
                JSON,
            // TODO: change the error into something more practical
            new RuntimeException('No tag name could be found in: '),
        ];

        yield 'name is a blank string' => [
            <<<'JSON'
                [
                  {
                    "name": " ",
                    "zipball_url": "https://api.github.com/repos/humbug/php-scoper/zipball/refs/tags/0.18.0-rc.0",
                    "tarball_url": "https://api.github.com/repos/humbug/php-scoper/tarball/refs/tags/0.18.0-rc.0",
                    "commit": {
                      "sha": "f7bd92f2459f1d9a643313f6d324476b0e23e087",
                      "url": "https://api.github.com/repos/humbug/php-scoper/commits/f7bd92f2459f1d9a643313f6d324476b0e23e087"
                    },
                    "node_id": "MDM6UmVmNDQzODQ0NDc6cmVmcy90YWdzLzAuMTguMC1yYy4w"
                  }
                ]
                JSON,
            // TODO: change the error into something more practical
            new RuntimeException('Invalid tag name found.'),
        ];
    }
}
