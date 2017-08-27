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

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\PhpParser\FakeParser;
use Humbug\PhpScoper\Scoper;
use PhpParser\Error as PhpParserError;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use function Humbug\PhpScoper\create_fake_patcher;
use function Humbug\PhpScoper\create_fake_whitelister;
use function Humbug\PhpScoper\create_parser;
use function Humbug\PhpScoper\escape_path;
use function Humbug\PhpScoper\make_tmp_dir;
use function Humbug\PhpScoper\remove_dir;
use Symfony\Component\Finder\Finder;
use Throwable;

/**
 * @covers \Humbug\PhpScoper\Scoper\PhpScoper
 */
class PhpScoperTest extends TestCase
{
    /** @private */
    const FIXTURES_PATH = __DIR__.'/files_to_scope';

    /**
     * @var Scoper
     */
    private $scoper;

    /**
     * @var string
     */
    private $cwd;

    /**
     * @var string
     */
    private $tmp;

    /**
     * @var Scoper|ObjectProphecy
     */
    private $decoratedScoperProphecy;

    /**
     * @var Scoper
     */
    private $decoratedScoper;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->scoper = new PhpScoper(
            create_parser(),
            new FakeScoper()
        );

        if (null === $this->tmp) {
            $this->cwd = getcwd();
            $this->tmp = make_tmp_dir('scoper', __CLASS__);
        }

        $this->decoratedScoperProphecy = $this->prophesize(Scoper::class);
        $this->decoratedScoper = $this->decoratedScoperProphecy->reveal();

        chdir($this->tmp);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        chdir($this->cwd);

        remove_dir($this->tmp);
    }

    public function test_is_a_Scoper()
    {
        $this->assertTrue(is_a(PhpScoper::class, Scoper::class, true));
    }

    public function test_can_scope_a_PHP_file()
    {
        $prefix = 'Humbug';
        $filePath = escape_path($this->tmp.'/file.php');
        $patchers = [create_fake_patcher()];
        $whitelist = ['Foo'];
        $whitelister = create_fake_whitelister();

        $content = <<<'PHP'
echo "Humbug!";
PHP;

        touch($filePath);
        file_put_contents($filePath, $content);

        $expected = <<<'PHP'
echo "Humbug!";

PHP;

        $actual = $this->scoper->scope($filePath, $prefix, $patchers, $whitelist, $whitelister);

        $this->assertSame($expected, $actual);
    }

    public function test_does_not_scope_file_if_is_not_a_PHP_file()
    {
        $filePath = 'file.yaml';
        $prefix = 'Humbug';
        $patchers = [create_fake_patcher()];
        $whitelist = ['Foo'];
        $whitelister = create_fake_whitelister();

        $this->decoratedScoperProphecy
            ->scope($filePath, $prefix, $patchers, $whitelist, $whitelister)
            ->willReturn(
                $expected = 'Scoped content'
            )
        ;

        $scoper = new PhpScoper(
            new FakeParser(),
            $this->decoratedScoper
        );

        $actual = $scoper->scope($filePath, $prefix, $patchers, $whitelist, $whitelister);

        $this->assertSame($expected, $actual);

        $this->decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_can_scope_PHP_binary_files()
    {
        $prefix = 'Humbug';
        $filePath = escape_path($this->tmp.'/hello');
        $patchers = [create_fake_patcher()];
        $whitelist = ['Foo'];
        $whitelister = create_fake_whitelister();

        $content = <<<'PHP'
#!/usr/bin/env php
<?php

echo "Hello world";
PHP;

        touch($filePath);
        file_put_contents($filePath, $content);

        $expected = <<<'PHP'
#!/usr/bin/env php
<?php
echo "Hello world";

PHP;

        $actual = $this->scoper->scope($filePath, $prefix, $patchers, $whitelist, $whitelister);

        $this->assertSame($expected, $actual);
    }

    public function test_does_not_scope_a_non_PHP_binary_files()
    {
        $prefix = 'Humbug';

        $filePath = escape_path($this->tmp.'/hello');

        $patchers = [create_fake_patcher()];

        $whitelist = ['Foo'];

        $whitelister = create_fake_whitelister();

        $content = <<<'PHP'
#!/usr/bin/env bash
<?php

echo "Hello world";
PHP;

        touch($filePath);
        file_put_contents($filePath, $content);

        $this->decoratedScoperProphecy
            ->scope($filePath, $prefix, $patchers, $whitelist, $whitelister)
            ->willReturn(
                $expected = 'Scoped content'
            )
        ;

        $scoper = new PhpScoper(
            new FakeParser(),
            $this->decoratedScoper
        );

        $actual = $scoper->scope($filePath, $prefix, $patchers, $whitelist, $whitelister);

        $this->assertSame($expected, $actual);

        $this->decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_cannot_scope_an_invalid_PHP_file()
    {
        $filePath = escape_path($this->tmp.'/invalid-file.php');
        $content = <<<'PHP'
<?php

$class = ;

PHP;

        touch($filePath);
        file_put_contents($filePath, $content);

        $prefix = 'Humbug';
        $patchers = [create_fake_patcher()];
        $whitelist = ['Foo'];
        $whitelister = create_fake_whitelister();

        try {
            $this->scoper->scope($filePath, $prefix, $patchers, $whitelist, $whitelister);

            $this->fail('Expected exception to have been thrown.');
        } catch (PhpParserError $error) {
            $this->assertEquals(
                'Syntax error, unexpected \';\' on line 3',
                $error->getMessage()
            );
            $this->assertSame(0, $error->getCode());
            $this->assertNull($error->getPrevious());
        }
    }

    /**
     * @dataProvider provideValidFiles
     */
    public function test_can_scope_valid_files(string $content, string $prefix, array $whitelist, string $expected)
    {
        $filePath = escape_path($this->tmp.'/file.php');

        touch($filePath);
        file_put_contents($filePath, $content);

        $patchers = [create_fake_patcher()];

        $whitelister = function (string $className) {
            return 'AppKernel' === $className;
        };

        $actual = $this->scoper->scope($filePath, $prefix, $patchers, $whitelist, $whitelister);

        $this->assertSame($expected, $actual);
    }

    public function provideValidFiles()
    {
        $files = (new Finder())->files()->in(self::FIXTURES_PATH);

        foreach ($files as $file) {
            try {
                $fixtures = include $file;

                $meta = $fixtures['meta'];
                unset($fixtures['meta']);

                foreach ($fixtures as $fixtureTitle => $fixtureSet) {
                    $payload = is_string($fixtureSet) ? $fixtureSet : $fixtureSet['payload'];

                    $payloadParts = preg_split("/\n----(?:\n|$)/", $payload);

                    yield sprintf('[%s] %s', $meta['title'], $fixtureTitle) => [
                        $payloadParts[0],
                        $fixtureSet['prefix'] ?? $meta['prefix'],
                        $fixtureSet['whitelist'] ?? $meta['whitelist'],
                        $payloadParts[1],
                    ];
                }
            } catch (Throwable $e) {
                $this->fail(sprintf('An error occurred while parsing the file "%s".', $file));
            }
        }

        return;

        //TODO: do not allow to whitelist a class from the global namespace, e.g. `Foo`. Indeed they are already
        //whitelisted and the global namespace whitelister is about scoping them when necessary. This validation can
        //be done in the configuration
    }
}
