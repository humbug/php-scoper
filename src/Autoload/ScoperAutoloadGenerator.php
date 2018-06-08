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

namespace Humbug\PhpScoper\Autoload;

use function array_map;
use Humbug\PhpScoper\Whitelist;

final class ScoperAutoloadGenerator
{
    private $whitelist;

    public function __construct(Whitelist $whitelist)
    {
        $this->whitelist = $whitelist;
    }

    public function dump(string $prefix): string
    {
        $statements = $this->createStatements($prefix);

        $statements = implode(PHP_EOL, $statements);

        return <<<PHP
<?php

// scoper-autoload.php @generated by PhpScoper

\$loader = require_once __DIR__.'/autoload.php'; 

$statements

return \$loader;

PHP;
    }

    /**
     * @return string[]
     */
    public function createStatements(string $prefix): array
    {
        return array_map(
            function (string $whitelistedElement) use ($prefix): string {
                return sprintf(
                    'class_exists(\'%s\%s\');',
                    $prefix,
                    $whitelistedElement
                );
            },
            $this->whitelist->getClassWhitelistArray()
        );
    }
}
