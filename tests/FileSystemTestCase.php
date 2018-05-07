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

namespace Humbug\PhpScoper;

use PHPUnit\Framework\TestCase;

abstract class FileSystemTestCase extends TestCase
{
    /**
     * @var string
     */
    protected $cwd;

    /**
     * @var string
     */
    protected $tmp;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Cleans up whatever was there before. Indeed upon failure PHPUnit fails to trigger the `tearDown()` method
        // and as a result some temporary files may still remain.
        remove_dir(str_replace('\\', '/', realpath(sys_get_temp_dir())).'/php-scoper');

        $this->cwd = getcwd();
        $this->tmp = make_tmp_dir('php-scoper', __CLASS__);

        chdir($this->tmp);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        chdir($this->cwd);

        remove_dir($this->tmp);
    }
}
