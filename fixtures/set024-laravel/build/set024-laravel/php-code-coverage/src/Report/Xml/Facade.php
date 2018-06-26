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

use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\CodeCoverage;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node\AbstractNode;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node\Directory as DirectoryNode;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node\File as FileNode;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\RuntimeException;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Version;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\Environment\Runtime;
final class Facade
{
    /**
     * @var string
     */
    private $target;
    /**
     * @var Project
     */
    private $project;
    /**
     * @var string
     */
    private $phpUnitVersion;
    public function __construct(string $version)
    {
        $this->phpUnitVersion = $version;
    }
    /**
     * @throws RuntimeException
     */
    public function process(\_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\CodeCoverage $coverage, string $target) : void
    {
        if (\substr($target, -1, 1) !== \DIRECTORY_SEPARATOR) {
            $target .= \DIRECTORY_SEPARATOR;
        }
        $this->target = $target;
        $this->initTargetDirectory($target);
        $report = $coverage->getReport();
        $this->project = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Project($coverage->getReport()->getName());
        $this->setBuildInformation();
        $this->processTests($coverage->getTests());
        $this->processDirectory($report, $this->project);
        $this->saveDocument($this->project->asDom(), 'index');
    }
    private function setBuildInformation() : void
    {
        $buildNode = $this->project->getBuildInformation();
        $buildNode->setRuntimeInformation(new \_PhpScoper5b2c11ee6df50\SebastianBergmann\Environment\Runtime());
        $buildNode->setBuildTime(\DateTime::createFromFormat('U', $_SERVER['REQUEST_TIME']));
        $buildNode->setGeneratorVersions($this->phpUnitVersion, \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Version::id());
    }
    /**
     * @throws RuntimeException
     */
    private function initTargetDirectory(string $directory) : void
    {
        if (\file_exists($directory)) {
            if (!\is_dir($directory)) {
                throw new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\RuntimeException("'{$directory}' exists but is not a directory.");
            }
            if (!\is_writable($directory)) {
                throw new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\RuntimeException("'{$directory}' exists but is not writable.");
            }
        } elseif (!$this->createDirectory($directory)) {
            throw new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\RuntimeException("'{$directory}' could not be created.");
        }
    }
    private function processDirectory(\_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node\Directory $directory, \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Node $context) : void
    {
        $directoryName = $directory->getName();
        if ($this->project->getProjectSourceDirectory() === $directoryName) {
            $directoryName = '/';
        }
        $directoryObject = $context->addDirectory($directoryName);
        $this->setTotals($directory, $directoryObject->getTotals());
        foreach ($directory->getDirectories() as $node) {
            $this->processDirectory($node, $directoryObject);
        }
        foreach ($directory->getFiles() as $node) {
            $this->processFile($node, $directoryObject);
        }
    }
    /**
     * @throws RuntimeException
     */
    private function processFile(\_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node\File $file, \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Directory $context) : void
    {
        $fileObject = $context->addFile($file->getName(), $file->getId() . '.xml');
        $this->setTotals($file, $fileObject->getTotals());
        $path = \substr($file->getPath(), \strlen($this->project->getProjectSourceDirectory()));
        $fileReport = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Report($path);
        $this->setTotals($file, $fileReport->getTotals());
        foreach ($file->getClassesAndTraits() as $unit) {
            $this->processUnit($unit, $fileReport);
        }
        foreach ($file->getFunctions() as $function) {
            $this->processFunction($function, $fileReport);
        }
        foreach ($file->getCoverageData() as $line => $tests) {
            if (!\is_array($tests) || \count($tests) === 0) {
                continue;
            }
            $coverage = $fileReport->getLineCoverage($line);
            foreach ($tests as $test) {
                $coverage->addTest($test);
            }
            $coverage->finalize();
        }
        $fileReport->getSource()->setSourceCode(\file_get_contents($file->getPath()));
        $this->saveDocument($fileReport->asDom(), $file->getId());
    }
    private function processUnit(array $unit, \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Report $report) : void
    {
        if (isset($unit['className'])) {
            $unitObject = $report->getClassObject($unit['className']);
        } else {
            $unitObject = $report->getTraitObject($unit['traitName']);
        }
        $unitObject->setLines($unit['startLine'], $unit['executableLines'], $unit['executedLines']);
        $unitObject->setCrap($unit['crap']);
        $unitObject->setPackage($unit['package']['fullPackage'], $unit['package']['package'], $unit['package']['subpackage'], $unit['package']['category']);
        $unitObject->setNamespace($unit['package']['namespace']);
        foreach ($unit['methods'] as $method) {
            $methodObject = $unitObject->addMethod($method['methodName']);
            $methodObject->setSignature($method['signature']);
            $methodObject->setLines($method['startLine'], $method['endLine']);
            $methodObject->setCrap($method['crap']);
            $methodObject->setTotals($method['executableLines'], $method['executedLines'], $method['coverage']);
        }
    }
    private function processFunction(array $function, \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Report $report) : void
    {
        $functionObject = $report->getFunctionObject($function['functionName']);
        $functionObject->setSignature($function['signature']);
        $functionObject->setLines($function['startLine']);
        $functionObject->setCrap($function['crap']);
        $functionObject->setTotals($function['executableLines'], $function['executedLines'], $function['coverage']);
    }
    private function processTests(array $tests) : void
    {
        $testsObject = $this->project->getTests();
        foreach ($tests as $test => $result) {
            if ($test === 'UNCOVERED_FILES_FROM_WHITELIST') {
                continue;
            }
            $testsObject->addTest($test, $result);
        }
    }
    private function setTotals(\_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node\AbstractNode $node, \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Totals $totals) : void
    {
        $loc = $node->getLinesOfCode();
        $totals->setNumLines($loc['loc'], $loc['cloc'], $loc['ncloc'], $node->getNumExecutableLines(), $node->getNumExecutedLines());
        $totals->setNumClasses($node->getNumClasses(), $node->getNumTestedClasses());
        $totals->setNumTraits($node->getNumTraits(), $node->getNumTestedTraits());
        $totals->setNumMethods($node->getNumMethods(), $node->getNumTestedMethods());
        $totals->setNumFunctions($node->getNumFunctions(), $node->getNumTestedFunctions());
    }
    private function getTargetDirectory() : string
    {
        return $this->target;
    }
    /**
     * @throws RuntimeException
     */
    private function saveDocument(\DOMDocument $document, string $name) : void
    {
        $filename = \sprintf('%s/%s.xml', $this->getTargetDirectory(), $name);
        $document->formatOutput = \true;
        $document->preserveWhiteSpace = \false;
        $this->initTargetDirectory(\dirname($filename));
        $document->save($filename);
    }
    private function createDirectory(string $directory) : bool
    {
        return !(!\is_dir($directory) && !@\mkdir($directory, 0777, \true) && !\is_dir($directory));
    }
}
