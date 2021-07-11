<?php

declare(strict_types=1);

namespace Humbug\PhpScoper;

use Humbug\PhpScoper\ConfigurationWhitelistFactory;
use Humbug\PhpScoper\RegexChecker;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\ConfigurationWhitelistFactory
 */
final class ConfigurationWhitelistFactoryTest extends TestCase
{
    private ConfigurationWhitelistFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ConfigurationWhitelistFactory(
            new RegexChecker(),
        );
    }

    /**
     * @dataProvider configProvider
     */
    public function test_it_can_create_a_whitelist_object_from_the_config(
        array $config,
        Whitelist $expected
    ): void
    {
        $actual = $this->factory->createWhitelist($config);

        self::assertEquals($expected, $actual);
    }

    public static function configProvider(): iterable
    {
        // TODO
    }
}
