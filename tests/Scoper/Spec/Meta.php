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

final class Meta extends SpecConfig
{
    public function __construct(
        public readonly string $title,
        public readonly string $prefix = 'Humbug',
        ?int $minPhpVersion = null,
        ?int $maxPhpVersion = null,
        bool $exposeGlobalConstants = false,
        bool $exposeGlobalClasses = false,
        bool $exposeGlobalFunctions = false,
        array $exposeNamespaces = [],
        array $exposeConstants = [],
        array $exposeClasses = [],
        array $exposeFunctions = [],
        array $excludeNamespaces = [],
        array $excludeConstants = [],
        array $excludeClasses = [],
        array $excludeFunctions = [],
        public readonly array $expectedRecordedClasses = [],
        public readonly array $expectedRecordedFunctions = [],
    ) {
        parent::__construct(
            $minPhpVersion,
            $maxPhpVersion,
            $exposeGlobalConstants,
            $exposeGlobalClasses,
            $exposeGlobalFunctions,
            $exposeNamespaces,
            $exposeConstants,
            $exposeClasses,
            $exposeFunctions,
            $excludeNamespaces,
            $excludeConstants,
            $excludeClasses,
            $excludeFunctions,
        );
    }
}
