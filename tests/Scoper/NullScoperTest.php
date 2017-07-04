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

use function Humbug\PhpScoper\create_fake_patcher;
use Humbug\PhpScoper\Scoper;
use PHPUnit\Framework\TestCase;
use function Humbug\PhpScoper\escape_path;
use function Humbug\PhpScoper\make_tmp_dir;
use function Humbug\PhpScoper\remove_dir;

/**
 * @covers \Humbug\PhpScoper\Scoper\NullScoper
 */
class NullScoperTest extends TestCase
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
        if (null === $this->tmp) {
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

    public function test_is_a_Scoper()
    {
        $this->assertTrue(is_a(NullScoper::class, Scoper::class, true));
    }

    public function test_returns_the_file_content_unchanged()
    {
        $filePath = escape_path($this->tmp.'/file');
        $content = $expected = 'File content';

        touch($filePath);
        file_put_contents($filePath, $content);

        $prefix = 'Humbug';

        $patchers = [create_fake_patcher()];

        $scoper = new NullScoper();

        $actual = $scoper->scope($filePath, $prefix, $patchers);

        $this->assertSame($expected, $actual);
    }
}
