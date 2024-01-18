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

namespace Humbug\PhpScoper\Symbol\Reflector;

use Humbug\PhpScoper\Symbol\Reflector;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Reflector::class)]
class EmptyReflectorTest extends TestCase
{
    private Reflector $reflector;

    protected function setUp(): void
    {
        $this->reflector = Reflector::createEmpty();
    }

    #[DataProviderExternal(PhpStormStubsReflectorTest::class, 'provideClasses')]
    public function test_it_can_identify_internal_classes(string $class): void
    {
        $actual = $this->reflector->isClassInternal($class);

        self::assertFalse($actual);
    }

    #[DataProviderExternal(PhpStormStubsReflectorTest::class, 'provideFunctions')]
    public function test_it_can_identify_internal_functions(string $class, bool $expected): void
    {
        $actual = $this->reflector->isFunctionInternal($class);

        self::assertFalse($actual);
    }

    #[DataProviderExternal(PhpStormStubsReflectorTest::class, 'provideConstants')]
    public function test_it_can_identify_internal_constants(string $class, bool $expected): void
    {
        $actual = $this->reflector->isConstantInternal($class);

        self::assertFalse($actual);
    }
}
