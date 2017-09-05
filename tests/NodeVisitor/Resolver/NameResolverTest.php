<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\NodeVisitor\Resolver;

use function Humbug\PhpScoper\create_fake_patcher;
use function Humbug\PhpScoper\create_parser;
use function Humbug\PhpScoper\escape_path;
use function Humbug\PhpScoper\make_tmp_dir;
use Humbug\PhpScoper\NodeVisitor\Collection\NamespaceStmtCollection;
use Humbug\PhpScoper\NodeVisitor\Collection\UseStmtCollection;
use Humbug\PhpScoper\NodeVisitor\NamespaceStmtCollector;
use Humbug\PhpScoper\NodeVisitor\AppendParentNode;
use Humbug\PhpScoper\NodeVisitor\UseStmt\UseStmtCollector;
use function Humbug\PhpScoper\remove_dir;
use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Scoper\FakeScoper;
use Humbug\PhpScoper\Scoper\PhpScoper;
use Humbug\PhpScoper\Scoper\TraverserFactory;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;
use PhpParser\NodeVisitor\NameResolver;
use PHPUnit\Framework\TestCase;

class NameResolverTest extends TestCase
{
    private static $createScoper;

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
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $nodeTraverserFactory = new class implements TraverserFactory {
            /**
             * @inheritdoc
             */
            public function create(string $prefix, array $whitelist, callable $globalWhitelister): NodeTraverserInterface
            {
                $traverser = new NodeTraverser();

                $namespaceStatements = new NamespaceStmtCollection();
                $useStatements = new UseStmtCollection();

                $traverser->addVisitor(new AppendParentNode());
                $traverser->addVisitor(new NamespaceStmtCollector($namespaceStatements));
                $traverser->addVisitor(new UseStmtCollector($useStatements));

                return $traverser;
            }
        };

        self::$createScoper = function () use ($nodeTraverserFactory): PhpScoper {
            return new PhpScoper(
                create_parser(),
                new FakeScoper(),
                $nodeTraverserFactory
            );
        };
    }

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->scoper = self::$createScoper();

        if (null === $this->tmp) {
            $this->cwd = getcwd();
            $this->tmp = make_tmp_dir('scoper', __CLASS__);
        }

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

    /**
     * @dataProvider provideFiles
     */
    public function test_can_scope_valid_files(string $content, string $prefix, string $expected)
    {
        $filePath = escape_path($this->tmp.'/file.php');
        touch($filePath);
        file_put_contents($filePath, $content);

        $patchers = [create_fake_patcher()];

        $whitelist = [];

        $whitelister = function (string $className) {
            return 'AppKernel' === $className;
        };

        $actual = $this->scoper->scope($filePath, $prefix, $patchers, $whitelist, $whitelister);

        $this->assertSame($expected, $actual);
    }

    public function provideFiles()
    {
        //
        // Namespace declaration
        //
        // ============================
        yield '[Namespace declaration] no declaration' => [
            <<<'PHP'
<?php
PHP
            ,
            'Humbug',
            <<<'PHP'
<?php
PHP
        ];
        yield '[Namespace declaration] simple declaration' => [
            <<<'PHP'
<?php
namespace MyNamespace;
PHP
            ,
            'Humbug',
            <<<'PHP'
<?php
namespace Humbug\MyNamespace;
PHP
        ];
        yield '[Namespace declaration] simple declaration with brackets' => [
            <<<'PHP'
<?php
namespace MyNamespace {
}
PHP
            ,
            'Humbug',
            <<<'PHP'
<?php
namespace Humbug\MyNamespace;
PHP
        ];
        yield '[Namespace declaration] prefixed simple declaration' => [
            <<<'PHP'
<?php
namespace Humbug\MyNamespace;
PHP
            ,
            'Humbug',
            <<<'PHP'
<?php
namespace Humbug\MyNamespace;
PHP
        ];
        yield '[Namespace declaration] multiple declarations' => [
            <<<'PHP'
<?php
namespace MyNamespace1;
namespace MyNamespace2;
namespace MyNamespace3;
PHP
            ,
            'Humbug',
            <<<'PHP'
<?php
namespace Humbug\MyNamespace1;
namespace Humbug\MyNamespace2;
namespace Humbug\MyNamespace3;
PHP
        ];
    }
}
