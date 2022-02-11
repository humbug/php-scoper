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

use Humbug\PhpScoper\PhpParser\TraverserFactory;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PhpParser\Error as PhpParserError;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use function basename;
use function func_get_args;
use function ltrim;
use function preg_match as native_preg_match;

final class PhpScoper implements Scoper
{
    private const FILE_PATH_PATTERN = '/\.php$/';
    private const NOT_FILE_BINARY = '/\..+?$/';
    private const PHP_TAG = '/^<\?php/';
    private const PHP_BINARY = '/^#!.+?php.*\n{1,}<\?php/';

    private Parser $parser;
    private Scoper $decoratedScoper;
    private TraverserFactory $traverserFactory;
    private string $prefix;
    private SymbolsRegistry $symbolsRegistry;

    public function __construct(
        Parser $parser,
        Scoper $decoratedScoper,
        TraverserFactory $traverserFactory,
        string $prefix,
        SymbolsRegistry $symbolsRegistry
    ) {
        $this->parser = $parser;
        $this->decoratedScoper = $decoratedScoper;
        $this->traverserFactory = $traverserFactory;
        $this->prefix = $prefix;
        $this->symbolsRegistry = $symbolsRegistry;
    }

    /**
     * Scopes PHP files.
     *
     * @throws PhpParserError
     */
    public function scope(string $filePath, string $contents): string
    {
        if (!self::isPhpFile($filePath, $contents)) {
            return $this->decoratedScoper->scope(...func_get_args());
        }

        return $this->scopePhp($contents);
    }

    public function scopePhp(string $php): string
    {
        $statements = $this->parser->parse($php);

        $scopedStatements = $this->traverserFactory
            ->create($this)
            ->traverse($statements);

        $prettyPrinter = new Standard();

        return $prettyPrinter->prettyPrintFile($scopedStatements)."\n";
    }

    private static function isPhpFile(string $filePath, string $contents): bool
    {
        if (1 === native_preg_match(self::FILE_PATH_PATTERN, $filePath)) {
            return true;
        }

        if (1 === native_preg_match(self::NOT_FILE_BINARY, basename($filePath))) {
            return false;
        }

        if (1 === native_preg_match(self::PHP_TAG, ltrim($contents))) {
            return true;
        }

        return 1 === native_preg_match(self::PHP_BINARY, $contents);
    }
}
