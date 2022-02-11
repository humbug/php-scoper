<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\Reflector;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\Scoper\SymfonyScoper
 */
final class SymfonyScoperTest extends TestCase
{
    private Scoper $scoper;

    protected function setUp(): void
    {
        $this->scoper = new SymfonyScoper(
            new FakeScoper(),
            '_Humbug',
            new EnrichedReflector(
                Reflector::createEmpty(),
                SymbolsConfiguration::create(),
            ),
            new SymbolsRegistry(),
        );
    }

    /**
     * @dataProvider provideScopableFiles
     */
    public function test_it_can_scope_symfony_config_files(
        string $filePath,
        string $contents,
        string $expected): void
    {
        $actual = $this->scoper->scope($filePath, $contents);

        self::assertSame($expected, $actual);
    }

    public function test_it_cannot_scope_non_symfony_config_files(): void
    {
        $this->expectException(LogicException::class);

        $this->scoper->scope('services.php', '');
    }

    public static function provideScopableFiles(): iterable
    {
        yield 'YAML file' => [
            'services.yaml',
            <<<'YAML'
            services: ~
            YAML,
            <<<'YAML'
            services: ~
            YAML,
        ];

        yield 'XML service file' => [
            'services.xml',
            <<<'XML'
            <?xml version="1.0" ?>
            
            <container xmlns="http://symfony.com/schema/dic/services"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
            
                <services></services>
            </container>
            XML,
            <<<'XML'
            <?xml version="1.0" ?>
            
            <container xmlns="http://symfony.com/schema/dic/services"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
            
                <services></services>
            </container>
            XML,
        ];
    }
}
