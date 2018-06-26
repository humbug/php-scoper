<?php

/*
 * This file is part of the php-code-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml;

use _PhpScoper5b2c11ee6df50\TheSeer\Tokenizer\NamespaceUri;
use _PhpScoper5b2c11ee6df50\TheSeer\Tokenizer\Tokenizer;
use _PhpScoper5b2c11ee6df50\TheSeer\Tokenizer\XMLSerializer;
final class Source
{
    /** @var \DOMElement */
    private $context;
    /**
     * @param \DOMElement $context
     */
    public function __construct(\DOMElement $context)
    {
        $this->context = $context;
    }
    public function setSourceCode(string $source) : void
    {
        $context = $this->context;
        $tokens = (new \_PhpScoper5b2c11ee6df50\TheSeer\Tokenizer\Tokenizer())->parse($source);
        $srcDom = (new \_PhpScoper5b2c11ee6df50\TheSeer\Tokenizer\XMLSerializer(new \_PhpScoper5b2c11ee6df50\TheSeer\Tokenizer\NamespaceUri($context->namespaceURI)))->toDom($tokens);
        $context->parentNode->replaceChild($context->ownerDocument->importNode($srcDom->documentElement, \true), $context);
    }
}
