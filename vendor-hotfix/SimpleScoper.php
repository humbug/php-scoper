<?php

declare(strict_types=1);

/*
 * This file is part of the box project.
 *
 * (c) Kevin Herrera <kevin@herrera.io>
 *     Théo Fidry <theo.fidry@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace KevinGH\Box\PhpScoper;

use Exception;
use Humbug\PhpScoper\Container as PhpScoperContainer;
use Humbug\PhpScoper\Patcher\ComposerPatcher;
use Humbug\PhpScoper\Patcher\SymfonyPatcher;
use Humbug\PhpScoper\Scoper as PhpScoper;
use Humbug\PhpScoper\Scoper\FileWhitelistScoper;
use Humbug\PhpScoper\Whitelist;
use Humbug\PhpScoper\Configuration as PhpScoperConfiguration;
use Opis\Closure\SerializableClosure;
use Serializable;
use function array_map;
use function count;
use function var_dump;

/**
 * @private
 */
final class SimpleScoper implements Scoper
{
    private PhpScoperConfiguration $scoperConfig;
    private PhpScoperContainer $scoperContainer;
    private PhpScoper $scoper;

    /**
     * @var list<string>
     */
    private array $whitelistedFilePaths;

    public function __construct(
        PhpScoperConfiguration $scoperConfig,
        string ...$whitelistedFilePaths
    ) {
        $this->scoperConfig = new PhpScoperConfiguration(
            $scoperConfig->getPath(),
            $scoperConfig->getPrefix(),
            $scoperConfig->getFilesWithContents(),
            $scoperConfig->getWhitelistedFilesWithContents(),
            self::createSerializablePatchers($scoperConfig->getPatchers()),
            $scoperConfig->getWhitelist(),
            $scoperConfig->getInternalClasses(),
            $scoperConfig->getInternalFunctions(),
            $scoperConfig->getInternalConstants(),
        );
        $this->whitelistedFilePaths = $whitelistedFilePaths;
    }

    /**
     * {@inheritdoc}
     */
    public function scope(string $filePath, string $contents): string
    {
        return $this->getScoper()->scope(
            $filePath,
            $contents,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function changeWhitelist(Whitelist $whitelist): void
    {
        $previousConfig = $this->scoperConfig;

        $this->scoperConfig = new PhpScoperConfiguration(
            $previousConfig->getPath(),
            $previousConfig->getPrefix(),
            $previousConfig->getFilesWithContents(),
            $previousConfig->getWhitelistedFilesWithContents(),
            $previousConfig->getPatchers(),
            $whitelist,
            $previousConfig->getInternalClasses(),
            $previousConfig->getInternalFunctions(),
            $previousConfig->getInternalConstants(),
        );

        unset($this->scoper);
    }

    /**
     * {@inheritdoc}
     */
    public function getWhitelist(): Whitelist
    {
        return $this->scoperConfig->getWhitelist();
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefix(): string
    {
        return $this->scoperConfig->getPrefix();
    }

    private function getScoper(): PhpScoper
    {
        if (isset($this->scoper)) {
            return $this->scoper;
        }

        if (!isset($this->scoperContainer)) {
            $this->scoperContainer = new PhpScoperContainer();
        }

        $scoper = $this->scoperContainer
            ->getScoperFactory()
            ->createScoper($this->scoperConfig);

        if (count($this->whitelistedFilePaths) !== 0) {
            $scoper = new FileWhitelistScoper(
                $scoper,
                ...$this->whitelistedFilePaths,
            );
        }

        $this->scoper = $scoper;

        return $this->scoper;
    }

    /**
     * @param callable[] $patchers
     *
     * @retunr SerializableClosure[]
     */
    private static function createSerializablePatchers(array $patchers): array
    {
        return array_map(
            static function (callable $patcher): SerializableClosure {
                if ($patcher instanceof SymfonyPatcher
                    || $patcher instanceof ComposerPatcher
                ) {
                    $patcher = static fn (string $filePath, string $prefix, string $contents) => $patcher($filePath, $prefix, $contents);
                }

                return new SerializableClosure($patcher);
            },
            $patchers,
        );
    }
}
