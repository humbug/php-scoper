<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Patcher;

class RemovePrefixFromIdentifiersPatcher
{
    public function __construct($identifiers)
    {
        $this->identifiers = $identifiers;
    }

    public function __invoke($filePath, $prefix, $content)
    {
        $prefixDoubleSlashed = str_replace('\\', '\\\\', $prefix);
        $quotes = ['\'', '"', '`'];

        foreach ($this->identifiers as $identifier) {
            // "PREFIX\foo()", or "foo extends nativeClass"
            $identifierDoubleSlashed = str_replace('\\', '\\\\', $identifier);
            $content = str_replace($prefix . '\\' . $identifier, $identifier, $content);

            // Replace in strings, e. g.  "if( function_exists('PREFIX\\foo') )"
            foreach ($quotes as $quote) {
                $content = str_replace(
                    $quote . $prefixDoubleSlashed . '\\\\' . $identifierDoubleSlashed . $quote,
                    $quote . $identifierDoubleSlashed . $quote,
                    $content
                );
            }
        }

        return $content;
    }
}
