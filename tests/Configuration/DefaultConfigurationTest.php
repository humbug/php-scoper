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

namespace Humbug\PhpScoper\Configuration;

use Humbug\PhpScoper\Container;
use Isolated\Symfony\Component\Finder\Finder as IsolatedFinder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use function class_alias;
use function class_exists;

/**
 * @coversNothing
 *
 * @internal
 */
final class DefaultConfigurationTest extends TestCase
{
    private ConfigurationFactory $configurationFactory;

    protected function setUp(): void
    {
        $this->configurationFactory = (new Container())->getConfigurationFactory();

        if (!class_exists(IsolatedFinder::class)) {
            class_alias(Finder::class, IsolatedFinder::class);
        }
    }

    public function test_the_template_file_is_in_sync_with_the_default_configuration(): void
    {
        $templateConfiguration = $this->configurationFactory->create(
            __DIR__.'/../../src/scoper.inc.php.tpl',
        );

        $defaultConfiguration = $this->configurationFactory->create();

        self::assertEquals(
            $templateConfiguration->getSymbolsConfiguration(),
            $defaultConfiguration->getSymbolsConfiguration(),
        );
    }
}
