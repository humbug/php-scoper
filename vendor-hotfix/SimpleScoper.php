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

use Humbug\PhpScoper\Container as PhpScoperContainer;
use Humbug\PhpScoper\Scoper as PhpScoper;
use Humbug\PhpScoper\Whitelist;
use Humbug\PhpScoper\Configuration as PhpScoperConfiguration;

/**
 * @private
 */
final class SimpleScoper implements Scoper
{
    private PhpScoperConfiguration $scoperConfig;
    private PhpScoperContainer $scoperContainer;
    private PhpScoper $scoper;

    public function __construct(PhpScoperConfiguration $scoperConfig)
    {
        $this->scoperConfig = $scoperConfig;
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
        return $this->prefix;
    }

    private function getScoper(): PhpScoper
    {
        if (isset($this->scoper)) {
            return $this->scoper;
        }

        if (!isset($this->scoperContainer)) {
            $this->scoperContainer = new PhpScoperContainer();
        }

        $this->scoper = $this->scoperContainer
            ->getScoperFactory()
            ->createScoper($this->scoperConfig);

        return $this->scoper;
    }
}
