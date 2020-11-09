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

namespace Humbug\PhpScoper\Scoper\Composer;

use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Whitelist;
use function Humbug\PhpScoper\json_decode;
use function Humbug\PhpScoper\json_encode;
use function preg_match;
use const JSON_PRETTY_PRINT;

final class InstalledPackagesScoper implements Scoper
{
    private static $filePattern = '/composer(\/|\\\\)installed\.json$/';

    private $decoratedScoper;

    public function __construct(Scoper $decoratedScoper)
    {
        $this->decoratedScoper = $decoratedScoper;
    }

    /**
     * Scopes PHP and JSON files related to Composer.
     *
     * {@inheritdoc}
     */
    public function scope(string $filePath, string $contents, string $prefix, array $patchers, Whitelist $whitelist): string
    {
        if (1 !== preg_match(self::$filePattern, $filePath)) {
            return $this->decoratedScoper->scope($filePath, $contents, $prefix, $patchers, $whitelist);
        }

        $decodedJson = json_decode($contents, false);

        // compatibility with Composer 2
        if (isset($decodedJson->packages)) {
            $decodedJson->packages = $this->prefixLockPackages($decodedJson->packages, $prefix, $whitelist);
        } else {
            $decodedJson = $this->prefixLockPackages((array) $decodedJson, $prefix, $whitelist);
        }

        return json_encode(
            $decodedJson,
            JSON_PRETTY_PRINT
        );
    }

    private function prefixLockPackages(array $packages, string $prefix, Whitelist $whitelist): array
    {
        foreach ($packages as $index => $package) {
            $packages[$index] = AutoloadPrefixer::prefixPackageAutoloadStatements($package, $prefix, $whitelist);
        }

        return $packages;
    }
}
