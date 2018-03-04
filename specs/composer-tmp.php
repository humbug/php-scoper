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

return [
    'meta' => [
        'title' => 'Miscellaneous',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'C3' => <<<'PHP'
<?php

$package = $loader->load($localConfig, 'Composer\\Package\\RootPackage', $cwd);

----
<?php

namespace Humbug;

$package = $loader->load($localConfig, 'Humbug\\Composer\\Package\\RootPackage', $cwd);

PHP
    ,

//    'C2' => <<<'PHP'
//<?php
//
//class AutoloaderInitXXX {
//
//    public static function getLoader()
//    {
//        if (null !== self::$loader) {
//            return self::$loader;
//        }
//    }
//}
//
//----
//<?php
//
//namespace Humbug;
//
//class AutoloaderInitXXX
//{
//    public static function getLoader()
//    {
//        if (null !== self::$loader) {
//            return self::$loader;
//        }
//    }
//}
//
//PHP
//    ,
//
//    'C1' => <<<'PHP'
//<?php
//
//namespace Composer;
//
//use Composer\Package\BasePackage;
//
//new InputOption(
//    'stability',
//    's',
//    InputOption::VALUE_REQUIRED,
//    'Minimum stability (empty or one of: '.implode(', ', array_keys(BasePackage::$stabilities)).')'
//);
//
//----
//<?php
//
//namespace Humbug\Composer;
//
//use Humbug\Composer\Package\BasePackage;
//new \Humbug\Composer\InputOption('stability', 's', \Humbug\Composer\InputOption::VALUE_REQUIRED, 'Minimum stability (empty or one of: ' . \implode(', ', \array_keys(\Humbug\Composer\Package\BasePackage::$stabilities)) . ')');
//
//PHP
//    ,
];
