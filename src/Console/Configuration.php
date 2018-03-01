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

namespace Humbug\PhpScoper\Console;

use Closure;
use Humbug\PhpScoper\Configuration as NewConfiguration;
use InvalidArgumentException;
use Iterator;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use function Humbug\PhpScoper\chain;

/**
 * @deprecated
 */
final class Configuration extends NewConfiguration
{
}
