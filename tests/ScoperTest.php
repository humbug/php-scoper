<?php

/*
 * This file is part of the webmozart/php-scoper package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\PhpScoper\Tests;

use PhpParser\ParserFactory;
use PHPUnit_Framework_TestCase;
use Webmozart\PhpScoper\Scoper;

/**
 * @author Matthieu Auger <mail@matthieuauger.com>
 */
class ScoperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Scoper
     */
    private $scoper;

    public function setUp()
    {
        $this->scoper = new Scoper((new ParserFactory())->create(ParserFactory::PREFER_PHP7));
    }

    public function testScopeNamespace()
    {
        $content = <<<EOF
<?php

namespace MyNamespace;

EOF;
        $expected = <<<EOF
<?php

namespace MyPrefix\MyNamespace;

EOF;

        $this->assertEquals($expected, $this->scoper->scope($content, 'MyPrefix'));
    }

    public function testScopeUseNamespace()
    {
        $content = <<<EOF
<?php

use AnotherNamespace;

EOF;
        $expected = <<<EOF
<?php

use MyPrefix\AnotherNamespace;

EOF;

        $this->assertEquals($expected, $this->scoper->scope($content, 'MyPrefix'));
    }

    public function testScopeFullyQualifiedNamespaceUse()
    {
        $content = <<<EOF
<?php

\$class = new \stdClass();

EOF;
        $expected = <<<EOF
<?php

\$class = new MyPrefix\stdClass();

EOF;

        $this->assertEquals($expected, $this->scoper->scope($content, 'MyPrefix'));
    }
}
