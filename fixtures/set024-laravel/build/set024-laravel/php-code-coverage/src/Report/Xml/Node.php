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

abstract class Node
{
    /**
     * @var \DOMDocument
     */
    private $dom;
    /**
     * @var \DOMElement
     */
    private $contextNode;
    public function __construct(\DOMElement $context)
    {
        $this->setContextNode($context);
    }
    public function getDom() : \DOMDocument
    {
        return $this->dom;
    }
    public function getTotals() : \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Totals
    {
        $totalsContainer = $this->getContextNode()->firstChild;
        if (!$totalsContainer) {
            $totalsContainer = $this->getContextNode()->appendChild($this->dom->createElementNS('https://schema.phpunit.de/coverage/1.0', 'totals'));
        }
        return new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Totals($totalsContainer);
    }
    public function addDirectory(string $name) : \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Directory
    {
        $dirNode = $this->getDom()->createElementNS('https://schema.phpunit.de/coverage/1.0', 'directory');
        $dirNode->setAttribute('name', $name);
        $this->getContextNode()->appendChild($dirNode);
        return new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Directory($dirNode);
    }
    public function addFile(string $name, string $href) : \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\File
    {
        $fileNode = $this->getDom()->createElementNS('https://schema.phpunit.de/coverage/1.0', 'file');
        $fileNode->setAttribute('name', $name);
        $fileNode->setAttribute('href', $href);
        $this->getContextNode()->appendChild($fileNode);
        return new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\File($fileNode);
    }
    protected function setContextNode(\DOMElement $context) : void
    {
        $this->dom = $context->ownerDocument;
        $this->contextNode = $context;
    }
    protected function getContextNode() : \DOMElement
    {
        return $this->contextNode;
    }
}
