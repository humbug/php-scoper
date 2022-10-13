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

namespace Humbug\PhpScoper\Scoper;

use UnexpectedValueException;
use function sprintf;

final class ScoperStub implements Scoper
{
    public const ANY_FILE_PATH = '*';

    /**
     * @var array<string, array<string, string>>
     */
    private array $scopedContentsByFileAndContents = [];

    /**
     * @var array<string, string>
     */
    private array $scopedContentsByContents = [];

    /**
     * @param array<string, array{string, string}> $config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * @param array<string, array{string, string}> $config
     */
    public function setConfig(array $config): void
    {
        $this->scopedContentsByFileAndContents = [];

        foreach ($config as $filePath => [$contents, $scopedContents]) {
            $this->addConfig($filePath, $contents, $scopedContents);
        }
    }

    public function addConfig(string $filePath, string $contents, string $scopedContents): void
    {
        $this->scopedContentsByFileAndContents[$filePath][$contents] = $scopedContents;

        if (self::ANY_FILE_PATH === $filePath) {
            $this->scopedContentsByContents[$contents] = $scopedContents;
        }
    }

    public function scope(string $filePath, string $contents): string
    {
        $scopedContents = $this->scopedContentsByContents[$contents]
            ?? $this->scopedContentsByFileAndContents[$filePath][$contents]
            ?? null;

        if (null === $scopedContents) {
            throw new UnexpectedValueException(
                sprintf(
                    'No configuration found for the file "%s" and contents "%s".',
                    $filePath,
                    $contents,
                ),
            );
        }

        return $scopedContents;
    }
}
