<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Extractor;

use InvalidArgumentException;
use PhpParser\ParserFactory;

class IdentifierExtractor
{
    public function __construct($statements = null)
    {
        $this->stubFiles = [];
        $this->extractStatements = $statements ?? [
            "PhpParser\Node\Stmt\Class_",
            "PhpParser\Node\Stmt\Interface_",
            "PhpParser\Node\Stmt\Trait_",
            "PhpParser\Node\Stmt\Function_"
        ];
    }

    public function addStub($file)
    {
        if (! file_exists($file)) {
            throw new InvalidArgumentException("File not found: " . $file);
        }

        $this->stubFiles[] = $file;
        return $this;
    }

    public function extract()
    {
        $identifiers = [];
        foreach ($this->stubFiles as $file) {
            $content = file_get_contents($file);
            $ast = $this->generateAst($content);
            $identifiers = array_merge($identifiers, $this->extractIdentifiersFromAst($ast));
        }

        return $identifiers;
    }

    protected function generateAst($code)
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        return $parser->parse($code);
    }

    protected function extractIdentifiersFromAst($ast)
    {
        $globals = [];
        $items = $ast;

        while (count($items) > 0) {
            $item = array_pop($items);

            if (isset($item->stmts)) {
                $items = array_merge($items, $item->stmts);
            }

            if (in_array(get_class($item), $this->extractStatements)) {
                $globals[] = $item->name->name;
            }
        }

        return $globals;
    }
}
