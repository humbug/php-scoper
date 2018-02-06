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
use PhpParser\Error as PhpParserError;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;

final class PhpScoper implements Scoper
{
    private const FILE_PATH_PATTERN = '/.*\.php$/';
    private const NOT_FILE_BINARY = '/\..+?$/';
    private const PHP_TAG = '/^<\?php/';
    private const PHP_BINARY = '/^#!.+?php.*\n{1,}<\?php/';

    private $parser;
    private $decoratedScoper;
    private $traverserFactory;

    public function __construct(Parser $parser, Scoper $decoratedScoper, TraverserFactory $traverserFactory)
    {
        $this->parser = $parser;
        $this->decoratedScoper = $decoratedScoper;
        $this->traverserFactory = $traverserFactory;
    }

    /**
     * Scopes PHP files.
     *
     * {@inheritdoc}
     *
     * @throws PhpParserError
     */
    public function scope(string $filePath, string $contents, string $prefix, array $patchers, array $whitelist, callable $globalWhitelister): string
    {
        if (false === $this->isPhpFile($filePath, $contents)) {
            return $this->decoratedScoper->scope($filePath, $contents, $prefix, $patchers, $whitelist, $globalWhitelister);
        }

        $statements = $this->parser->parse($contents);

        $traverser = $this->traverserFactory->create($prefix, $whitelist, $globalWhitelister);

        $statements = $traverser->traverse($statements);

        $prettyPrinter = new Standard();

        return $prettyPrinter->prettyPrintFile($statements)."\n";
    }

    private function isPhpFile(string $filePath, string $contents): bool
    {
        if (1 === preg_match(self::FILE_PATH_PATTERN, $filePath)) {
            return true;
        }

        if (1 === preg_match(self::NOT_FILE_BINARY, basename($filePath))) {
            return false;
        }

        if (1 === preg_match(self::PHP_TAG, ltrim($contents))) {
            return true;
        }

        return 1 === preg_match(self::PHP_BINARY, $contents);
    }
}
