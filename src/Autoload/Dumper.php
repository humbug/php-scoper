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

final class Dumper
{
    private $whitelist;

    /**
     * @param string[] $whitelist
     */
    public function __construct(array $whitelist)
    {
        $this->whitelist = $whitelist;
    }

    public function dump(string $prefix): string
    {
        $statements = array_map(
            function (string $whitelist) use ($prefix): string {
                return sprintf(
                    'class_exists(\'%s\%s\');',
                    $prefix,
                    $whitelist
                );
            },
            $this->whitelist
        );

        $statements = implode(PHP_EOL, $statements);

        return <<<PHP
<?php

// scoper-autoload.php @generated by PhpScoper

\$loader = require_once __DIR__.'/autoload.php'; 

$statements

return \$loader;

PHP;
    }
}
