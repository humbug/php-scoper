<?php

/*
 * This file is part of the php-code-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report;

use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\CodeCoverage;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node\File;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\RuntimeException;
final class Crap4j
{
    /**
     * @var int
     */
    private $threshold;
    public function __construct(int $threshold = 30)
    {
        $this->threshold = $threshold;
    }
    /**
     * @throws \RuntimeException
     */
    public function process(\_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\CodeCoverage $coverage, ?string $target = null, ?string $name = null) : string
    {
        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = \true;
        $root = $document->createElement('crap_result');
        $document->appendChild($root);
        $project = $document->createElement('project', \is_string($name) ? $name : '');
        $root->appendChild($project);
        $root->appendChild($document->createElement('timestamp', \date('Y-m-d H:i:s', (int) $_SERVER['REQUEST_TIME'])));
        $stats = $document->createElement('stats');
        $methodsNode = $document->createElement('methods');
        $report = $coverage->getReport();
        unset($coverage);
        $fullMethodCount = 0;
        $fullCrapMethodCount = 0;
        $fullCrapLoad = 0;
        $fullCrap = 0;
        foreach ($report as $item) {
            $namespace = 'global';
            if (!$item instanceof \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node\File) {
                continue;
            }
            $file = $document->createElement('file');
            $file->setAttribute('name', $item->getPath());
            $classes = $item->getClassesAndTraits();
            foreach ($classes as $className => $class) {
                foreach ($class['methods'] as $methodName => $method) {
                    $crapLoad = $this->getCrapLoad($method['crap'], $method['ccn'], $method['coverage']);
                    $fullCrap += $method['crap'];
                    $fullCrapLoad += $crapLoad;
                    $fullMethodCount++;
                    if ($method['crap'] >= $this->threshold) {
                        $fullCrapMethodCount++;
                    }
                    $methodNode = $document->createElement('method');
                    if (!empty($class['package']['namespace'])) {
                        $namespace = $class['package']['namespace'];
                    }
                    $methodNode->appendChild($document->createElement('package', $namespace));
                    $methodNode->appendChild($document->createElement('className', $className));
                    $methodNode->appendChild($document->createElement('methodName', $methodName));
                    $methodNode->appendChild($document->createElement('methodSignature', \htmlspecialchars($method['signature'])));
                    $methodNode->appendChild($document->createElement('fullMethod', \htmlspecialchars($method['signature'])));
                    $methodNode->appendChild($document->createElement('crap', $this->roundValue($method['crap'])));
                    $methodNode->appendChild($document->createElement('complexity', $method['ccn']));
                    $methodNode->appendChild($document->createElement('coverage', $this->roundValue($method['coverage'])));
                    $methodNode->appendChild($document->createElement('crapLoad', \round($crapLoad)));
                    $methodsNode->appendChild($methodNode);
                }
            }
        }
        $stats->appendChild($document->createElement('name', 'Method Crap Stats'));
        $stats->appendChild($document->createElement('methodCount', $fullMethodCount));
        $stats->appendChild($document->createElement('crapMethodCount', $fullCrapMethodCount));
        $stats->appendChild($document->createElement('crapLoad', \round($fullCrapLoad)));
        $stats->appendChild($document->createElement('totalCrap', $fullCrap));
        $crapMethodPercent = 0;
        if ($fullMethodCount > 0) {
            $crapMethodPercent = $this->roundValue(100 * $fullCrapMethodCount / $fullMethodCount);
        }
        $stats->appendChild($document->createElement('crapMethodPercent', $crapMethodPercent));
        $root->appendChild($stats);
        $root->appendChild($methodsNode);
        $buffer = $document->saveXML();
        if ($target !== null) {
            if (!$this->createDirectory(\dirname($target))) {
                throw new \RuntimeException(\sprintf('Directory "%s" was not created', \dirname($target)));
            }
            if (@\file_put_contents($target, $buffer) === \false) {
                throw new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\RuntimeException(\sprintf('Could not write to "%s', $target));
            }
        }
        return $buffer;
    }
    /**
     * @param float $crapValue
     * @param int   $cyclomaticComplexity
     * @param float $coveragePercent
     *
     * @return float
     */
    private function getCrapLoad($crapValue, $cyclomaticComplexity, $coveragePercent) : float
    {
        $crapLoad = 0;
        if ($crapValue >= $this->threshold) {
            $crapLoad += $cyclomaticComplexity * (1.0 - $coveragePercent / 100);
            $crapLoad += $cyclomaticComplexity / $this->threshold;
        }
        return $crapLoad;
    }
    /**
     * @param float $value
     *
     * @return float
     */
    private function roundValue($value) : float
    {
        return \round($value, 2);
    }
    private function createDirectory(string $directory) : bool
    {
        return !(!\is_dir($directory) && !@\mkdir($directory, 0777, \true) && !\is_dir($directory));
    }
}
