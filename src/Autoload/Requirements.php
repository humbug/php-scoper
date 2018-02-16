<?php

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

use Composer\Semver\Semver;
use Symfony\Requirements\RequirementCollection;

/**
 * Collect the list of requirements for running the project. Code in this file must be PHP 5.3+ compatible as is used
 * to know if PHP-Scoper can be run or not.
 */
final class Requirements extends RequirementCollection
{
    public function __construct($composerJson)
    {
        $composerConfig = $this->readComposer($composerJson);

        $this->addPhpVersionRequirement($composerConfig);
        $this->addExtensionRequirements($composerConfig);
    }

    private function readComposer($composerJson)
    {
        $composer = json_decode(file_get_contents($composerJson), true);

        return $composer['require'];
    }

    private function addPhpVersionRequirement(array $composerConfig)
    {
        $installedPhpVersion = phpversion();
        $requiredPhpVersion = $composerConfig['php'];

        $this->addRequirement(
            version_compare(phpversion(), $requiredPhpVersion, '>='),
            sprintf(
                'PHP version must satisfy "%s" ("%s" installed)',
                $requiredPhpVersion,
                $installedPhpVersion
            ),
            ''
        );
    }

    private function addExtensionRequirements(array $composerConfig)
    {
        foreach ($composerConfig as $package => $constraint) {
            if (preg_match('/^ext-(?<extension>.+)$/', $package, $matches)) {
                $this->addExtensionRequirement($matches['extension']);
            }
        }
    }

    /**
     * @param string $extension Extension name, e.g. `iconv`.
     */
    private function addExtensionRequirement($extension)
    {
        $this->addRequirement(
            extension_loaded($extension),
            sprintf(
                'The extension "%s" must be enabled.',
                $extension
            ),
            ''
        );
    }
}
