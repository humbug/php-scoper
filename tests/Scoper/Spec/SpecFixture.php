<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Scoper\Spec;

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use function array_filter;
use function array_values;
use function explode;
use const PHP_INT_MAX;
use const PHP_INT_MIN;
use const PHP_VERSION_ID;

final readonly class SpecFixture
{
    public function __construct(
        public string               $file,
        public string               $spec,
        public string               $contents,
        public string               $prefix,
        public SymbolsConfiguration $symbolsConfiguration,
        public ?string              $expected,
        public array                $expectedRegisteredClasses,
        public array                $expectedRegisteredFunctions,
        public ?int                 $requiredMinPhpVersion,
        public ?int $requiredMaxPhpVersion
    ) {
    }

    public function respectsMinPhpVersion(): bool
    {
        return ($this->requiredMinPhpVersion ?? PHP_INT_MAX) >= PHP_VERSION_ID;
    }

    public function respectsMaxPhpVersion(): bool
    {
        return ($this->requiredMaxPhpVersion ?? 0) <= PHP_VERSION_ID;
    }

    /**
     * @return list<string>
     */
    public function getLines(): array
    {
        return array_values(array_filter(explode("\n", $this->contents)));
    }
}