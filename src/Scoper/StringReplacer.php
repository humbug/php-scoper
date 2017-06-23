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

use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Throwable\Exception\RuntimeException;

final class StringReplacer implements Scoper
{
    /**
     * @var array
     */
    private $replaceMap;

    private $decoratedScoper;

    public function __construct(Scoper $decoratedScoper)
    {
        $this->decoratedScoper = $decoratedScoper;
    }

    public function setReplaceMap(array $replaceMap)
    {
        $this->mapByFile($replaceMap);
        var_dump($this->replaceMap);
    }

    /**
     * Replaces selected strings in files if configured by user.
     *
     * {@inheritdoc}
     */
    public function scope(string $filePath, string $prefix): string
    {
        $content = $this->decoratedScoper->scope($filePath, $prefix); 

        if (is_array($this->replaceMap) && isset($this->replaceMap[$filePath])) {
            foreach ($this->replaceMap[$filePath] as $replacement) {
                $content = preg_replace(
                    $replacement['pattern'],
                    $replacement['replacement'],
                    $content,
                    -1,
                    $count
                );

                if (0 === $count) {
                    throw new RuntimeException(
                        sprintf(
                            'The following string replacement could not be applied:' . PHP_EOL
                                . 'File: %s' . PHP_EOL
                                . 'Pattern: %s' . PHP_EOL
                                . 'Replacement: %s',
                            $filePath,
                            $replacement['pattern'],
                            $replacement['replacement']
                        )
                    );
                }
            }
        }

        return $content;
    }

    private function mapByFile(array $replaceMap)
    {
        $this->replaceMap = [];
        foreach ($replaceMap as $replace) {
            foreach ($replace['files'] as $file) {
                $this->replaceMap[$file][] = [
                    'pattern' => $replace['pattern'],
                    'replacement' => $replace['replacement']
                ];
            }
        }
    }
}