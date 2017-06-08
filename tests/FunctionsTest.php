<?php
declare(strict_types=1);

namespace Humbug\PhpScoper;

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    /**
     * @dataProvider providePaths
     */
    public function test_get_the_common_path(array $paths, string $expected)
    {
        $actual = get_common_path($paths);

        $this->assertSame($expected, $actual);
    }

    public function providePaths()
    {
        yield [
            [
                '/path/to/file',

            ],
            '/path/to',
        ];

        yield [
            [
                '/path/to/file',
                '/path/to/another-file',

            ],
            '/path/to',
        ];

        yield [
            [
                '/path/to/file',
                '/path/to/another-file',
                '/path/another-to/another-file',
            ],
            '/path',
        ];

        yield [
            [
                '/path/to/file',
                '/another/path/to/another-file',

            ],
            '',
        ];

        yield [
            [
                '/file',
            ],
            '',
        ];
    }
}
