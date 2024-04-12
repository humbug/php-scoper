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

namespace Humbug\PhpScoper\Scoper\Spec;

final readonly class Meta
{
    public function __construct(
        public string $title,
        public string $prefix = 'Humbug',
        public bool $exposeGlobalConstants = false,
        public bool $exposeGlobalClasses = false,
        public bool $exposeGlobalFunctions = false,
        public array $exposeNamespaces = [],
        public array $exposeConstants = [],
        public array $exposeClasses = [],
        public array $exposeFunctions = [],
        public array $excludeNamespaces = [],
        public array $excludeConstants = [],
        public array $excludeClasses = [],
        public array $excludeFunctions = [],
        public array $expectedRecordedClasses = [],
        public array $expectedRecordedFunctions = [],
    ) {
    }
}
