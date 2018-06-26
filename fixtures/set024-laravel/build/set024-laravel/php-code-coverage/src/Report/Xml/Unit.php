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

final class Unit
{
    /**
     * @var \DOMElement
     */
    private $contextNode;
    public function __construct(\DOMElement $context, string $name)
    {
        $this->contextNode = $context;
        $this->setName($name);
    }
    public function setLines(int $start, int $executable, int $executed) : void
    {
        $this->contextNode->setAttribute('start', $start);
        $this->contextNode->setAttribute('executable', $executable);
        $this->contextNode->setAttribute('executed', $executed);
    }
    public function setCrap(float $crap) : void
    {
        $this->contextNode->setAttribute('crap', $crap);
    }
    public function setPackage(string $full, string $package, string $sub, string $category) : void
    {
        $node = $this->contextNode->getElementsByTagNameNS('https://schema.phpunit.de/coverage/1.0', 'package')->item(0);
        if (!$node) {
            $node = $this->contextNode->appendChild($this->contextNode->ownerDocument->createElementNS('https://schema.phpunit.de/coverage/1.0', 'package'));
        }
        $node->setAttribute('full', $full);
        $node->setAttribute('name', $package);
        $node->setAttribute('sub', $sub);
        $node->setAttribute('category', $category);
    }
    public function setNamespace(string $namespace) : void
    {
        $node = $this->contextNode->getElementsByTagNameNS('https://schema.phpunit.de/coverage/1.0', 'namespace')->item(0);
        if (!$node) {
            $node = $this->contextNode->appendChild($this->contextNode->ownerDocument->createElementNS('https://schema.phpunit.de/coverage/1.0', 'namespace'));
        }
        $node->setAttribute('name', $namespace);
    }
    public function addMethod(string $name) : \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Method
    {
        $node = $this->contextNode->appendChild($this->contextNode->ownerDocument->createElementNS('https://schema.phpunit.de/coverage/1.0', 'method'));
        return new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Method($node, $name);
    }
    private function setName(string $name) : void
    {
        $this->contextNode->setAttribute('name', $name);
    }
}
