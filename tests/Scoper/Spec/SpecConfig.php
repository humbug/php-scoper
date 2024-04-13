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

use Humbug\PhpScoper\Configuration\ConfigurationKeys;

abstract class SpecConfig
{
    public function __construct(
        public readonly ?int $minPhpVersion,
        public readonly ?int $maxPhpVersion,
        public readonly bool $exposeGlobalConstants,
        public readonly bool $exposeGlobalClasses,
        public readonly bool $exposeGlobalFunctions,
        public readonly array $exposeNamespaces,
        public readonly array $exposeConstants,
        public readonly array $exposeClasses,
        public readonly array $exposeFunctions,
        public readonly array $excludeNamespaces,
        public readonly array $excludeConstants,
        public readonly array $excludeClasses,
        public readonly array $excludeFunctions,
    ) {
    }

    /**
     * @return array<ConfigurationKeys::*, mixed>
     */
    final public function getSymbolsConfig(): array
    {
        return [
            ConfigurationKeys::EXPOSE_GLOBAL_CONSTANTS_KEYWORD => $this->exposeGlobalConstants,
            ConfigurationKeys::EXPOSE_GLOBAL_CLASSES_KEYWORD => $this->exposeGlobalClasses,
            ConfigurationKeys::EXPOSE_GLOBAL_FUNCTIONS_KEYWORD => $this->exposeGlobalFunctions,
            ConfigurationKeys::EXPOSE_NAMESPACES_KEYWORD => $this->exposeNamespaces,
            ConfigurationKeys::EXPOSE_CONSTANTS_SYMBOLS_KEYWORD => $this->exposeConstants,
            ConfigurationKeys::EXPOSE_FUNCTIONS_SYMBOLS_KEYWORD => $this->exposeFunctions,
            ConfigurationKeys::EXPOSE_CLASSES_SYMBOLS_KEYWORD => $this->exposeClasses,
            ConfigurationKeys::CONSTANTS_INTERNAL_SYMBOLS_KEYWORD => $this->excludeConstants,
            ConfigurationKeys::CLASSES_INTERNAL_SYMBOLS_KEYWORD => $this->exposeClasses,
            ConfigurationKeys::FUNCTIONS_INTERNAL_SYMBOLS_KEYWORD => $this->exposeFunctions,
        ];
    }
}
