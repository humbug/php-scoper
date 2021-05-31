<?php

declare(strict_types=1);

use Humbug\PhpScoper\Configuration;
use Humbug\PhpScoper\Whitelist;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\ConfigurationFactory
 */
final class ConfigurationTest extends TestCase
{
    /**
     * @dataProvider prefixProvider
     */
    public function test_it_validates_the_prefix(
        string $prefix,
        string $expectedExceptionMessage
    ): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        new Configuration(
            null,
            $prefix,
            [],
            [],
            Whitelist::create(
                false,
                false,
                false,
            ),
            [],
        );
    }

    public static function prefixProvider(): iterable
    {
        yield [
            ';',
            'The prefix needs to be composed solely of letters, digits and backslashes (as namespace separators). Got ";"',
        ];

        yield [
            'App\\\\Foo',
            'Invalid namespace separator sequence. Got "App\\\\Foo"',
        ];
    }
}
