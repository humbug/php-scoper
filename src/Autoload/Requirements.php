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

use Composer\Semver\Constraint\Constraint;
use Composer\Semver\Constraint\MultiConstraint;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;
use Symfony\Requirements\RequirementCollection;

final class Requirements extends RequirementCollection
{
    public function __construct($rootDir)
    {
        $rootDir = $this->getComposerRootDir($rootDir);

        $composerConfig = $this->readComposer($rootDir);

        $this->addPhpVersionRequirement($composerConfig);
        $this->addExtensionRequirements($composerConfig);

    }

    private function getComposerRootDir($rootDir)
    {
        $dir = $rootDir;

        while (!file_exists($dir.'/composer.json')) {
            if ($dir === dirname($dir)) {
                return $rootDir;
            }

            $dir = dirname($dir);
        }

        return $dir;
    }

    private function readComposer($rootDir)
    {
        $composer = json_decode(file_get_contents($rootDir.'/composer.json'), true);

        return $composer['require'];
    }

    private function addPhpVersionRequirement(array $composerConfig)
    {
        $installedPhpVersion = phpversion();
        $requiredPhpVersion = $composerConfig['php'];

        $this->addRequirement(
            Semver::satisfies(phpversion(), $requiredPhpVersion),
            sprintf(
                'PHP version must satisfies <comment>%s</comment> (<comment>%s</comment> installed)',
                $requiredPhpVersion,
                $installedPhpVersion
            ),
            '',
            ''
        );

        $this->addRequirement(
            version_compare($installedPhpVersion, '5.3.16', '!='),
            'PHP version must not be 5.3.16 as Symfony won\'t work properly with it',
            'Install PHP 5.3.17 or newer (or downgrade to an earlier PHP version)'
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
                'The extension <comment>%s</comment> must be enabled.',
                $extension
            ),
            '',
            ''
        );
    }
}
