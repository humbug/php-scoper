<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Patcher;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\Patcher\NullPatcher
 */
final class NullPatcherTest extends TestCase
{
    public function test_it_returns_the_contents_unchanged(): void
    {
        $filePath = 'file.php';
        $contents = 'file contents';
        $prefix = '_Humbug';

        $patcher = new NullPatcher();

        self::assertSame($contents, $patcher($filePath, $prefix, $contents));
    }
}
