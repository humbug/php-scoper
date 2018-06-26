<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Runner;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\FileLoader;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\Filesystem;
use ReflectionClass;
/**
 * The standard test suite loader.
 */
class StandardTestSuiteLoader implements \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestSuiteLoader
{
    /**
     * @throws Exception
     * @throws \PHPUnit\Framework\Exception
     */
    public function load(string $suiteClassName, string $suiteClassFile = '') : \ReflectionClass
    {
        $suiteClassName = \str_replace('.php', '', $suiteClassName);
        if (empty($suiteClassFile)) {
            $suiteClassFile = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Filesystem::classNameToFilename($suiteClassName);
        }
        if (!\class_exists($suiteClassName, \false)) {
            $loadedClasses = \get_declared_classes();
            $filename = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\FileLoader::checkAndLoad($suiteClassFile);
            $loadedClasses = \array_values(\array_diff(\get_declared_classes(), $loadedClasses));
        }
        if (!\class_exists($suiteClassName, \false) && !empty($loadedClasses)) {
            $offset = 0 - \strlen($suiteClassName);
            foreach ($loadedClasses as $loadedClass) {
                $class = new \ReflectionClass($loadedClass);
                if (\substr($loadedClass, $offset) === $suiteClassName && $class->getFileName() == $filename) {
                    $suiteClassName = $loadedClass;
                    break;
                }
            }
        }
        if (!\class_exists($suiteClassName, \false) && !empty($loadedClasses)) {
            $testCaseClass = \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase::class;
            foreach ($loadedClasses as $loadedClass) {
                $class = new \ReflectionClass($loadedClass);
                $classFile = $class->getFileName();
                if ($class->isSubclassOf($testCaseClass) && !$class->isAbstract()) {
                    $suiteClassName = $loadedClass;
                    $testCaseClass = $loadedClass;
                    if ($classFile == \realpath($suiteClassFile)) {
                        break;
                    }
                }
                if ($class->hasMethod('suite')) {
                    $method = $class->getMethod('suite');
                    if (!$method->isAbstract() && $method->isPublic() && $method->isStatic()) {
                        $suiteClassName = $loadedClass;
                        if ($classFile == \realpath($suiteClassFile)) {
                            break;
                        }
                    }
                }
            }
        }
        if (\class_exists($suiteClassName, \false)) {
            $class = new \ReflectionClass($suiteClassName);
            if ($class->getFileName() == \realpath($suiteClassFile)) {
                return $class;
            }
        }
        throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\Exception(\sprintf("Class '%s' could not be found in '%s'.", $suiteClassName, $suiteClassFile));
    }
    public function reload(\ReflectionClass $aClass) : \ReflectionClass
    {
        return $aClass;
    }
}
