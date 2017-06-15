<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\Scoper;

use function Humbug\PhpScoper\escape_path;
use function Humbug\PhpScoper\make_tmp_dir;
use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Scoper\ComposerScoper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @covers \Humbug\PhpScoper\Scoper\ComposerScoper
 */
class ComposerScoperTest extends TestCase
{
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
            $this->tmp = make_tmp_dir('scoper', __CLASS__);
        }

        chdir($this->tmp);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        (new Filesystem())->remove($this->tmp);
    }

    public function test_it_is_a_Scoper()
    {
        $this->assertTrue(is_a(ComposerScoper::class, Scoper::class, true));
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
}
