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
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Util;
/**
 * Generates human readable output from a code coverage object.
 *
 * The output gets put into a text file our written to the CLI.
 */
final class Text
{
    /**
     * @var string
     */
    private const COLOR_GREEN = "\33[30;42m";
    /**
     * @var string
     */
    private const COLOR_YELLOW = "\33[30;43m";
    /**
     * @var string
     */
    private const COLOR_RED = "\33[37;41m";
    /**
     * @var string
     */
    private const COLOR_HEADER = "\33[1;37;40m";
    /**
     * @var string
     */
    private const COLOR_RESET = "\33[0m";
    /**
     * @var string
     */
    private const COLOR_EOL = "\33[2K";
    /**
     * @var int
     */
    private $lowUpperBound;
    /**
     * @var int
     */
    private $highLowerBound;
    /**
     * @var bool
     */
    private $showUncoveredFiles;
    /**
     * @var bool
     */
    private $showOnlySummary;
    public function __construct(int $lowUpperBound = 50, int $highLowerBound = 90, bool $showUncoveredFiles = \false, bool $showOnlySummary = \false)
    {
        $this->lowUpperBound = $lowUpperBound;
        $this->highLowerBound = $highLowerBound;
        $this->showUncoveredFiles = $showUncoveredFiles;
        $this->showOnlySummary = $showOnlySummary;
    }
    public function process(\_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\CodeCoverage $coverage, bool $showColors = \false) : string
    {
        $output = \PHP_EOL . \PHP_EOL;
        $report = $coverage->getReport();
        $colors = ['header' => '', 'classes' => '', 'methods' => '', 'lines' => '', 'reset' => '', 'eol' => ''];
        if ($showColors) {
            $colors['classes'] = $this->getCoverageColor($report->getNumTestedClassesAndTraits(), $report->getNumClassesAndTraits());
            $colors['methods'] = $this->getCoverageColor($report->getNumTestedMethods(), $report->getNumMethods());
            $colors['lines'] = $this->getCoverageColor($report->getNumExecutedLines(), $report->getNumExecutableLines());
            $colors['reset'] = self::COLOR_RESET;
            $colors['header'] = self::COLOR_HEADER;
            $colors['eol'] = self::COLOR_EOL;
        }
        $classes = \sprintf('  Classes: %6s (%d/%d)', \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Util::percent($report->getNumTestedClassesAndTraits(), $report->getNumClassesAndTraits(), \true), $report->getNumTestedClassesAndTraits(), $report->getNumClassesAndTraits());
        $methods = \sprintf('  Methods: %6s (%d/%d)', \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Util::percent($report->getNumTestedMethods(), $report->getNumMethods(), \true), $report->getNumTestedMethods(), $report->getNumMethods());
        $lines = \sprintf('  Lines:   %6s (%d/%d)', \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Util::percent($report->getNumExecutedLines(), $report->getNumExecutableLines(), \true), $report->getNumExecutedLines(), $report->getNumExecutableLines());
        $padding = \max(\array_map('strlen', [$classes, $methods, $lines]));
        if ($this->showOnlySummary) {
            $title = 'Code Coverage Report Summary:';
            $padding = \max($padding, \strlen($title));
            $output .= $this->format($colors['header'], $padding, $title);
        } else {
            $date = \date('  Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            $title = 'Code Coverage Report:';
            $output .= $this->format($colors['header'], $padding, $title);
            $output .= $this->format($colors['header'], $padding, $date);
            $output .= $this->format($colors['header'], $padding, '');
            $output .= $this->format($colors['header'], $padding, ' Summary:');
        }
        $output .= $this->format($colors['classes'], $padding, $classes);
        $output .= $this->format($colors['methods'], $padding, $methods);
        $output .= $this->format($colors['lines'], $padding, $lines);
        if ($this->showOnlySummary) {
            return $output . \PHP_EOL;
        }
        $classCoverage = [];
        foreach ($report as $item) {
            if (!$item instanceof \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Node\File) {
                continue;
            }
            $classes = $item->getClassesAndTraits();
            foreach ($classes as $className => $class) {
                $classStatements = 0;
                $coveredClassStatements = 0;
                $coveredMethods = 0;
                $classMethods = 0;
                foreach ($class['methods'] as $method) {
                    if ($method['executableLines'] == 0) {
                        continue;
                    }
                    $classMethods++;
                    $classStatements += $method['executableLines'];
                    $coveredClassStatements += $method['executedLines'];
                    if ($method['coverage'] == 100) {
                        $coveredMethods++;
                    }
                }
                $namespace = '';
                if (!empty($class['package']['namespace'])) {
                    $namespace = '\\' . $class['package']['namespace'] . '::';
                } elseif (!empty($class['package']['fullPackage'])) {
                    $namespace = '@' . $class['package']['fullPackage'] . '::';
                }
                $classCoverage[$namespace . $className] = ['namespace' => $namespace, 'className ' => $className, 'methodsCovered' => $coveredMethods, 'methodCount' => $classMethods, 'statementsCovered' => $coveredClassStatements, 'statementCount' => $classStatements];
            }
        }
        \ksort($classCoverage);
        $methodColor = '';
        $linesColor = '';
        $resetColor = '';
        foreach ($classCoverage as $fullQualifiedPath => $classInfo) {
            if ($this->showUncoveredFiles || $classInfo['statementsCovered'] != 0) {
                if ($showColors) {
                    $methodColor = $this->getCoverageColor($classInfo['methodsCovered'], $classInfo['methodCount']);
                    $linesColor = $this->getCoverageColor($classInfo['statementsCovered'], $classInfo['statementCount']);
                    $resetColor = $colors['reset'];
                }
                $output .= \PHP_EOL . $fullQualifiedPath . \PHP_EOL . '  ' . $methodColor . 'Methods: ' . $this->printCoverageCounts($classInfo['methodsCovered'], $classInfo['methodCount'], 2) . $resetColor . ' ' . '  ' . $linesColor . 'Lines: ' . $this->printCoverageCounts($classInfo['statementsCovered'], $classInfo['statementCount'], 3) . $resetColor;
            }
        }
        return $output . \PHP_EOL;
    }
    private function getCoverageColor(int $numberOfCoveredElements, int $totalNumberOfElements) : string
    {
        $coverage = \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Util::percent($numberOfCoveredElements, $totalNumberOfElements);
        if ($coverage >= $this->highLowerBound) {
            return self::COLOR_GREEN;
        }
        if ($coverage > $this->lowUpperBound) {
            return self::COLOR_YELLOW;
        }
        return self::COLOR_RED;
    }
    private function printCoverageCounts(int $numberOfCoveredElements, int $totalNumberOfElements, int $precision) : string
    {
        $format = '%' . $precision . 's';
        return \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\Util::percent($numberOfCoveredElements, $totalNumberOfElements, \true, \true) . ' (' . \sprintf($format, $numberOfCoveredElements) . '/' . \sprintf($format, $totalNumberOfElements) . ')';
    }
    private function format($color, $padding, $string) : string
    {
        $reset = $color ? self::COLOR_RESET : '';
        return $color . \str_pad($string, $padding) . $reset . \PHP_EOL;
    }
}
