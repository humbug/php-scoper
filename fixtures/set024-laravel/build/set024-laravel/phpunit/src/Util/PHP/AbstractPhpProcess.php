<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Util\PHP;

use __PHP_Incomplete_Class;
use ErrorException;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\SyntheticError;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestFailure;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestResult;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\Environment\Runtime;
/**
 * Utility methods for PHP sub-processes.
 */
abstract class AbstractPhpProcess
{
    /**
     * @var Runtime
     */
    protected $runtime;
    /**
     * @var bool
     */
    protected $stderrRedirection = \false;
    /**
     * @var string
     */
    protected $stdin = '';
    /**
     * @var string
     */
    protected $args = '';
    /**
     * @var array<string, string>
     */
    protected $env = [];
    /**
     * @var int
     */
    protected $timeout = 0;
    public static function factory() : self
    {
        if (\DIRECTORY_SEPARATOR === '\\') {
            return new \_PhpScoper5b2c11ee6df50\PHPUnit\Util\PHP\WindowsPhpProcess();
        }
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Util\PHP\DefaultPhpProcess();
    }
    public function __construct()
    {
        $this->runtime = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\Environment\Runtime();
    }
    /**
     * Defines if should use STDERR redirection or not.
     *
     * Then $stderrRedirection is TRUE, STDERR is redirected to STDOUT.
     */
    public function setUseStderrRedirection(bool $stderrRedirection) : void
    {
        $this->stderrRedirection = $stderrRedirection;
    }
    /**
     * Returns TRUE if uses STDERR redirection or FALSE if not.
     */
    public function useStderrRedirection() : bool
    {
        return $this->stderrRedirection;
    }
    /**
     * Sets the input string to be sent via STDIN
     */
    public function setStdin(string $stdin) : void
    {
        $this->stdin = $stdin;
    }
    /**
     * Returns the input string to be sent via STDIN
     */
    public function getStdin() : string
    {
        return $this->stdin;
    }
    /**
     * Sets the string of arguments to pass to the php job
     */
    public function setArgs(string $args) : void
    {
        $this->args = $args;
    }
    /**
     * Returns the string of arguments to pass to the php job
     */
    public function getArgs() : string
    {
        return $this->args;
    }
    /**
     * Sets the array of environment variables to start the child process with
     *
     * @param array<string, string> $env
     */
    public function setEnv(array $env) : void
    {
        $this->env = $env;
    }
    /**
     * Returns the array of environment variables to start the child process with
     */
    public function getEnv() : array
    {
        return $this->env;
    }
    /**
     * Sets the amount of seconds to wait before timing out
     */
    public function setTimeout(int $timeout) : void
    {
        $this->timeout = $timeout;
    }
    /**
     * Returns the amount of seconds to wait before timing out
     */
    public function getTimeout() : int
    {
        return $this->timeout;
    }
    /**
     * Runs a single test in a separate PHP process.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function runTestJob(string $job, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestResult $result) : void
    {
        $result->startTest($test);
        $_result = $this->runJob($job);
        $this->processChildResult($test, $result, $_result['stdout'], $_result['stderr']);
    }
    /**
     * Returns the command based into the configurations.
     */
    public function getCommand(array $settings, string $file = null) : string
    {
        $command = $this->runtime->getBinary();
        $command .= $this->settingsToParameters($settings);
        if (\PHP_SAPI === 'phpdbg') {
            $command .= ' -qrr ';
            if ($file) {
                $command .= '-e ' . \escapeshellarg($file);
            } else {
                $command .= \escapeshellarg(__DIR__ . '/eval-stdin.php');
            }
        } elseif ($file) {
            $command .= ' -f ' . \escapeshellarg($file);
        }
        if ($this->args) {
            $command .= ' -- ' . $this->args;
        }
        if ($this->stderrRedirection === \true) {
            $command .= ' 2>&1';
        }
        return $command;
    }
    /**
     * Runs a single job (PHP code) using a separate PHP process.
     */
    public abstract function runJob(string $job, array $settings = []) : array;
    protected function settingsToParameters(array $settings) : string
    {
        $buffer = '';
        foreach ($settings as $setting) {
            $buffer .= ' -d ' . \escapeshellarg($setting);
        }
        return $buffer;
    }
    /**
     * Processes the TestResult object from an isolated process.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    private function processChildResult(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestResult $result, string $stdout, string $stderr) : void
    {
        $time = 0;
        if (!empty($stderr)) {
            $result->addError($test, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception(\trim($stderr)), $time);
        } else {
            \set_error_handler(function ($errno, $errstr, $errfile, $errline) : void {
                throw new \ErrorException($errstr, $errno, $errno, $errfile, $errline);
            });
            try {
                if (\strpos($stdout, "#!/usr/bin/env php\n") === 0) {
                    $stdout = \substr($stdout, 19);
                }
                $childResult = \unserialize(\str_replace("#!/usr/bin/env php\n", '', $stdout));
                \restore_error_handler();
            } catch (\ErrorException $e) {
                \restore_error_handler();
                $childResult = \false;
                $result->addError($test, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception(\trim($stdout), 0, $e), $time);
            }
            if ($childResult !== \false) {
                if (!empty($childResult['output'])) {
                    $output = $childResult['output'];
                }
                /* @var TestCase $test */
                $test->setResult($childResult['testResult']);
                $test->addToAssertionCount($childResult['numAssertions']);
                /** @var TestResult $childResult */
                $childResult = $childResult['result'];
                if ($result->getCollectCodeCoverageInformation()) {
                    $result->getCodeCoverage()->merge($childResult->getCodeCoverage());
                }
                $time = $childResult->time();
                $notImplemented = $childResult->notImplemented();
                $risky = $childResult->risky();
                $skipped = $childResult->skipped();
                $errors = $childResult->errors();
                $warnings = $childResult->warnings();
                $failures = $childResult->failures();
                if (!empty($notImplemented)) {
                    $result->addError($test, $this->getException($notImplemented[0]), $time);
                } elseif (!empty($risky)) {
                    $result->addError($test, $this->getException($risky[0]), $time);
                } elseif (!empty($skipped)) {
                    $result->addError($test, $this->getException($skipped[0]), $time);
                } elseif (!empty($errors)) {
                    $result->addError($test, $this->getException($errors[0]), $time);
                } elseif (!empty($warnings)) {
                    $result->addWarning($test, $this->getException($warnings[0]), $time);
                } elseif (!empty($failures)) {
                    $result->addFailure($test, $this->getException($failures[0]), $time);
                }
            }
        }
        $result->endTest($test, $time);
        if (!empty($output)) {
            print $output;
        }
    }
    /**
     * Gets the thrown exception from a PHPUnit\Framework\TestFailure.
     *
     * @see https://github.com/sebastianbergmann/phpunit/issues/74
     */
    private function getException(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestFailure $error) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception
    {
        $exception = $error->thrownException();
        if ($exception instanceof \__PHP_Incomplete_Class) {
            $exceptionArray = [];
            foreach ((array) $exception as $key => $value) {
                $key = \substr($key, \strrpos($key, "\0") + 1);
                $exceptionArray[$key] = $value;
            }
            $exception = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\SyntheticError(\sprintf('%s: %s', $exceptionArray['_PHP_Incomplete_Class_Name'], $exceptionArray['message']), $exceptionArray['code'], $exceptionArray['file'], $exceptionArray['line'], $exceptionArray['trace']);
        }
        return $exception;
    }
}
