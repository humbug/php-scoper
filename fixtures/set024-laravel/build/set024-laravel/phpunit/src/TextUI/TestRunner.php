<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\TextUI;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Error\Deprecated;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Error\Notice;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Error\Warning;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestListener;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestResult;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\AfterLastTestHook;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\BaseTestRunner;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\BeforeFirstTestHook;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\Filter\ExcludeGroupFilterIterator;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\Filter\Factory;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\Filter\IncludeGroupFilterIterator;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\Filter\NameFilterIterator;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\Hook;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\StandardTestSuiteLoader;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestHook;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestListenerAdapter;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestSuiteLoader;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestSuiteSorter;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\Version;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\Configuration;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\Log\JUnit;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\Log\TeamCity;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\Printer;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\TestDox\HtmlResultPrinter;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\TestDox\TextResultPrinter;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\TestDox\XmlResultPrinter;
use ReflectionClass;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\CodeCoverage;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Exception as CodeCoverageException;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Filter as CodeCoverageFilter;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Clover as CloverReport;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Crap4j as Crap4jReport;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlReport;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\PHP as PhpReport;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Text as TextReport;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Facade as XmlReport;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\Comparator\Comparator;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\Environment\Runtime;
/**
 * A TestRunner for the Command Line Interface (CLI)
 * PHP SAPI Module.
 */
class TestRunner extends \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\BaseTestRunner
{
    public const SUCCESS_EXIT = 0;
    public const FAILURE_EXIT = 1;
    public const EXCEPTION_EXIT = 2;
    /**
     * @var bool
     */
    protected static $versionStringPrinted = \false;
    /**
     * @var CodeCoverageFilter
     */
    protected $codeCoverageFilter;
    /**
     * @var TestSuiteLoader
     */
    protected $loader;
    /**
     * @var ResultPrinter
     */
    protected $printer;
    /**
     * @var Runtime
     */
    private $runtime;
    /**
     * @var bool
     */
    private $messagePrinted = \false;
    /**
     * @var Hook[]
     */
    private $extensions = [];
    /**
     * @param ReflectionClass|Test $test
     * @param array                $arguments
     * @param bool                 $exit
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws Exception
     * @throws \ReflectionException
     */
    public static function run($test, array $arguments = [], $exit = \true) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestResult
    {
        if ($test instanceof \ReflectionClass) {
            $test = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite($test);
        }
        if ($test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test) {
            $aTestRunner = new self();
            return $aTestRunner->doRun($test, $arguments, $exit);
        }
        throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception('No test case or test suite found.');
    }
    public function __construct(\_PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestSuiteLoader $loader = null, \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Filter $filter = null)
    {
        if ($filter === null) {
            $filter = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Filter();
        }
        $this->codeCoverageFilter = $filter;
        $this->loader = $loader;
        $this->runtime = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\Environment\Runtime();
    }
    /**
     * @throws \PHPUnit\Runner\Exception
     * @throws Exception
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \ReflectionException
     */
    public function doRun(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $suite, array $arguments = [], bool $exit = \true) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestResult
    {
        if (isset($arguments['configuration'])) {
            $GLOBALS['__PHPUNIT_CONFIGURATION_FILE'] = $arguments['configuration'];
        }
        $this->handleConfiguration($arguments);
        $this->processSuiteFilters($suite, $arguments);
        if (isset($arguments['bootstrap'])) {
            $GLOBALS['__PHPUNIT_BOOTSTRAP'] = $arguments['bootstrap'];
        }
        if ($arguments['backupGlobals'] === \true) {
            $suite->setBackupGlobals(\true);
        }
        if ($arguments['backupStaticAttributes'] === \true) {
            $suite->setBackupStaticAttributes(\true);
        }
        if ($arguments['beStrictAboutChangesToGlobalState'] === \true) {
            $suite->setBeStrictAboutChangesToGlobalState(\true);
        }
        if ($arguments['executionOrder'] === \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestSuiteSorter::ORDER_RANDOMIZED) {
            \mt_srand($arguments['randomOrderSeed']);
        }
        if ($arguments['executionOrder'] !== \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestSuiteSorter::ORDER_DEFAULT || $arguments['resolveDependencies']) {
            $sorter = new \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestSuiteSorter();
            $sorter->reorderTestsInSuite($suite, $arguments['executionOrder'], $arguments['resolveDependencies']);
            unset($sorter);
        }
        if (\is_int($arguments['repeat']) && $arguments['repeat'] > 0) {
            $_suite = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite();
            foreach (\range(1, $arguments['repeat']) as $step) {
                $_suite->addTest($suite);
            }
            $suite = $_suite;
            unset($_suite);
        }
        $result = $this->createTestResult();
        $listener = new \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestListenerAdapter();
        $listenerNeeded = \false;
        foreach ($this->extensions as $extension) {
            if ($extension instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestHook) {
                $listener->add($extension);
                $listenerNeeded = \true;
            }
        }
        if ($listenerNeeded) {
            $result->addListener($listener);
        }
        unset($listener, $listenerNeeded);
        if (!$arguments['convertErrorsToExceptions']) {
            $result->convertErrorsToExceptions(\false);
        }
        if (!$arguments['convertDeprecationsToExceptions']) {
            \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Error\Deprecated::$enabled = \false;
        }
        if (!$arguments['convertNoticesToExceptions']) {
            \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Error\Notice::$enabled = \false;
        }
        if (!$arguments['convertWarningsToExceptions']) {
            \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Error\Warning::$enabled = \false;
        }
        if ($arguments['stopOnError']) {
            $result->stopOnError(\true);
        }
        if ($arguments['stopOnFailure']) {
            $result->stopOnFailure(\true);
        }
        if ($arguments['stopOnWarning']) {
            $result->stopOnWarning(\true);
        }
        if ($arguments['stopOnIncomplete']) {
            $result->stopOnIncomplete(\true);
        }
        if ($arguments['stopOnRisky']) {
            $result->stopOnRisky(\true);
        }
        if ($arguments['stopOnSkipped']) {
            $result->stopOnSkipped(\true);
        }
        if ($arguments['registerMockObjectsFromTestArgumentsRecursively']) {
            $result->setRegisterMockObjectsFromTestArgumentsRecursively(\true);
        }
        if ($this->printer === null) {
            if (isset($arguments['printer']) && $arguments['printer'] instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Printer) {
                $this->printer = $arguments['printer'];
            } else {
                $printerClass = \_PhpScoper5b2c11ee6df50\PHPUnit\TextUI\ResultPrinter::class;
                if (isset($arguments['printer']) && \is_string($arguments['printer']) && \class_exists($arguments['printer'], \false)) {
                    $class = new \ReflectionClass($arguments['printer']);
                    if ($class->isSubclassOf(\_PhpScoper5b2c11ee6df50\PHPUnit\TextUI\ResultPrinter::class)) {
                        $printerClass = $arguments['printer'];
                    }
                }
                $this->printer = new $printerClass(isset($arguments['stderr']) && $arguments['stderr'] === \true ? 'php://stderr' : null, $arguments['verbose'], $arguments['colors'], $arguments['debug'], $arguments['columns'], $arguments['reverseList']);
            }
        }
        $this->printer->write(\_PhpScoper5b2c11ee6df50\PHPUnit\Runner\Version::getVersionString() . "\n");
        self::$versionStringPrinted = \true;
        if ($arguments['verbose']) {
            $runtime = $this->runtime->getNameWithVersion();
            if ($this->runtime->hasXdebug()) {
                $runtime .= \sprintf(' with Xdebug %s', \phpversion('xdebug'));
            }
            $this->writeMessage('Runtime', $runtime);
            if ($arguments['executionOrder'] === \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestSuiteSorter::ORDER_RANDOMIZED) {
                $this->writeMessage('Random seed', $arguments['randomOrderSeed']);
            }
            if (isset($arguments['configuration'])) {
                $this->writeMessage('Configuration', $arguments['configuration']->getFilename());
            }
            foreach ($arguments['loadedExtensions'] as $extension) {
                $this->writeMessage('Extension', $extension);
            }
            foreach ($arguments['notLoadedExtensions'] as $extension) {
                $this->writeMessage('Extension', $extension);
            }
        }
        if ($this->runtime->discardsComments()) {
            $this->writeMessage('Warning', 'opcache.save_comments=0 set; annotations will not work');
        }
        if (isset($arguments['configuration']) && $arguments['configuration']->hasValidationErrors()) {
            $this->write("\n  Warning - The configuration file did not pass validation!\n  The following problems have been detected:\n");
            foreach ($arguments['configuration']->getValidationErrors() as $line => $errors) {
                $this->write(\sprintf("\n  Line %d:\n", $line));
                foreach ($errors as $msg) {
                    $this->write(\sprintf("  - %s\n", $msg));
                }
            }
            $this->write("\n  Test results may not be as expected.\n\n");
        }
        foreach ($arguments['listeners'] as $listener) {
            $result->addListener($listener);
        }
        $result->addListener($this->printer);
        $codeCoverageReports = 0;
        if (!isset($arguments['noLogging'])) {
            if (isset($arguments['testdoxHTMLFile'])) {
                $result->addListener(new \_PhpScoper5b2c11ee6df50\PHPUnit\Util\TestDox\HtmlResultPrinter($arguments['testdoxHTMLFile'], $arguments['testdoxGroups'], $arguments['testdoxExcludeGroups']));
            }
            if (isset($arguments['testdoxTextFile'])) {
                $result->addListener(new \_PhpScoper5b2c11ee6df50\PHPUnit\Util\TestDox\TextResultPrinter($arguments['testdoxTextFile'], $arguments['testdoxGroups'], $arguments['testdoxExcludeGroups']));
            }
            if (isset($arguments['testdoxXMLFile'])) {
                $result->addListener(new \_PhpScoper5b2c11ee6df50\PHPUnit\Util\TestDox\XmlResultPrinter($arguments['testdoxXMLFile']));
            }
            if (isset($arguments['teamcityLogfile'])) {
                $result->addListener(new \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Log\TeamCity($arguments['teamcityLogfile']));
            }
            if (isset($arguments['junitLogfile'])) {
                $result->addListener(new \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Log\JUnit($arguments['junitLogfile'], $arguments['reportUselessTests']));
            }
            if (isset($arguments['coverageClover'])) {
                $codeCoverageReports++;
            }
            if (isset($arguments['coverageCrap4J'])) {
                $codeCoverageReports++;
            }
            if (isset($arguments['coverageHtml'])) {
                $codeCoverageReports++;
            }
            if (isset($arguments['coveragePHP'])) {
                $codeCoverageReports++;
            }
            if (isset($arguments['coverageText'])) {
                $codeCoverageReports++;
            }
            if (isset($arguments['coverageXml'])) {
                $codeCoverageReports++;
            }
        }
        if (isset($arguments['noCoverage'])) {
            $codeCoverageReports = 0;
        }
        if ($codeCoverageReports > 0 && !$this->runtime->canCollectCodeCoverage()) {
            $this->writeMessage('Error', 'No code coverage driver is available');
            $codeCoverageReports = 0;
        }
        if ($codeCoverageReports > 0) {
            $codeCoverage = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\CodeCoverage(null, $this->codeCoverageFilter);
            $codeCoverage->setUnintentionallyCoveredSubclassesWhitelist([\_PhpScoper5b2c11ee6df50\SebastianBergmann\Comparator\Comparator::class]);
            $codeCoverage->setCheckForUnintentionallyCoveredCode($arguments['strictCoverage']);
            $codeCoverage->setCheckForMissingCoversAnnotation($arguments['strictCoverage']);
            if (isset($arguments['forceCoversAnnotation'])) {
                $codeCoverage->setForceCoversAnnotation($arguments['forceCoversAnnotation']);
            }
            if (isset($arguments['ignoreDeprecatedCodeUnitsFromCodeCoverage'])) {
                $codeCoverage->setIgnoreDeprecatedCode($arguments['ignoreDeprecatedCodeUnitsFromCodeCoverage']);
            }
            if (isset($arguments['disableCodeCoverageIgnore'])) {
                $codeCoverage->setDisableIgnoredLines(\true);
            }
            $whitelistFromConfigurationFile = \false;
            $whitelistFromOption = \false;
            if (isset($arguments['whitelist'])) {
                $this->codeCoverageFilter->addDirectoryToWhitelist($arguments['whitelist']);
                $whitelistFromOption = \true;
            }
            if (isset($arguments['configuration'])) {
                $filterConfiguration = $arguments['configuration']->getFilterConfiguration();
                if (!empty($filterConfiguration['whitelist'])) {
                    $whitelistFromConfigurationFile = \true;
                }
                if (!empty($filterConfiguration['whitelist'])) {
                    $codeCoverage->setAddUncoveredFilesFromWhitelist($filterConfiguration['whitelist']['addUncoveredFilesFromWhitelist']);
                    $codeCoverage->setProcessUncoveredFilesFromWhitelist($filterConfiguration['whitelist']['processUncoveredFilesFromWhitelist']);
                    foreach ($filterConfiguration['whitelist']['include']['directory'] as $dir) {
                        $this->codeCoverageFilter->addDirectoryToWhitelist($dir['path'], $dir['suffix'], $dir['prefix']);
                    }
                    foreach ($filterConfiguration['whitelist']['include']['file'] as $file) {
                        $this->codeCoverageFilter->addFileToWhitelist($file);
                    }
                    foreach ($filterConfiguration['whitelist']['exclude']['directory'] as $dir) {
                        $this->codeCoverageFilter->removeDirectoryFromWhitelist($dir['path'], $dir['suffix'], $dir['prefix']);
                    }
                    foreach ($filterConfiguration['whitelist']['exclude']['file'] as $file) {
                        $this->codeCoverageFilter->removeFileFromWhitelist($file);
                    }
                }
            }
            if (!$this->codeCoverageFilter->hasWhitelist()) {
                if (!$whitelistFromConfigurationFile && !$whitelistFromOption) {
                    $this->writeMessage('Error', 'No whitelist is configured, no code coverage will be generated.');
                } else {
                    $this->writeMessage('Error', 'Incorrect whitelist config, no code coverage will be generated.');
                }
                $codeCoverageReports = 0;
                unset($codeCoverage);
            }
        }
        $this->printer->write("\n");
        if (isset($codeCoverage)) {
            $result->setCodeCoverage($codeCoverage);
            if ($codeCoverageReports > 1 && isset($arguments['cacheTokens'])) {
                $codeCoverage->setCacheTokens($arguments['cacheTokens']);
            }
        }
        $result->beStrictAboutTestsThatDoNotTestAnything($arguments['reportUselessTests']);
        $result->beStrictAboutOutputDuringTests($arguments['disallowTestOutput']);
        $result->beStrictAboutTodoAnnotatedTests($arguments['disallowTodoAnnotatedTests']);
        $result->beStrictAboutResourceUsageDuringSmallTests($arguments['beStrictAboutResourceUsageDuringSmallTests']);
        $result->enforceTimeLimit($arguments['enforceTimeLimit']);
        $result->setTimeoutForSmallTests($arguments['timeoutForSmallTests']);
        $result->setTimeoutForMediumTests($arguments['timeoutForMediumTests']);
        $result->setTimeoutForLargeTests($arguments['timeoutForLargeTests']);
        if ($suite instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite) {
            $suite->setRunTestInSeparateProcess($arguments['processIsolation']);
        }
        foreach ($this->extensions as $extension) {
            if ($extension instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\BeforeFirstTestHook) {
                $extension->executeBeforeFirstTest();
            }
        }
        $suite->run($result);
        foreach ($this->extensions as $extension) {
            if ($extension instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\AfterLastTestHook) {
                $extension->executeAfterLastTest();
            }
        }
        $result->flushListeners();
        if ($this->printer instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\TextUI\ResultPrinter) {
            $this->printer->printResult($result);
        }
        if (isset($codeCoverage)) {
            if (isset($arguments['coverageClover'])) {
                $this->printer->write("\nGenerating code coverage report in Clover XML format ...");
                try {
                    $writer = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Clover();
                    $writer->process($codeCoverage, $arguments['coverageClover']);
                    $this->printer->write(" done\n");
                    unset($writer);
                } catch (\_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Exception $e) {
                    $this->printer->write(" failed\n" . $e->getMessage() . "\n");
                }
            }
            if (isset($arguments['coverageCrap4J'])) {
                $this->printer->write("\nGenerating Crap4J report XML file ...");
                try {
                    $writer = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Crap4j($arguments['crap4jThreshold']);
                    $writer->process($codeCoverage, $arguments['coverageCrap4J']);
                    $this->printer->write(" done\n");
                    unset($writer);
                } catch (\_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Exception $e) {
                    $this->printer->write(" failed\n" . $e->getMessage() . "\n");
                }
            }
            if (isset($arguments['coverageHtml'])) {
                $this->printer->write("\nGenerating code coverage report in HTML format ...");
                try {
                    $writer = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Html\Facade($arguments['reportLowUpperBound'], $arguments['reportHighLowerBound'], \sprintf(' and <a href="https://phpunit.de/">PHPUnit %s</a>', \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\Version::id()));
                    $writer->process($codeCoverage, $arguments['coverageHtml']);
                    $this->printer->write(" done\n");
                    unset($writer);
                } catch (\_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Exception $e) {
                    $this->printer->write(" failed\n" . $e->getMessage() . "\n");
                }
            }
            if (isset($arguments['coveragePHP'])) {
                $this->printer->write("\nGenerating code coverage report in PHP format ...");
                try {
                    $writer = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\PHP();
                    $writer->process($codeCoverage, $arguments['coveragePHP']);
                    $this->printer->write(" done\n");
                    unset($writer);
                } catch (\_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Exception $e) {
                    $this->printer->write(" failed\n" . $e->getMessage() . "\n");
                }
            }
            if (isset($arguments['coverageText'])) {
                if ($arguments['coverageText'] == 'php://stdout') {
                    $outputStream = $this->printer;
                    $colors = $arguments['colors'] && $arguments['colors'] != \_PhpScoper5b2c11ee6df50\PHPUnit\TextUI\ResultPrinter::COLOR_NEVER;
                } else {
                    $outputStream = new \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Printer($arguments['coverageText']);
                    $colors = \false;
                }
                $processor = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Text($arguments['reportLowUpperBound'], $arguments['reportHighLowerBound'], $arguments['coverageTextShowUncoveredFiles'], $arguments['coverageTextShowOnlySummary']);
                $outputStream->write($processor->process($codeCoverage, $colors));
            }
            if (isset($arguments['coverageXml'])) {
                $this->printer->write("\nGenerating code coverage report in PHPUnit XML format ...");
                try {
                    $writer = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Report\Xml\Facade(\_PhpScoper5b2c11ee6df50\PHPUnit\Runner\Version::id());
                    $writer->process($codeCoverage, $arguments['coverageXml']);
                    $this->printer->write(" done\n");
                    unset($writer);
                } catch (\_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Exception $e) {
                    $this->printer->write(" failed\n" . $e->getMessage() . "\n");
                }
            }
        }
        if ($exit) {
            if ($result->wasSuccessful()) {
                if ($arguments['failOnRisky'] && !$result->allHarmless()) {
                    exit(self::FAILURE_EXIT);
                }
                if ($arguments['failOnWarning'] && $result->warningCount() > 0) {
                    exit(self::FAILURE_EXIT);
                }
                exit(self::SUCCESS_EXIT);
            }
            if ($result->errorCount() > 0) {
                exit(self::EXCEPTION_EXIT);
            }
            if ($result->failureCount() > 0) {
                exit(self::FAILURE_EXIT);
            }
        }
        return $result;
    }
    public function setPrinter(\_PhpScoper5b2c11ee6df50\PHPUnit\TextUI\ResultPrinter $resultPrinter) : void
    {
        $this->printer = $resultPrinter;
    }
    /**
     * Returns the loader to be used.
     */
    public function getLoader() : \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestSuiteLoader
    {
        if ($this->loader === null) {
            $this->loader = new \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\StandardTestSuiteLoader();
        }
        return $this->loader;
    }
    protected function createTestResult() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestResult
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestResult();
    }
    /**
     * Override to define how to handle a failed loading of
     * a test suite.
     */
    protected function runFailed(string $message) : void
    {
        $this->write($message . \PHP_EOL);
        exit(self::FAILURE_EXIT);
    }
    protected function write(string $buffer) : void
    {
        if (\PHP_SAPI != 'cli' && \PHP_SAPI != 'phpdbg') {
            $buffer = \htmlspecialchars($buffer);
        }
        if ($this->printer !== null) {
            $this->printer->write($buffer);
        } else {
            print $buffer;
        }
    }
    /**
     * @throws Exception
     */
    protected function handleConfiguration(array &$arguments) : void
    {
        if (isset($arguments['configuration']) && !$arguments['configuration'] instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Configuration) {
            $arguments['configuration'] = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Configuration::getInstance($arguments['configuration']);
        }
        $arguments['debug'] = $arguments['debug'] ?? \false;
        $arguments['filter'] = $arguments['filter'] ?? \false;
        $arguments['listeners'] = $arguments['listeners'] ?? [];
        if (isset($arguments['configuration'])) {
            $arguments['configuration']->handlePHPConfiguration();
            $phpunitConfiguration = $arguments['configuration']->getPHPUnitConfiguration();
            if (isset($phpunitConfiguration['backupGlobals']) && !isset($arguments['backupGlobals'])) {
                $arguments['backupGlobals'] = $phpunitConfiguration['backupGlobals'];
            }
            if (isset($phpunitConfiguration['backupStaticAttributes']) && !isset($arguments['backupStaticAttributes'])) {
                $arguments['backupStaticAttributes'] = $phpunitConfiguration['backupStaticAttributes'];
            }
            if (isset($phpunitConfiguration['beStrictAboutChangesToGlobalState']) && !isset($arguments['beStrictAboutChangesToGlobalState'])) {
                $arguments['beStrictAboutChangesToGlobalState'] = $phpunitConfiguration['beStrictAboutChangesToGlobalState'];
            }
            if (isset($phpunitConfiguration['bootstrap']) && !isset($arguments['bootstrap'])) {
                $arguments['bootstrap'] = $phpunitConfiguration['bootstrap'];
            }
            if (isset($phpunitConfiguration['cacheTokens']) && !isset($arguments['cacheTokens'])) {
                $arguments['cacheTokens'] = $phpunitConfiguration['cacheTokens'];
            }
            if (isset($phpunitConfiguration['colors']) && !isset($arguments['colors'])) {
                $arguments['colors'] = $phpunitConfiguration['colors'];
            }
            if (isset($phpunitConfiguration['convertDeprecationsToExceptions']) && !isset($arguments['convertDeprecationsToExceptions'])) {
                $arguments['convertDeprecationsToExceptions'] = $phpunitConfiguration['convertDeprecationsToExceptions'];
            }
            if (isset($phpunitConfiguration['convertErrorsToExceptions']) && !isset($arguments['convertErrorsToExceptions'])) {
                $arguments['convertErrorsToExceptions'] = $phpunitConfiguration['convertErrorsToExceptions'];
            }
            if (isset($phpunitConfiguration['convertNoticesToExceptions']) && !isset($arguments['convertNoticesToExceptions'])) {
                $arguments['convertNoticesToExceptions'] = $phpunitConfiguration['convertNoticesToExceptions'];
            }
            if (isset($phpunitConfiguration['convertWarningsToExceptions']) && !isset($arguments['convertWarningsToExceptions'])) {
                $arguments['convertWarningsToExceptions'] = $phpunitConfiguration['convertWarningsToExceptions'];
            }
            if (isset($phpunitConfiguration['processIsolation']) && !isset($arguments['processIsolation'])) {
                $arguments['processIsolation'] = $phpunitConfiguration['processIsolation'];
            }
            if (isset($phpunitConfiguration['stopOnError']) && !isset($arguments['stopOnError'])) {
                $arguments['stopOnError'] = $phpunitConfiguration['stopOnError'];
            }
            if (isset($phpunitConfiguration['stopOnFailure']) && !isset($arguments['stopOnFailure'])) {
                $arguments['stopOnFailure'] = $phpunitConfiguration['stopOnFailure'];
            }
            if (isset($phpunitConfiguration['stopOnWarning']) && !isset($arguments['stopOnWarning'])) {
                $arguments['stopOnWarning'] = $phpunitConfiguration['stopOnWarning'];
            }
            if (isset($phpunitConfiguration['stopOnIncomplete']) && !isset($arguments['stopOnIncomplete'])) {
                $arguments['stopOnIncomplete'] = $phpunitConfiguration['stopOnIncomplete'];
            }
            if (isset($phpunitConfiguration['stopOnRisky']) && !isset($arguments['stopOnRisky'])) {
                $arguments['stopOnRisky'] = $phpunitConfiguration['stopOnRisky'];
            }
            if (isset($phpunitConfiguration['stopOnSkipped']) && !isset($arguments['stopOnSkipped'])) {
                $arguments['stopOnSkipped'] = $phpunitConfiguration['stopOnSkipped'];
            }
            if (isset($phpunitConfiguration['failOnWarning']) && !isset($arguments['failOnWarning'])) {
                $arguments['failOnWarning'] = $phpunitConfiguration['failOnWarning'];
            }
            if (isset($phpunitConfiguration['failOnRisky']) && !isset($arguments['failOnRisky'])) {
                $arguments['failOnRisky'] = $phpunitConfiguration['failOnRisky'];
            }
            if (isset($phpunitConfiguration['timeoutForSmallTests']) && !isset($arguments['timeoutForSmallTests'])) {
                $arguments['timeoutForSmallTests'] = $phpunitConfiguration['timeoutForSmallTests'];
            }
            if (isset($phpunitConfiguration['timeoutForMediumTests']) && !isset($arguments['timeoutForMediumTests'])) {
                $arguments['timeoutForMediumTests'] = $phpunitConfiguration['timeoutForMediumTests'];
            }
            if (isset($phpunitConfiguration['timeoutForLargeTests']) && !isset($arguments['timeoutForLargeTests'])) {
                $arguments['timeoutForLargeTests'] = $phpunitConfiguration['timeoutForLargeTests'];
            }
            if (isset($phpunitConfiguration['reportUselessTests']) && !isset($arguments['reportUselessTests'])) {
                $arguments['reportUselessTests'] = $phpunitConfiguration['reportUselessTests'];
            }
            if (isset($phpunitConfiguration['strictCoverage']) && !isset($arguments['strictCoverage'])) {
                $arguments['strictCoverage'] = $phpunitConfiguration['strictCoverage'];
            }
            if (isset($phpunitConfiguration['ignoreDeprecatedCodeUnitsFromCodeCoverage']) && !isset($arguments['ignoreDeprecatedCodeUnitsFromCodeCoverage'])) {
                $arguments['ignoreDeprecatedCodeUnitsFromCodeCoverage'] = $phpunitConfiguration['ignoreDeprecatedCodeUnitsFromCodeCoverage'];
            }
            if (isset($phpunitConfiguration['disallowTestOutput']) && !isset($arguments['disallowTestOutput'])) {
                $arguments['disallowTestOutput'] = $phpunitConfiguration['disallowTestOutput'];
            }
            if (isset($phpunitConfiguration['enforceTimeLimit']) && !isset($arguments['enforceTimeLimit'])) {
                $arguments['enforceTimeLimit'] = $phpunitConfiguration['enforceTimeLimit'];
            }
            if (isset($phpunitConfiguration['disallowTodoAnnotatedTests']) && !isset($arguments['disallowTodoAnnotatedTests'])) {
                $arguments['disallowTodoAnnotatedTests'] = $phpunitConfiguration['disallowTodoAnnotatedTests'];
            }
            if (isset($phpunitConfiguration['beStrictAboutResourceUsageDuringSmallTests']) && !isset($arguments['beStrictAboutResourceUsageDuringSmallTests'])) {
                $arguments['beStrictAboutResourceUsageDuringSmallTests'] = $phpunitConfiguration['beStrictAboutResourceUsageDuringSmallTests'];
            }
            if (isset($phpunitConfiguration['verbose']) && !isset($arguments['verbose'])) {
                $arguments['verbose'] = $phpunitConfiguration['verbose'];
            }
            if (isset($phpunitConfiguration['reverseDefectList']) && !isset($arguments['reverseList'])) {
                $arguments['reverseList'] = $phpunitConfiguration['reverseDefectList'];
            }
            if (isset($phpunitConfiguration['forceCoversAnnotation']) && !isset($arguments['forceCoversAnnotation'])) {
                $arguments['forceCoversAnnotation'] = $phpunitConfiguration['forceCoversAnnotation'];
            }
            if (isset($phpunitConfiguration['disableCodeCoverageIgnore']) && !isset($arguments['disableCodeCoverageIgnore'])) {
                $arguments['disableCodeCoverageIgnore'] = $phpunitConfiguration['disableCodeCoverageIgnore'];
            }
            if (isset($phpunitConfiguration['registerMockObjectsFromTestArgumentsRecursively']) && !isset($arguments['registerMockObjectsFromTestArgumentsRecursively'])) {
                $arguments['registerMockObjectsFromTestArgumentsRecursively'] = $phpunitConfiguration['registerMockObjectsFromTestArgumentsRecursively'];
            }
            if (isset($phpunitConfiguration['executionOrder']) && !isset($arguments['executionOrder'])) {
                $arguments['executionOrder'] = $phpunitConfiguration['executionOrder'];
            }
            if (isset($phpunitConfiguration['resolveDependencies']) && !isset($arguments['resolveDependencies'])) {
                $arguments['resolveDependencies'] = $phpunitConfiguration['resolveDependencies'];
            }
            $groupCliArgs = [];
            if (!empty($arguments['groups'])) {
                $groupCliArgs = $arguments['groups'];
            }
            $groupConfiguration = $arguments['configuration']->getGroupConfiguration();
            if (!empty($groupConfiguration['include']) && !isset($arguments['groups'])) {
                $arguments['groups'] = $groupConfiguration['include'];
            }
            if (!empty($groupConfiguration['exclude']) && !isset($arguments['excludeGroups'])) {
                $arguments['excludeGroups'] = \array_diff($groupConfiguration['exclude'], $groupCliArgs);
            }
            foreach ($arguments['configuration']->getExtensionConfiguration() as $extension) {
                if (!\class_exists($extension['class'], \false) && $extension['file'] !== '') {
                    require_once $extension['file'];
                }
                if (!\class_exists($extension['class'])) {
                    throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception(\sprintf('Class "%s" does not exist', $extension['class']));
                }
                $extensionClass = new \ReflectionClass($extension['class']);
                if (!$extensionClass->implementsInterface(\_PhpScoper5b2c11ee6df50\PHPUnit\Runner\Hook::class)) {
                    throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception(\sprintf('_PhpScoper5b2c11ee6df50\\Class "%s" does not implement a PHPUnit\\Runner\\Hook interface', $extension['class']));
                }
                if (\count($extension['arguments']) == 0) {
                    $this->extensions[] = $extensionClass->newInstance();
                } else {
                    $this->extensions[] = $extensionClass->newInstanceArgs($extension['arguments']);
                }
            }
            foreach ($arguments['configuration']->getListenerConfiguration() as $listener) {
                if (!\class_exists($listener['class'], \false) && $listener['file'] !== '') {
                    require_once $listener['file'];
                }
                if (!\class_exists($listener['class'])) {
                    throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception(\sprintf('Class "%s" does not exist', $listener['class']));
                }
                $listenerClass = new \ReflectionClass($listener['class']);
                if (!$listenerClass->implementsInterface(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestListener::class)) {
                    throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception(\sprintf('_PhpScoper5b2c11ee6df50\\Class "%s" does not implement the PHPUnit\\Framework\\TestListener interface', $listener['class']));
                }
                if (\count($listener['arguments']) == 0) {
                    $listener = new $listener['class']();
                } else {
                    $listener = $listenerClass->newInstanceArgs($listener['arguments']);
                }
                $arguments['listeners'][] = $listener;
            }
            $loggingConfiguration = $arguments['configuration']->getLoggingConfiguration();
            if (isset($loggingConfiguration['coverage-clover']) && !isset($arguments['coverageClover'])) {
                $arguments['coverageClover'] = $loggingConfiguration['coverage-clover'];
            }
            if (isset($loggingConfiguration['coverage-crap4j']) && !isset($arguments['coverageCrap4J'])) {
                $arguments['coverageCrap4J'] = $loggingConfiguration['coverage-crap4j'];
                if (isset($loggingConfiguration['crap4jThreshold']) && !isset($arguments['crap4jThreshold'])) {
                    $arguments['crap4jThreshold'] = $loggingConfiguration['crap4jThreshold'];
                }
            }
            if (isset($loggingConfiguration['coverage-html']) && !isset($arguments['coverageHtml'])) {
                if (isset($loggingConfiguration['lowUpperBound']) && !isset($arguments['reportLowUpperBound'])) {
                    $arguments['reportLowUpperBound'] = $loggingConfiguration['lowUpperBound'];
                }
                if (isset($loggingConfiguration['highLowerBound']) && !isset($arguments['reportHighLowerBound'])) {
                    $arguments['reportHighLowerBound'] = $loggingConfiguration['highLowerBound'];
                }
                $arguments['coverageHtml'] = $loggingConfiguration['coverage-html'];
            }
            if (isset($loggingConfiguration['coverage-php']) && !isset($arguments['coveragePHP'])) {
                $arguments['coveragePHP'] = $loggingConfiguration['coverage-php'];
            }
            if (isset($loggingConfiguration['coverage-text']) && !isset($arguments['coverageText'])) {
                $arguments['coverageText'] = $loggingConfiguration['coverage-text'];
                if (isset($loggingConfiguration['coverageTextShowUncoveredFiles'])) {
                    $arguments['coverageTextShowUncoveredFiles'] = $loggingConfiguration['coverageTextShowUncoveredFiles'];
                } else {
                    $arguments['coverageTextShowUncoveredFiles'] = \false;
                }
                if (isset($loggingConfiguration['coverageTextShowOnlySummary'])) {
                    $arguments['coverageTextShowOnlySummary'] = $loggingConfiguration['coverageTextShowOnlySummary'];
                } else {
                    $arguments['coverageTextShowOnlySummary'] = \false;
                }
            }
            if (isset($loggingConfiguration['coverage-xml']) && !isset($arguments['coverageXml'])) {
                $arguments['coverageXml'] = $loggingConfiguration['coverage-xml'];
            }
            if (isset($loggingConfiguration['plain'])) {
                $arguments['listeners'][] = new \_PhpScoper5b2c11ee6df50\PHPUnit\TextUI\ResultPrinter($loggingConfiguration['plain'], \true);
            }
            if (isset($loggingConfiguration['teamcity']) && !isset($arguments['teamcityLogfile'])) {
                $arguments['teamcityLogfile'] = $loggingConfiguration['teamcity'];
            }
            if (isset($loggingConfiguration['junit']) && !isset($arguments['junitLogfile'])) {
                $arguments['junitLogfile'] = $loggingConfiguration['junit'];
            }
            if (isset($loggingConfiguration['testdox-html']) && !isset($arguments['testdoxHTMLFile'])) {
                $arguments['testdoxHTMLFile'] = $loggingConfiguration['testdox-html'];
            }
            if (isset($loggingConfiguration['testdox-text']) && !isset($arguments['testdoxTextFile'])) {
                $arguments['testdoxTextFile'] = $loggingConfiguration['testdox-text'];
            }
            if (isset($loggingConfiguration['testdox-xml']) && !isset($arguments['testdoxXMLFile'])) {
                $arguments['testdoxXMLFile'] = $loggingConfiguration['testdox-xml'];
            }
            $testdoxGroupConfiguration = $arguments['configuration']->getTestdoxGroupConfiguration();
            if (isset($testdoxGroupConfiguration['include']) && !isset($arguments['testdoxGroups'])) {
                $arguments['testdoxGroups'] = $testdoxGroupConfiguration['include'];
            }
            if (isset($testdoxGroupConfiguration['exclude']) && !isset($arguments['testdoxExcludeGroups'])) {
                $arguments['testdoxExcludeGroups'] = $testdoxGroupConfiguration['exclude'];
            }
        }
        $arguments['addUncoveredFilesFromWhitelist'] = $arguments['addUncoveredFilesFromWhitelist'] ?? \true;
        $arguments['backupGlobals'] = $arguments['backupGlobals'] ?? null;
        $arguments['backupStaticAttributes'] = $arguments['backupStaticAttributes'] ?? null;
        $arguments['beStrictAboutChangesToGlobalState'] = $arguments['beStrictAboutChangesToGlobalState'] ?? null;
        $arguments['beStrictAboutResourceUsageDuringSmallTests'] = $arguments['beStrictAboutResourceUsageDuringSmallTests'] ?? \false;
        $arguments['cacheTokens'] = $arguments['cacheTokens'] ?? \false;
        $arguments['colors'] = $arguments['colors'] ?? \_PhpScoper5b2c11ee6df50\PHPUnit\TextUI\ResultPrinter::COLOR_DEFAULT;
        $arguments['columns'] = $arguments['columns'] ?? 80;
        $arguments['convertDeprecationsToExceptions'] = $arguments['convertDeprecationsToExceptions'] ?? \true;
        $arguments['convertErrorsToExceptions'] = $arguments['convertErrorsToExceptions'] ?? \true;
        $arguments['convertNoticesToExceptions'] = $arguments['convertNoticesToExceptions'] ?? \true;
        $arguments['convertWarningsToExceptions'] = $arguments['convertWarningsToExceptions'] ?? \true;
        $arguments['crap4jThreshold'] = $arguments['crap4jThreshold'] ?? 30;
        $arguments['disallowTestOutput'] = $arguments['disallowTestOutput'] ?? \false;
        $arguments['disallowTodoAnnotatedTests'] = $arguments['disallowTodoAnnotatedTests'] ?? \false;
        $arguments['enforceTimeLimit'] = $arguments['enforceTimeLimit'] ?? \false;
        $arguments['excludeGroups'] = $arguments['excludeGroups'] ?? [];
        $arguments['failOnRisky'] = $arguments['failOnRisky'] ?? \false;
        $arguments['failOnWarning'] = $arguments['failOnWarning'] ?? \false;
        $arguments['groups'] = $arguments['groups'] ?? [];
        $arguments['processIsolation'] = $arguments['processIsolation'] ?? \false;
        $arguments['processUncoveredFilesFromWhitelist'] = $arguments['processUncoveredFilesFromWhitelist'] ?? \false;
        $arguments['randomOrderSeed'] = $arguments['randomOrderSeed'] ?? \time();
        $arguments['registerMockObjectsFromTestArgumentsRecursively'] = $arguments['registerMockObjectsFromTestArgumentsRecursively'] ?? \false;
        $arguments['repeat'] = $arguments['repeat'] ?? \false;
        $arguments['reportHighLowerBound'] = $arguments['reportHighLowerBound'] ?? 90;
        $arguments['reportLowUpperBound'] = $arguments['reportLowUpperBound'] ?? 50;
        $arguments['reportUselessTests'] = $arguments['reportUselessTests'] ?? \true;
        $arguments['reverseList'] = $arguments['reverseList'] ?? \false;
        $arguments['executionOrder'] = $arguments['executionOrder'] ?? \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestSuiteSorter::ORDER_DEFAULT;
        $arguments['resolveDependencies'] = $arguments['resolveDependencies'] ?? \false;
        $arguments['stopOnError'] = $arguments['stopOnError'] ?? \false;
        $arguments['stopOnFailure'] = $arguments['stopOnFailure'] ?? \false;
        $arguments['stopOnIncomplete'] = $arguments['stopOnIncomplete'] ?? \false;
        $arguments['stopOnRisky'] = $arguments['stopOnRisky'] ?? \false;
        $arguments['stopOnSkipped'] = $arguments['stopOnSkipped'] ?? \false;
        $arguments['stopOnWarning'] = $arguments['stopOnWarning'] ?? \false;
        $arguments['strictCoverage'] = $arguments['strictCoverage'] ?? \false;
        $arguments['testdoxExcludeGroups'] = $arguments['testdoxExcludeGroups'] ?? [];
        $arguments['testdoxGroups'] = $arguments['testdoxGroups'] ?? [];
        $arguments['timeoutForLargeTests'] = $arguments['timeoutForLargeTests'] ?? 60;
        $arguments['timeoutForMediumTests'] = $arguments['timeoutForMediumTests'] ?? 10;
        $arguments['timeoutForSmallTests'] = $arguments['timeoutForSmallTests'] ?? 1;
        $arguments['verbose'] = $arguments['verbose'] ?? \false;
    }
    /**
     * @throws \ReflectionException
     * @throws \InvalidArgumentException
     */
    private function processSuiteFilters(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite $suite, array $arguments) : void
    {
        if (!$arguments['filter'] && empty($arguments['groups']) && empty($arguments['excludeGroups'])) {
            return;
        }
        $filterFactory = new \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\Filter\Factory();
        if (!empty($arguments['excludeGroups'])) {
            $filterFactory->addFilter(new \ReflectionClass(\_PhpScoper5b2c11ee6df50\PHPUnit\Runner\Filter\ExcludeGroupFilterIterator::class), $arguments['excludeGroups']);
        }
        if (!empty($arguments['groups'])) {
            $filterFactory->addFilter(new \ReflectionClass(\_PhpScoper5b2c11ee6df50\PHPUnit\Runner\Filter\IncludeGroupFilterIterator::class), $arguments['groups']);
        }
        if ($arguments['filter']) {
            $filterFactory->addFilter(new \ReflectionClass(\_PhpScoper5b2c11ee6df50\PHPUnit\Runner\Filter\NameFilterIterator::class), $arguments['filter']);
        }
        $suite->injectFilter($filterFactory);
    }
    private function writeMessage(string $type, string $message) : void
    {
        if (!$this->messagePrinted) {
            $this->write("\n");
        }
        $this->write(\sprintf("%-15s%s\n", $type . ':', $message));
        $this->messagePrinted = \true;
    }
}
