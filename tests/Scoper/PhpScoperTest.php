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


        //
        // FQCN usage for a method
        //
        // ====================

        yield '[FQCN usage for a method] complete FQCN' => [
            <<<'PHP'
<?php

\PHPUnit\TextUI\Command::main();

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

\Humbug\PHPUnit\TextUI\Command::main();

PHP
        ];

        yield '[FQCN usage for a method] complete whitelisted FQCN' => [
            <<<'PHP'
<?php

\PHPUnit\TextUI\Command::main();

PHP
            ,
            'Humbug',
            ['PHPUnit\TextUI\Command'],
            <<<'PHP'
<?php

\PHPUnit\TextUI\Command::main();

PHP
        ];

        yield '[FQCN usage for a method] incomplete FQCN' => [
            <<<'PHP'
<?php

PHPUnit\TextUI\Command::main();

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

\Humbug\PHPUnit\TextUI\Command::main();

PHP
        ];

        yield '[FQCN usage for a method] incomplete FQCN in a namespace' => [
            <<<'PHP'
<?php

namespace X;

PHPUnit\TextUI\Command::main();

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

namespace Humbug\X;

PHPUnit\TextUI\Command::main();

PHP
        ];

        yield '[FQCN usage for a method] incomplete FQCN with use statement' => [
            <<<'PHP'
<?php

use PHPUnit;

PHPUnit\TextUI\Command::main();

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

use Humbug\PHPUnit

PHPUnit\TextUI\Command::main();

PHP
        ];

        yield '[FQCN usage for a method] incomplete FQCN in a namespace with use statement' => [
            <<<'PHP'
<?php

namespace X;

use Foo\PHPUnit;

PHPUnit\TextUI\Command::main();

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

namespace Humbug\X;

use Humbug\Foo\PHPUnit;
PHPUnit\TextUI\Command::main();

PHP
        ];

        yield '[FQCN usage for a method] incomplete whitelisted FQCN' => [
            <<<'PHP'
<?php

PHPUnit\TextUI\Command::main();

PHP
            ,
            'Humbug',
            ['PHPUnit\TextUI\Command'],
            <<<'PHP'
<?php

\PHPUnit\TextUI\Command::main();

PHP
        ];

        yield '[FQCN usage for a method] incomplete non-whitelisted FQCN in a namespace' => [
            <<<'PHP'
<?php

namespace X;

PHPUnit\TextUI\Command::main();

PHP
            ,
            'Humbug',
            ['PHPUnit\TextUI\Command'],
            <<<'PHP'
<?php

namespace Humbug\X;

PHPUnit\TextUI\Command::main();

PHP
        ];

        yield '[FQCN usage for a method] incomplete whitelisted FQCN in a namespace' => [
            <<<'PHP'
<?php

namespace X;

PHPUnit\TextUI\Command::main();

PHP
            ,
            'Humbug',
            ['X\PHPUnit\TextUI\Command'],
            <<<'PHP'
<?php

namespace Humbug\X;

\X\PHPUnit\TextUI\Command::main();

PHP
        ];

        yield '[FQCN usage for a method] incomplete non-whitelisted FQCN with a use statement' => [
            <<<'PHP'
<?php

use Foo\PHPUnit;

PHPUnit\TextUI\Command::main();

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

use Humbug\Foo\PHPUnit;
PHPUnit\TextUI\Command::main();

PHP
        ];

        yield '[FQCN usage for a method] incomplete whitelisted FQCN with a use statement' => [
            <<<'PHP'
<?php

use Foo\PHPUnit;

Foo\PHPUnit\TextUI\Command::main();

PHP
            ,
            'Humbug',
            ['Foo\PHPUnit\TextUI\Command'],
            <<<'PHP'
<?php

use Humbug\Foo\PHPUnit;
\Foo\PHPUnit\TextUI\Command::main();

PHP
        ];

        yield '[FQCN usage for a method] incomplete whitelisted FQCN with a use statement in a namespace' => [
            <<<'PHP'
<?php

namespace X;

use Foo\PHPUnit;

PHPUnit\TextUI\Command::main();

PHP
            ,
            'Humbug',
            ['Foo\PHPUnit\TextUI\Command'],
            <<<'PHP'
<?php

namespace Humbug\X;

use Humbug\Foo\PHPUnit;
\Foo\PHPUnit\TextUI\Command::main();

PHP
        ];

        yield '[FQCN usage for a method] incomplete FQCN with use statement' => [
            <<<'PHP'
<?php

use PHPUnit\TextUI\Command;

Command::main();

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

use Humbug\PHPUnit\TextUI\Command;
Command::main();

PHP
        ];

        yield '[FQCN usage for a method] incomplete whitelisted FQCN with use statement' => [
            <<<'PHP'
<?php

use PHPUnit\TextUI\Command;

Command::main();

PHP
            ,
            'Humbug',
            ['PHPUnit\TextUI\Command'],
            <<<'PHP'
<?php

\PHPUnit\TextUI\Command::main();

PHP
        ];

        //
        // FQCN usage for a function
        //
        // ====================

        yield '[FQCN usage for a function] complete FQCN' => [
            <<<'PHP'
<?php

\PHPUnit\TextUI\Command\main();

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

\Humbug\PHPUnit\TextUI\Command\main();

PHP
        ];

        yield '[FQCN usage for a function] complete whitelisted FQCN' => [
            <<<'PHP'
<?php

\PHPUnit\TextUI\Command\main();

PHP
            ,
            'Humbug',
            ['PHPUnit\TextUI\Command\main'],
            // The whitelist does nothing here as it is meant to work only with classes
            <<<'PHP'
<?php

\Humbug\PHPUnit\TextUI\Command\main();

PHP
        ];

        yield '[FQCN usage for a function] complete FQCN with whitelisted class' => [
            <<<'PHP'
<?php

\PHPUnit\TextUI\Command\main();

PHP
            ,
            'Humbug',
            ['PHPUnit\TextUI\Command'],
            // The whitelist does nothing here as it is meant to work only with classes
            <<<'PHP'
<?php

\Humbug\PHPUnit\TextUI\Command\main();

PHP
        ];

        yield '[FQCN usage for a function] incomplete FQCN' => [
            <<<'PHP'
<?php

PHPUnit\TextUI\Command\main();

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

\Humbug\PHPUnit\TextUI\Command\main();

PHP
        ];

        yield '[FQCN usage for a function] incomplete FQCN with use statement' => [
            <<<'PHP'
<?php

use Foo\PHPUnit;

PHPUnit\TextUI\Command\main();

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

use Humbug\Foo\PHPUnit;
PHPUnit\TextUI\Command\main();

PHP
        ];

        yield '[FQCN usage for a function] incomplete FQCN in namespace' => [
            <<<'PHP'
<?php

namespace X;

PHPUnit\TextUI\Command\main();

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

namespace Humbug\X;

PHPUnit\TextUI\Command\main();

PHP
        ];

        yield '[FQCN usage for a function] incomplete FQCN in namespace with use statement' => [
            <<<'PHP'
<?php

namespace X;

use Foo\PHPUnit;

PHPUnit\TextUI\Command\main();

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

namespace Humbug\X;

use Humbug\Foo\PHPUnit;
PHPUnit\TextUI\Command\main();

PHP
        ];

        yield '[FQCN usage for a function] incomplete whitelisted FQCN' => [
            <<<'PHP'
<?php

PHPUnit\TextUI\Command\main();

PHP
            ,
            'Humbug',
            ['PHPUnit\TextUI\Command\main'],
            // The whitelist does nothing here as it is meant to work only with classes
            <<<'PHP'
<?php

\Humbug\PHPUnit\TextUI\Command\main();

PHP
        ];

        yield '[FQCN usage for a function] incomplete FQCN with whitelisted class' => [
            <<<'PHP'
<?php

PHPUnit\TextUI\Command\main();

PHP
            ,
            'Humbug',
            ['PHPUnit\TextUI\Command'],
            // The whitelist does nothing here as it is meant to work only with classes
            <<<'PHP'
<?php

\Humbug\PHPUnit\TextUI\Command\main();

PHP
        ];

        //
        // FQCN usage for a constant
        //
        // ====================

        yield '[FQCN usage for a constant] complete FQCN' => [
            <<<'PHP'
<?php

\PHPUnit\TextUI\Command::FOO;

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

\Humbug\PHPUnit\TextUI\Command::FOO;

PHP
        ];

        yield '[FQCN usage for a constant] complete whitelisted FQCN' => [
            <<<'PHP'
<?php

\PHPUnit\TextUI\Command::FOO;

PHP
            ,
            'Humbug',
            ['PHPUnit\TextUI\Command'],
            <<<'PHP'
<?php

\PHPUnit\TextUI\Command::FOO;

PHP
        ];

        yield '[FQCN usage for a constant] incomplete FQCN' => [
            <<<'PHP'
<?php

PHPUnit\TextUI\Command::FOO;

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

\Humbug\PHPUnit\TextUI\Command::FOO;

PHP
        ];

        yield '[FQCN usage for a constant] incomplete FQCN with use statement' => [
            <<<'PHP'
<?php

use Foo\PHPUnit;

PHPUnit\TextUI\Command::FOO;

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

use Humbug\Foo\PHPUnit;
PHPUnit\TextUI\Command::FOO;

PHP
        ];

        yield '[FQCN usage for a constant] incomplete FQCN in a namespace' => [
            <<<'PHP'
<?php

namespace X;

PHPUnit\TextUI\Command::FOO;

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

namespace Humbug\X;

PHPUnit\TextUI\Command::FOO;

PHP
        ];

        yield '[FQCN usage for a constant] incomplete FQCN in a namespace with use statement' => [
            <<<'PHP'
<?php

namespace X;

use Foo\PHPUnit;

PHPUnit\TextUI\Command::FOO;

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

namespace Humbug\X;

use Humbug\Foo\PHPUnit;
PHPUnit\TextUI\Command::FOO;

PHP
        ];

        yield '[FQCN usage for a constant] incomplete whitelisted FQCN' => [
            <<<'PHP'
<?php

PHPUnit\TextUI\Command::FOO;

PHP
            ,
            'Humbug',
            ['PHPUnit\TextUI\Command'],
            <<<'PHP'
<?php

\PHPUnit\TextUI\Command::FOO;

PHP
        ];

        yield '[FQCN usage for a constant] incomplete whitelisted FQCN with use statement' => [
            <<<'PHP'
<?php

use Foo\PHPUnit;

PHPUnit\TextUI\Command::FOO;

PHP
            ,
            'Humbug',
            ['Foo\PHPUnit\TextUI\Command'],
            <<<'PHP'
<?php

use Humbug\Foo\PHPUnit;
\Foo\PHPUnit\TextUI\Command::FOO;

PHP
        ];

        yield '[FQCN usage for a constant] incomplete whitelisted FQCN in a namespace' => [
            <<<'PHP'
<?php

namespace X;

PHPUnit\TextUI\Command::FOO;

PHP
            ,
            'Humbug',
            ['X\PHPUnit\TextUI\Command'],
            <<<'PHP'
<?php

namespace Humbug\X;

\X\PHPUnit\TextUI\Command::FOO;

PHP
        ];

        yield '[FQCN usage for a constant] incomplete whitelisted FQCN in a namespace with a use statement' => [
            <<<'PHP'
<?php

namespace X;

use Foo\PHPUnit;

PHPUnit\TextUI\Command::FOO;

PHP
            ,
            'Humbug',
            ['Foo\PHPUnit\TextUI\Command'],
            <<<'PHP'
<?php

namespace Humbug\X;

use Humbug\Foo\PHPUnit;
\Foo\PHPUnit\TextUI\Command::FOO;

PHP
        ];

        //
        // Single part global namespace reference
        //
        // ======================================
        //TODO: do not allow to whitelist a class from the global namespace, e.g. `Foo`. Indeed they are already
        //whitelisted and the global namespace whitelister is about scoping them when necessary. This validation can
        //be done in the configuration
        yield '[Single part global namespace reference] a fully qualified constant' => [
            <<<'PHP'
<?php

$a = \PHP_EOL;

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

$a = \PHP_EOL;

PHP
        ];

        yield '[Single part global namespace reference] a non-FQN constant' => [
            <<<'PHP'
<?php

$a = PHP_EOL;

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

$a = PHP_EOL;

PHP
        ];

        yield '[Single part global namespace reference] a FQ function call' => [
            <<<'PHP'
<?php

\var_dump(1);

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

\var_dump(1);

PHP
        ];

        yield '[Single part global namespace reference] a non-FQ function call' => [
            <<<'PHP'
<?php

var_dump(1);

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

var_dump(1);

PHP
        ];


        //
        // Function parameters
        //
        // ====================

        yield '[Function parameter] class_exists' => [
            <<<'PHP'
<?php

class_exists('Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Symfony\\Component\\Yaml\\Yaml');

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

class_exists('Humbug\\Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

PHP
        ];

        yield '[Function parameter] class_exists on whitelisted class' => [
            <<<'PHP'
<?php

class_exists('Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Symfony\\Component\\Yaml\\Yaml');

PHP
            ,
            'Humbug',
            ['Symfony\Component\Yaml\Yaml'],
            <<<'PHP'
<?php

class_exists('Symfony\\Component\\Yaml\\Yaml');
class_exists('\\Symfony\\Component\\Yaml\\Yaml');

PHP
        ];

        yield '[Function parameter] interface_exists' => [
            <<<'PHP'
<?php

interface_exists('Foo\\Bar');
interface_exists('\\Foo\\Bar');

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

interface_exists('Humbug\\Foo\\Bar');
interface_exists('\\Humbug\\Foo\\Bar');

PHP
        ];

        yield '[Function parameter] interface_exists on whitelisted class' => [
            <<<'PHP'
<?php

interface_exists('Foo\\Bar');
interface_exists('\\Foo\\Bar');

PHP
            ,
            'Humbug',
            ['Foo\Bar'],
            <<<'PHP'
<?php

interface_exists('Foo\\Bar');
interface_exists('\\Foo\\Bar');

PHP
        ];

        yield '[Function parameter] class_exists with concat strings' => [
            <<<'PHP'
<?php

class_exists('Symfony\\Component' . '\\Yaml\\Yaml');

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

class_exists('Symfony\\Component' . '\\Yaml\\Yaml');

PHP
        ];

        yield '[Function parameter] class_exists with constant' => [
            <<<'PHP'
<?php

class_exists(Symfony\Component\Yaml\Yaml::class);
class_exists(\Symfony\Component\Yaml\Yaml::class);

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

class_exists(Symfony\Component\Yaml\Yaml::class);
class_exists(\Humbug\Symfony\Component\Yaml\Yaml::class);

PHP
        ];

        yield '[Function parameter] class_exists with constant on whitelisted class' => [
            <<<'PHP'
<?php

class_exists(Symfony\Component\Yaml\Yaml::class);
class_exists(\Symfony\Component\Yaml\Yaml::class);

PHP
            ,
            'Humbug',
            ['Symfony\Component\Yaml\Yaml'],
            <<<'PHP'
<?php

class_exists(Symfony\Component\Yaml\Yaml::class);
class_exists(\Symfony\Component\Yaml\Yaml::class);

PHP
        ];

        yield '[Function parameter] class_exists with variable (no change)' => [
            <<<'PHP'
<?php

$x = 'Symfony\\Component\\Yaml\\Yaml';
$y = '\\Symfony\\Component\\Yaml\\Yaml';
class_exists($x);
class_exists($y);

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

$x = 'Symfony\\Component\\Yaml\\Yaml';
$y = '\\Symfony\\Component\\Yaml\\Yaml';
class_exists($x);
class_exists($y);

PHP
        ];

        yield '[Function parameter] Do not prepend global namespace' => [
            <<<'PHP'
<?php

class_exists('Closure');
class_exists('\\Closure');

PHP
            ,
            'Humbug',
            [],
            <<<'PHP'
<?php

class_exists('Closure');
class_exists('\\Closure');

PHP
        ];
    }
}
