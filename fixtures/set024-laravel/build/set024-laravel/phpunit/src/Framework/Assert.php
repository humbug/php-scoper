<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Framework;

use ArrayAccess;
use Countable;
use DOMDocument;
use DOMElement;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ArrayHasKey;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ArraySubset;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Attribute;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Callback;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ClassHasAttribute;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ClassHasStaticAttribute;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Constraint;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Count;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\DirectoryExists;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\FileExists;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\GreaterThan;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsAnything;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsEmpty;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsEqual;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsFalse;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsFinite;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsIdentical;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsInfinite;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsInstanceOf;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsJson;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsNan;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsNull;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsReadable;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsTrue;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsType;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsWritable;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\JsonMatches;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LessThan;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalAnd;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalOr;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalXor;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ObjectHasAttribute;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\RegularExpression;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\SameSize;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringContains;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringEndsWith;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringMatchesFormatDescription;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringStartsWith;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContains;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContainsOnly;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\Type;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use ReflectionProperty;
use Traversable;
/**
 * A set of assertion methods.
 */
abstract class Assert
{
    /**
     * @var int
     */
    private static $count = 0;
    /**
     * Asserts that an array has a specified key.
     *
     * @param int|string        $key
     * @param array|ArrayAccess $array
     * @param string            $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertArrayHasKey($key, $array, string $message = '') : void
    {
        if (!(\is_int($key) || \is_string($key))) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'integer or string');
        }
        if (!(\is_array($array) || $array instanceof \ArrayAccess)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'array or ArrayAccess');
        }
        $constraint = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ArrayHasKey($key);
        static::assertThat($array, $constraint, $message);
    }
    /**
     * Asserts that an array has a specified subset.
     *
     * @param array|ArrayAccess $subset
     * @param array|ArrayAccess $array
     * @param bool              $strict  Check for object identity
     * @param string            $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertArraySubset($subset, $array, bool $strict = \false, string $message = '') : void
    {
        if (!(\is_array($subset) || $subset instanceof \ArrayAccess)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'array or ArrayAccess');
        }
        if (!(\is_array($array) || $array instanceof \ArrayAccess)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'array or ArrayAccess');
        }
        $constraint = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ArraySubset($subset, $strict);
        static::assertThat($array, $constraint, $message);
    }
    /**
     * Asserts that an array does not have a specified key.
     *
     * @param int|string        $key
     * @param array|ArrayAccess $array
     * @param string            $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertArrayNotHasKey($key, $array, string $message = '') : void
    {
        if (!(\is_int($key) || \is_string($key))) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'integer or string');
        }
        if (!(\is_array($array) || $array instanceof \ArrayAccess)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'array or ArrayAccess');
        }
        $constraint = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ArrayHasKey($key));
        static::assertThat($array, $constraint, $message);
    }
    /**
     * Asserts that a haystack contains a needle.
     *
     * @param mixed  $needle
     * @param mixed  $haystack
     * @param string $message
     * @param bool   $ignoreCase
     * @param bool   $checkForObjectIdentity
     * @param bool   $checkForNonObjectIdentity
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertContains($needle, $haystack, string $message = '', bool $ignoreCase = \false, bool $checkForObjectIdentity = \true, bool $checkForNonObjectIdentity = \false) : void
    {
        if (\is_array($haystack) || \is_object($haystack) && $haystack instanceof \Traversable) {
            $constraint = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContains($needle, $checkForObjectIdentity, $checkForNonObjectIdentity);
        } elseif (\is_string($haystack)) {
            if (!\is_string($needle)) {
                throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'string');
            }
            $constraint = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringContains($needle, $ignoreCase);
        } else {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'array, traversable or string');
        }
        static::assertThat($haystack, $constraint, $message);
    }
    /**
     * Asserts that a haystack that is stored in a static attribute of a class
     * or an attribute of an object contains a needle.
     *
     * @param mixed         $needle
     * @param string        $haystackAttributeName
     * @param object|string $haystackClassOrObject
     * @param string        $message
     * @param bool          $ignoreCase
     * @param bool          $checkForObjectIdentity
     * @param bool          $checkForNonObjectIdentity
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeContains($needle, string $haystackAttributeName, $haystackClassOrObject, string $message = '', bool $ignoreCase = \false, bool $checkForObjectIdentity = \true, bool $checkForNonObjectIdentity = \false) : void
    {
        static::assertContains($needle, static::readAttribute($haystackClassOrObject, $haystackAttributeName), $message, $ignoreCase, $checkForObjectIdentity, $checkForNonObjectIdentity);
    }
    /**
     * Asserts that a haystack does not contain a needle.
     *
     * @param mixed  $needle
     * @param mixed  $haystack
     * @param string $message
     * @param bool   $ignoreCase
     * @param bool   $checkForObjectIdentity
     * @param bool   $checkForNonObjectIdentity
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotContains($needle, $haystack, string $message = '', bool $ignoreCase = \false, bool $checkForObjectIdentity = \true, bool $checkForNonObjectIdentity = \false) : void
    {
        if (\is_array($haystack) || \is_object($haystack) && $haystack instanceof \Traversable) {
            $constraint = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContains($needle, $checkForObjectIdentity, $checkForNonObjectIdentity));
        } elseif (\is_string($haystack)) {
            if (!\is_string($needle)) {
                throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'string');
            }
            $constraint = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringContains($needle, $ignoreCase));
        } else {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'array, traversable or string');
        }
        static::assertThat($haystack, $constraint, $message);
    }
    /**
     * Asserts that a haystack that is stored in a static attribute of a class
     * or an attribute of an object does not contain a needle.
     *
     * @param mixed         $needle
     * @param string        $haystackAttributeName
     * @param object|string $haystackClassOrObject
     * @param string        $message
     * @param bool          $ignoreCase
     * @param bool          $checkForObjectIdentity
     * @param bool          $checkForNonObjectIdentity
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeNotContains($needle, string $haystackAttributeName, $haystackClassOrObject, string $message = '', bool $ignoreCase = \false, bool $checkForObjectIdentity = \true, bool $checkForNonObjectIdentity = \false) : void
    {
        static::assertNotContains($needle, static::readAttribute($haystackClassOrObject, $haystackAttributeName), $message, $ignoreCase, $checkForObjectIdentity, $checkForNonObjectIdentity);
    }
    /**
     * Asserts that a haystack contains only values of a given type.
     *
     * @param string    $type
     * @param iterable  $haystack
     * @param null|bool $isNativeType
     * @param string    $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertContainsOnly(string $type, iterable $haystack, ?bool $isNativeType = null, string $message = '') : void
    {
        if ($isNativeType === null) {
            $isNativeType = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Type::isType($type);
        }
        static::assertThat($haystack, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContainsOnly($type, $isNativeType), $message);
    }
    /**
     * Asserts that a haystack contains only instances of a given class name.
     *
     * @param string   $className
     * @param iterable $haystack
     * @param string   $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertContainsOnlyInstancesOf(string $className, iterable $haystack, string $message = '') : void
    {
        static::assertThat($haystack, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContainsOnly($className, \false), $message);
    }
    /**
     * Asserts that a haystack that is stored in a static attribute of a class
     * or an attribute of an object contains only values of a given type.
     *
     * @param string        $type
     * @param string        $haystackAttributeName
     * @param object|string $haystackClassOrObject
     * @param bool          $isNativeType
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeContainsOnly(string $type, string $haystackAttributeName, $haystackClassOrObject, ?bool $isNativeType = null, string $message = '') : void
    {
        static::assertContainsOnly($type, static::readAttribute($haystackClassOrObject, $haystackAttributeName), $isNativeType, $message);
    }
    /**
     * Asserts that a haystack does not contain only values of a given type.
     *
     * @param string    $type
     * @param iterable  $haystack
     * @param null|bool $isNativeType
     * @param string    $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotContainsOnly(string $type, iterable $haystack, ?bool $isNativeType = null, string $message = '') : void
    {
        if ($isNativeType === null) {
            $isNativeType = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Type::isType($type);
        }
        static::assertThat($haystack, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContainsOnly($type, $isNativeType)), $message);
    }
    /**
     * Asserts that a haystack that is stored in a static attribute of a class
     * or an attribute of an object does not contain only values of a given
     * type.
     *
     * @param string        $type
     * @param string        $haystackAttributeName
     * @param object|string $haystackClassOrObject
     * @param bool          $isNativeType
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeNotContainsOnly(string $type, string $haystackAttributeName, $haystackClassOrObject, ?bool $isNativeType = null, string $message = '') : void
    {
        static::assertNotContainsOnly($type, static::readAttribute($haystackClassOrObject, $haystackAttributeName), $isNativeType, $message);
    }
    /**
     * Asserts the number of elements of an array, Countable or Traversable.
     *
     * @param int                $expectedCount
     * @param Countable|iterable $haystack
     * @param string             $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertCount(int $expectedCount, $haystack, string $message = '') : void
    {
        if (!$haystack instanceof \Countable && !\is_iterable($haystack)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'countable or iterable');
        }
        static::assertThat($haystack, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Count($expectedCount), $message);
    }
    /**
     * Asserts the number of elements of an array, Countable or Traversable
     * that is stored in an attribute.
     *
     * @param int           $expectedCount
     * @param string        $haystackAttributeName
     * @param object|string $haystackClassOrObject
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeCount(int $expectedCount, string $haystackAttributeName, $haystackClassOrObject, string $message = '') : void
    {
        static::assertCount($expectedCount, static::readAttribute($haystackClassOrObject, $haystackAttributeName), $message);
    }
    /**
     * Asserts the number of elements of an array, Countable or Traversable.
     *
     * @param int                $expectedCount
     * @param Countable|iterable $haystack
     * @param string             $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotCount(int $expectedCount, $haystack, string $message = '') : void
    {
        if (!$haystack instanceof \Countable && !\is_iterable($haystack)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'countable or iterable');
        }
        $constraint = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Count($expectedCount));
        static::assertThat($haystack, $constraint, $message);
    }
    /**
     * Asserts the number of elements of an array, Countable or Traversable
     * that is stored in an attribute.
     *
     * @param int           $expectedCount
     * @param string        $haystackAttributeName
     * @param object|string $haystackClassOrObject
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeNotCount(int $expectedCount, string $haystackAttributeName, $haystackClassOrObject, string $message = '') : void
    {
        static::assertNotCount($expectedCount, static::readAttribute($haystackClassOrObject, $haystackAttributeName), $message);
    }
    /**
     * Asserts that two variables are equal.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     * @param float  $delta
     * @param int    $maxDepth
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertEquals($expected, $actual, string $message = '', float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = \false, bool $ignoreCase = \false) : void
    {
        $constraint = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsEqual($expected, $delta, $maxDepth, $canonicalize, $ignoreCase);
        static::assertThat($actual, $constraint, $message);
    }
    /**
     * Asserts that a variable is equal to an attribute of an object.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param object|string $actualClassOrObject
     * @param string        $message
     * @param float         $delta
     * @param int           $maxDepth
     * @param bool          $canonicalize
     * @param bool          $ignoreCase
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeEquals($expected, string $actualAttributeName, $actualClassOrObject, string $message = '', float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = \false, bool $ignoreCase = \false) : void
    {
        static::assertEquals($expected, static::readAttribute($actualClassOrObject, $actualAttributeName), $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }
    /**
     * Asserts that two variables are not equal.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     * @param float  $delta
     * @param int    $maxDepth
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotEquals($expected, $actual, string $message = '', $delta = 0.0, $maxDepth = 10, $canonicalize = \false, $ignoreCase = \false) : void
    {
        $constraint = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsEqual($expected, $delta, $maxDepth, $canonicalize, $ignoreCase));
        static::assertThat($actual, $constraint, $message);
    }
    /**
     * Asserts that a variable is not equal to an attribute of an object.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param object|string $actualClassOrObject
     * @param string        $message
     * @param float         $delta
     * @param int           $maxDepth
     * @param bool          $canonicalize
     * @param bool          $ignoreCase
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeNotEquals($expected, string $actualAttributeName, $actualClassOrObject, string $message = '', float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = \false, bool $ignoreCase = \false) : void
    {
        static::assertNotEquals($expected, static::readAttribute($actualClassOrObject, $actualAttributeName), $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }
    /**
     * Asserts that a variable is empty.
     *
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertEmpty($actual, string $message = '') : void
    {
        static::assertThat($actual, static::isEmpty(), $message);
    }
    /**
     * Asserts that a static attribute of a class or an attribute of an object
     * is empty.
     *
     * @param string        $haystackAttributeName
     * @param object|string $haystackClassOrObject
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeEmpty(string $haystackAttributeName, $haystackClassOrObject, string $message = '') : void
    {
        static::assertEmpty(static::readAttribute($haystackClassOrObject, $haystackAttributeName), $message);
    }
    /**
     * Asserts that a variable is not empty.
     *
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotEmpty($actual, string $message = '') : void
    {
        static::assertThat($actual, static::logicalNot(static::isEmpty()), $message);
    }
    /**
     * Asserts that a static attribute of a class or an attribute of an object
     * is not empty.
     *
     * @param string        $haystackAttributeName
     * @param object|string $haystackClassOrObject
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeNotEmpty(string $haystackAttributeName, $haystackClassOrObject, string $message = '') : void
    {
        static::assertNotEmpty(static::readAttribute($haystackClassOrObject, $haystackAttributeName), $message);
    }
    /**
     * Asserts that a value is greater than another value.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertGreaterThan($expected, $actual, string $message = '') : void
    {
        static::assertThat($actual, static::greaterThan($expected), $message);
    }
    /**
     * Asserts that an attribute is greater than another value.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param object|string $actualClassOrObject
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeGreaterThan($expected, string $actualAttributeName, $actualClassOrObject, string $message = '') : void
    {
        static::assertGreaterThan($expected, static::readAttribute($actualClassOrObject, $actualAttributeName), $message);
    }
    /**
     * Asserts that a value is greater than or equal to another value.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertGreaterThanOrEqual($expected, $actual, string $message = '') : void
    {
        static::assertThat($actual, static::greaterThanOrEqual($expected), $message);
    }
    /**
     * Asserts that an attribute is greater than or equal to another value.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param object|string $actualClassOrObject
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeGreaterThanOrEqual($expected, string $actualAttributeName, $actualClassOrObject, string $message = '') : void
    {
        static::assertGreaterThanOrEqual($expected, static::readAttribute($actualClassOrObject, $actualAttributeName), $message);
    }
    /**
     * Asserts that a value is smaller than another value.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertLessThan($expected, $actual, string $message = '') : void
    {
        static::assertThat($actual, static::lessThan($expected), $message);
    }
    /**
     * Asserts that an attribute is smaller than another value.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param object|string $actualClassOrObject
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeLessThan($expected, string $actualAttributeName, $actualClassOrObject, string $message = '') : void
    {
        static::assertLessThan($expected, static::readAttribute($actualClassOrObject, $actualAttributeName), $message);
    }
    /**
     * Asserts that a value is smaller than or equal to another value.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertLessThanOrEqual($expected, $actual, string $message = '') : void
    {
        static::assertThat($actual, static::lessThanOrEqual($expected), $message);
    }
    /**
     * Asserts that an attribute is smaller than or equal to another value.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param object|string $actualClassOrObject
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeLessThanOrEqual($expected, string $actualAttributeName, $actualClassOrObject, string $message = '') : void
    {
        static::assertLessThanOrEqual($expected, static::readAttribute($actualClassOrObject, $actualAttributeName), $message);
    }
    /**
     * Asserts that the contents of one file is equal to the contents of another
     * file.
     *
     * @param string $expected
     * @param string $actual
     * @param string $message
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertFileEquals(string $expected, string $actual, string $message = '', bool $canonicalize = \false, bool $ignoreCase = \false) : void
    {
        static::assertFileExists($expected, $message);
        static::assertFileExists($actual, $message);
        static::assertEquals(\file_get_contents($expected), \file_get_contents($actual), $message, 0, 10, $canonicalize, $ignoreCase);
    }
    /**
     * Asserts that the contents of one file is not equal to the contents of
     * another file.
     *
     * @param string $expected
     * @param string $actual
     * @param string $message
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertFileNotEquals(string $expected, string $actual, string $message = '', bool $canonicalize = \false, bool $ignoreCase = \false) : void
    {
        static::assertFileExists($expected, $message);
        static::assertFileExists($actual, $message);
        static::assertNotEquals(\file_get_contents($expected), \file_get_contents($actual), $message, 0, 10, $canonicalize, $ignoreCase);
    }
    /**
     * Asserts that the contents of a string is equal
     * to the contents of a file.
     *
     * @param string $expectedFile
     * @param string $actualString
     * @param string $message
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertStringEqualsFile(string $expectedFile, string $actualString, string $message = '', bool $canonicalize = \false, bool $ignoreCase = \false) : void
    {
        static::assertFileExists($expectedFile, $message);
        /** @noinspection PhpUnitTestsInspection */
        static::assertEquals(\file_get_contents($expectedFile), $actualString, $message, 0, 10, $canonicalize, $ignoreCase);
    }
    /**
     * Asserts that the contents of a string is not equal
     * to the contents of a file.
     *
     * @param string $expectedFile
     * @param string $actualString
     * @param string $message
     * @param bool   $canonicalize
     * @param bool   $ignoreCase
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertStringNotEqualsFile(string $expectedFile, string $actualString, string $message = '', bool $canonicalize = \false, bool $ignoreCase = \false) : void
    {
        static::assertFileExists($expectedFile, $message);
        static::assertNotEquals(\file_get_contents($expectedFile), $actualString, $message, 0, 10, $canonicalize, $ignoreCase);
    }
    /**
     * Asserts that a file/dir is readable.
     *
     * @param string $filename
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertIsReadable(string $filename, string $message = '') : void
    {
        static::assertThat($filename, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsReadable(), $message);
    }
    /**
     * Asserts that a file/dir exists and is not readable.
     *
     * @param string $filename
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotIsReadable(string $filename, string $message = '') : void
    {
        static::assertThat($filename, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsReadable()), $message);
    }
    /**
     * Asserts that a file/dir exists and is writable.
     *
     * @param string $filename
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertIsWritable(string $filename, string $message = '') : void
    {
        static::assertThat($filename, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsWritable(), $message);
    }
    /**
     * Asserts that a file/dir exists and is not writable.
     *
     * @param string $filename
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotIsWritable(string $filename, string $message = '') : void
    {
        static::assertThat($filename, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsWritable()), $message);
    }
    /**
     * Asserts that a directory exists.
     *
     * @param string $directory
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertDirectoryExists(string $directory, string $message = '') : void
    {
        static::assertThat($directory, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\DirectoryExists(), $message);
    }
    /**
     * Asserts that a directory does not exist.
     *
     * @param string $directory
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertDirectoryNotExists(string $directory, string $message = '') : void
    {
        static::assertThat($directory, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\DirectoryExists()), $message);
    }
    /**
     * Asserts that a directory exists and is readable.
     *
     * @param string $directory
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertDirectoryIsReadable(string $directory, string $message = '') : void
    {
        self::assertDirectoryExists($directory, $message);
        self::assertIsReadable($directory, $message);
    }
    /**
     * Asserts that a directory exists and is not readable.
     *
     * @param string $directory
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertDirectoryNotIsReadable(string $directory, string $message = '') : void
    {
        self::assertDirectoryExists($directory, $message);
        self::assertNotIsReadable($directory, $message);
    }
    /**
     * Asserts that a directory exists and is writable.
     *
     * @param string $directory
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertDirectoryIsWritable(string $directory, string $message = '') : void
    {
        self::assertDirectoryExists($directory, $message);
        self::assertIsWritable($directory, $message);
    }
    /**
     * Asserts that a directory exists and is not writable.
     *
     * @param string $directory
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertDirectoryNotIsWritable(string $directory, string $message = '') : void
    {
        self::assertDirectoryExists($directory, $message);
        self::assertNotIsWritable($directory, $message);
    }
    /**
     * Asserts that a file exists.
     *
     * @param string $filename
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertFileExists(string $filename, string $message = '') : void
    {
        static::assertThat($filename, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\FileExists(), $message);
    }
    /**
     * Asserts that a file does not exist.
     *
     * @param string $filename
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertFileNotExists(string $filename, string $message = '') : void
    {
        static::assertThat($filename, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\FileExists()), $message);
    }
    /**
     * Asserts that a file exists and is readable.
     *
     * @param string $file
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertFileIsReadable(string $file, string $message = '') : void
    {
        self::assertFileExists($file, $message);
        self::assertIsReadable($file, $message);
    }
    /**
     * Asserts that a file exists and is not readable.
     *
     * @param string $file
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertFileNotIsReadable(string $file, string $message = '') : void
    {
        self::assertFileExists($file, $message);
        self::assertNotIsReadable($file, $message);
    }
    /**
     * Asserts that a file exists and is writable.
     *
     * @param string $file
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertFileIsWritable(string $file, string $message = '') : void
    {
        self::assertFileExists($file, $message);
        self::assertIsWritable($file, $message);
    }
    /**
     * Asserts that a file exists and is not writable.
     *
     * @param string $file
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertFileNotIsWritable(string $file, string $message = '') : void
    {
        self::assertFileExists($file, $message);
        self::assertNotIsWritable($file, $message);
    }
    /**
     * Asserts that a condition is true.
     *
     * @param mixed  $condition
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertTrue($condition, string $message = '') : void
    {
        static::assertThat($condition, static::isTrue(), $message);
    }
    /**
     * Asserts that a condition is not true.
     *
     * @param mixed  $condition
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotTrue($condition, string $message = '') : void
    {
        static::assertThat($condition, static::logicalNot(static::isTrue()), $message);
    }
    /**
     * Asserts that a condition is false.
     *
     * @param mixed  $condition
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertFalse($condition, string $message = '') : void
    {
        static::assertThat($condition, static::isFalse(), $message);
    }
    /**
     * Asserts that a condition is not false.
     *
     * @param mixed  $condition
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotFalse($condition, string $message = '') : void
    {
        static::assertThat($condition, static::logicalNot(static::isFalse()), $message);
    }
    /**
     * Asserts that a variable is null.
     *
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNull($actual, string $message = '') : void
    {
        static::assertThat($actual, static::isNull(), $message);
    }
    /**
     * Asserts that a variable is not null.
     *
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotNull($actual, string $message = '') : void
    {
        static::assertThat($actual, static::logicalNot(static::isNull()), $message);
    }
    /**
     * Asserts that a variable is finite.
     *
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertFinite($actual, string $message = '') : void
    {
        static::assertThat($actual, static::isFinite(), $message);
    }
    /**
     * Asserts that a variable is infinite.
     *
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertInfinite($actual, string $message = '') : void
    {
        static::assertThat($actual, static::isInfinite(), $message);
    }
    /**
     * Asserts that a variable is nan.
     *
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNan($actual, string $message = '') : void
    {
        static::assertThat($actual, static::isNan(), $message);
    }
    /**
     * Asserts that a class has a specified attribute.
     *
     * @param string $attributeName
     * @param string $className
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertClassHasAttribute(string $attributeName, string $className, string $message = '') : void
    {
        if (!self::isValidAttributeName($attributeName)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'valid attribute name');
        }
        if (!\class_exists($className)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'class name', $className);
        }
        static::assertThat($className, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ClassHasAttribute($attributeName), $message);
    }
    /**
     * Asserts that a class does not have a specified attribute.
     *
     * @param string $attributeName
     * @param string $className
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertClassNotHasAttribute(string $attributeName, string $className, string $message = '') : void
    {
        if (!self::isValidAttributeName($attributeName)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'valid attribute name');
        }
        if (!\class_exists($className)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'class name', $className);
        }
        static::assertThat($className, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ClassHasAttribute($attributeName)), $message);
    }
    /**
     * Asserts that a class has a specified static attribute.
     *
     * @param string $attributeName
     * @param string $className
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertClassHasStaticAttribute(string $attributeName, string $className, string $message = '') : void
    {
        if (!self::isValidAttributeName($attributeName)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'valid attribute name');
        }
        if (!\class_exists($className)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'class name', $className);
        }
        static::assertThat($className, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ClassHasStaticAttribute($attributeName), $message);
    }
    /**
     * Asserts that a class does not have a specified static attribute.
     *
     * @param string $attributeName
     * @param string $className
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertClassNotHasStaticAttribute(string $attributeName, string $className, string $message = '') : void
    {
        if (!self::isValidAttributeName($attributeName)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'valid attribute name');
        }
        if (!\class_exists($className)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'class name', $className);
        }
        static::assertThat($className, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ClassHasStaticAttribute($attributeName)), $message);
    }
    /**
     * Asserts that an object has a specified attribute.
     *
     * @param string $attributeName
     * @param object $object
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertObjectHasAttribute(string $attributeName, $object, string $message = '') : void
    {
        if (!self::isValidAttributeName($attributeName)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'valid attribute name');
        }
        if (!\is_object($object)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'object');
        }
        static::assertThat($object, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ObjectHasAttribute($attributeName), $message);
    }
    /**
     * Asserts that an object does not have a specified attribute.
     *
     * @param string $attributeName
     * @param object $object
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertObjectNotHasAttribute(string $attributeName, $object, string $message = '') : void
    {
        if (!self::isValidAttributeName($attributeName)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'valid attribute name');
        }
        if (!\is_object($object)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'object');
        }
        static::assertThat($object, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ObjectHasAttribute($attributeName)), $message);
    }
    /**
     * Asserts that two variables have the same type and value.
     * Used on objects, it asserts that two variables reference
     * the same object.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertSame($expected, $actual, string $message = '') : void
    {
        if (\is_bool($expected) && \is_bool($actual)) {
            static::assertEquals($expected, $actual, $message);
        }
        static::assertThat($actual, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsIdentical($expected), $message);
    }
    /**
     * Asserts that a variable and an attribute of an object have the same type
     * and value.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param object|string $actualClassOrObject
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeSame($expected, string $actualAttributeName, $actualClassOrObject, string $message = '') : void
    {
        static::assertSame($expected, static::readAttribute($actualClassOrObject, $actualAttributeName), $message);
    }
    /**
     * Asserts that two variables do not have the same type and value.
     * Used on objects, it asserts that two variables do not reference
     * the same object.
     *
     * @param mixed  $expected
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotSame($expected, $actual, string $message = '') : void
    {
        if (\is_bool($expected) && \is_bool($actual)) {
            static::assertNotEquals($expected, $actual, $message);
        }
        static::assertThat($actual, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsIdentical($expected)), $message);
    }
    /**
     * Asserts that a variable and an attribute of an object do not have the
     * same type and value.
     *
     * @param mixed         $expected
     * @param string        $actualAttributeName
     * @param object|string $actualClassOrObject
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeNotSame($expected, string $actualAttributeName, $actualClassOrObject, string $message = '') : void
    {
        static::assertNotSame($expected, static::readAttribute($actualClassOrObject, $actualAttributeName), $message);
    }
    /**
     * Asserts that a variable is of a given type.
     *
     * @param string $expected
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertInstanceOf(string $expected, $actual, string $message = '') : void
    {
        if (!\class_exists($expected) && !\interface_exists($expected)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'class or interface name');
        }
        static::assertThat($actual, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsInstanceOf($expected), $message);
    }
    /**
     * Asserts that an attribute is of a given type.
     *
     * @param string        $expected
     * @param string        $attributeName
     * @param object|string $classOrObject
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeInstanceOf(string $expected, string $attributeName, $classOrObject, string $message = '') : void
    {
        static::assertInstanceOf($expected, static::readAttribute($classOrObject, $attributeName), $message);
    }
    /**
     * Asserts that a variable is not of a given type.
     *
     * @param string $expected
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotInstanceOf(string $expected, $actual, string $message = '') : void
    {
        if (!\class_exists($expected) && !\interface_exists($expected)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'class or interface name');
        }
        static::assertThat($actual, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsInstanceOf($expected)), $message);
    }
    /**
     * Asserts that an attribute is of a given type.
     *
     * @param string        $expected
     * @param string        $attributeName
     * @param object|string $classOrObject
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeNotInstanceOf(string $expected, string $attributeName, $classOrObject, string $message = '') : void
    {
        static::assertNotInstanceOf($expected, static::readAttribute($classOrObject, $attributeName), $message);
    }
    /**
     * Asserts that a variable is of a given type.
     *
     * @param string $expected
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertInternalType(string $expected, $actual, string $message = '') : void
    {
        static::assertThat($actual, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsType($expected), $message);
    }
    /**
     * Asserts that an attribute is of a given type.
     *
     * @param string        $expected
     * @param string        $attributeName
     * @param object|string $classOrObject
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeInternalType(string $expected, string $attributeName, $classOrObject, string $message = '') : void
    {
        static::assertInternalType($expected, static::readAttribute($classOrObject, $attributeName), $message);
    }
    /**
     * Asserts that a variable is not of a given type.
     *
     * @param string $expected
     * @param mixed  $actual
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotInternalType(string $expected, $actual, string $message = '') : void
    {
        static::assertThat($actual, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsType($expected)), $message);
    }
    /**
     * Asserts that an attribute is of a given type.
     *
     * @param string        $expected
     * @param string        $attributeName
     * @param object|string $classOrObject
     * @param string        $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertAttributeNotInternalType(string $expected, string $attributeName, $classOrObject, string $message = '') : void
    {
        static::assertNotInternalType($expected, static::readAttribute($classOrObject, $attributeName), $message);
    }
    /**
     * Asserts that a string matches a given regular expression.
     *
     * @param string $pattern
     * @param string $string
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertRegExp(string $pattern, string $string, string $message = '') : void
    {
        static::assertThat($string, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\RegularExpression($pattern), $message);
    }
    /**
     * Asserts that a string does not match a given regular expression.
     *
     * @param string $pattern
     * @param string $string
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotRegExp(string $pattern, string $string, string $message = '') : void
    {
        static::assertThat($string, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\RegularExpression($pattern)), $message);
    }
    /**
     * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
     * is the same.
     *
     * @param Countable|iterable $expected
     * @param Countable|iterable $actual
     * @param string             $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertSameSize($expected, $actual, string $message = '') : void
    {
        if (!$expected instanceof \Countable && !\is_iterable($expected)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'countable or iterable');
        }
        if (!$actual instanceof \Countable && !\is_iterable($actual)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'countable or iterable');
        }
        static::assertThat($actual, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\SameSize($expected), $message);
    }
    /**
     * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
     * is not the same.
     *
     * @param Countable|iterable $expected
     * @param Countable|iterable $actual
     * @param string             $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertNotSameSize($expected, $actual, string $message = '') : void
    {
        if (!$expected instanceof \Countable && !\is_iterable($expected)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'countable or iterable');
        }
        if (!$actual instanceof \Countable && !\is_iterable($actual)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'countable or iterable');
        }
        static::assertThat($actual, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\SameSize($expected)), $message);
    }
    /**
     * Asserts that a string matches a given format string.
     *
     * @param string $format
     * @param string $string
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertStringMatchesFormat(string $format, string $string, string $message = '') : void
    {
        static::assertThat($string, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringMatchesFormatDescription($format), $message);
    }
    /**
     * Asserts that a string does not match a given format string.
     *
     * @param string $format
     * @param string $string
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertStringNotMatchesFormat(string $format, string $string, string $message = '') : void
    {
        static::assertThat($string, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringMatchesFormatDescription($format)), $message);
    }
    /**
     * Asserts that a string matches a given format file.
     *
     * @param string $formatFile
     * @param string $string
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertStringMatchesFormatFile(string $formatFile, string $string, string $message = '') : void
    {
        static::assertFileExists($formatFile, $message);
        static::assertThat($string, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringMatchesFormatDescription(\file_get_contents($formatFile)), $message);
    }
    /**
     * Asserts that a string does not match a given format string.
     *
     * @param string $formatFile
     * @param string $string
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertStringNotMatchesFormatFile(string $formatFile, string $string, string $message = '') : void
    {
        static::assertFileExists($formatFile, $message);
        static::assertThat($string, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringMatchesFormatDescription(\file_get_contents($formatFile))), $message);
    }
    /**
     * Asserts that a string starts with a given prefix.
     *
     * @param string $prefix
     * @param string $string
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertStringStartsWith(string $prefix, string $string, string $message = '') : void
    {
        static::assertThat($string, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringStartsWith($prefix), $message);
    }
    /**
     * Asserts that a string starts not with a given prefix.
     *
     * @param string $prefix
     * @param string $string
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertStringStartsNotWith($prefix, $string, string $message = '') : void
    {
        static::assertThat($string, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringStartsWith($prefix)), $message);
    }
    /**
     * Asserts that a string ends with a given suffix.
     *
     * @param string $suffix
     * @param string $string
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertStringEndsWith(string $suffix, string $string, string $message = '') : void
    {
        static::assertThat($string, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringEndsWith($suffix), $message);
    }
    /**
     * Asserts that a string ends not with a given suffix.
     *
     * @param string $suffix
     * @param string $string
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertStringEndsNotWith(string $suffix, string $string, string $message = '') : void
    {
        static::assertThat($string, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringEndsWith($suffix)), $message);
    }
    /**
     * Asserts that two XML files are equal.
     *
     * @param string $expectedFile
     * @param string $actualFile
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertXmlFileEqualsXmlFile(string $expectedFile, string $actualFile, string $message = '') : void
    {
        $expected = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::loadFile($expectedFile);
        $actual = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::loadFile($actualFile);
        static::assertEquals($expected, $actual, $message);
    }
    /**
     * Asserts that two XML files are not equal.
     *
     * @param string $expectedFile
     * @param string $actualFile
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertXmlFileNotEqualsXmlFile(string $expectedFile, string $actualFile, string $message = '') : void
    {
        $expected = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::loadFile($expectedFile);
        $actual = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::loadFile($actualFile);
        static::assertNotEquals($expected, $actual, $message);
    }
    /**
     * Asserts that two XML documents are equal.
     *
     * @param string             $expectedFile
     * @param DOMDocument|string $actualXml
     * @param string             $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertXmlStringEqualsXmlFile(string $expectedFile, $actualXml, string $message = '') : void
    {
        $expected = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::loadFile($expectedFile);
        $actual = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::load($actualXml);
        static::assertEquals($expected, $actual, $message);
    }
    /**
     * Asserts that two XML documents are not equal.
     *
     * @param string             $expectedFile
     * @param DOMDocument|string $actualXml
     * @param string             $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertXmlStringNotEqualsXmlFile(string $expectedFile, $actualXml, string $message = '') : void
    {
        $expected = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::loadFile($expectedFile);
        $actual = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::load($actualXml);
        static::assertNotEquals($expected, $actual, $message);
    }
    /**
     * Asserts that two XML documents are equal.
     *
     * @param DOMDocument|string $expectedXml
     * @param DOMDocument|string $actualXml
     * @param string             $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertXmlStringEqualsXmlString($expectedXml, $actualXml, string $message = '') : void
    {
        $expected = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::load($expectedXml);
        $actual = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::load($actualXml);
        static::assertEquals($expected, $actual, $message);
    }
    /**
     * Asserts that two XML documents are not equal.
     *
     * @param DOMDocument|string $expectedXml
     * @param DOMDocument|string $actualXml
     * @param string             $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertXmlStringNotEqualsXmlString($expectedXml, $actualXml, string $message = '') : void
    {
        $expected = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::load($expectedXml);
        $actual = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::load($actualXml);
        static::assertNotEquals($expected, $actual, $message);
    }
    /**
     * Asserts that a hierarchy of DOMElements matches.
     *
     * @param DOMElement $expectedElement
     * @param DOMElement $actualElement
     * @param bool       $checkAttributes
     * @param string     $message
     *
     * @throws AssertionFailedError
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertEqualXMLStructure(\DOMElement $expectedElement, \DOMElement $actualElement, bool $checkAttributes = \false, string $message = '') : void
    {
        $tmp = new \DOMDocument();
        $expectedElement = $tmp->importNode($expectedElement, \true);
        $tmp = new \DOMDocument();
        $actualElement = $tmp->importNode($actualElement, \true);
        unset($tmp);
        static::assertEquals($expectedElement->tagName, $actualElement->tagName, $message);
        if ($checkAttributes) {
            static::assertEquals($expectedElement->attributes->length, $actualElement->attributes->length, \sprintf('%s%sNumber of attributes on node "%s" does not match', $message, !empty($message) ? "\n" : '', $expectedElement->tagName));
            for ($i = 0; $i < $expectedElement->attributes->length; $i++) {
                $expectedAttribute = $expectedElement->attributes->item($i);
                $actualAttribute = $actualElement->attributes->getNamedItem($expectedAttribute->name);
                if (!$actualAttribute) {
                    static::fail(\sprintf('%s%sCould not find attribute "%s" on node "%s"', $message, !empty($message) ? "\n" : '', $expectedAttribute->name, $expectedElement->tagName));
                }
            }
        }
        \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::removeCharacterDataNodes($expectedElement);
        \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::removeCharacterDataNodes($actualElement);
        static::assertEquals($expectedElement->childNodes->length, $actualElement->childNodes->length, \sprintf('%s%sNumber of child nodes of "%s" differs', $message, !empty($message) ? "\n" : '', $expectedElement->tagName));
        for ($i = 0; $i < $expectedElement->childNodes->length; $i++) {
            static::assertEqualXMLStructure($expectedElement->childNodes->item($i), $actualElement->childNodes->item($i), $checkAttributes, $message);
        }
    }
    /**
     * Evaluates a PHPUnit\Framework\Constraint matcher object.
     *
     * @param mixed      $value
     * @param Constraint $constraint
     * @param string     $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertThat($value, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Constraint $constraint, string $message = '') : void
    {
        self::$count += \count($constraint);
        $constraint->evaluate($value, $message);
    }
    /**
     * Asserts that a string is a valid JSON string.
     *
     * @param string $actualJson
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertJson(string $actualJson, string $message = '') : void
    {
        static::assertThat($actualJson, static::isJson(), $message);
    }
    /**
     * Asserts that two given JSON encoded objects or arrays are equal.
     *
     * @param string $expectedJson
     * @param string $actualJson
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertJsonStringEqualsJsonString(string $expectedJson, string $actualJson, string $message = '') : void
    {
        static::assertJson($expectedJson, $message);
        static::assertJson($actualJson, $message);
        static::assertThat($actualJson, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\JsonMatches($expectedJson), $message);
    }
    /**
     * Asserts that two given JSON encoded objects or arrays are not equal.
     *
     * @param string $expectedJson
     * @param string $actualJson
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertJsonStringNotEqualsJsonString($expectedJson, $actualJson, string $message = '') : void
    {
        static::assertJson($expectedJson, $message);
        static::assertJson($actualJson, $message);
        static::assertThat($actualJson, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\JsonMatches($expectedJson)), $message);
    }
    /**
     * Asserts that the generated JSON encoded object and the content of the given file are equal.
     *
     * @param string $expectedFile
     * @param string $actualJson
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertJsonStringEqualsJsonFile(string $expectedFile, string $actualJson, string $message = '') : void
    {
        static::assertFileExists($expectedFile, $message);
        $expectedJson = \file_get_contents($expectedFile);
        static::assertJson($expectedJson, $message);
        static::assertJson($actualJson, $message);
        static::assertThat($actualJson, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\JsonMatches($expectedJson), $message);
    }
    /**
     * Asserts that the generated JSON encoded object and the content of the given file are not equal.
     *
     * @param string $expectedFile
     * @param string $actualJson
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertJsonStringNotEqualsJsonFile(string $expectedFile, string $actualJson, string $message = '') : void
    {
        static::assertFileExists($expectedFile, $message);
        $expectedJson = \file_get_contents($expectedFile);
        static::assertJson($expectedJson, $message);
        static::assertJson($actualJson, $message);
        static::assertThat($actualJson, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\JsonMatches($expectedJson)), $message);
    }
    /**
     * Asserts that two JSON files are equal.
     *
     * @param string $expectedFile
     * @param string $actualFile
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertJsonFileEqualsJsonFile(string $expectedFile, string $actualFile, string $message = '') : void
    {
        static::assertFileExists($expectedFile, $message);
        static::assertFileExists($actualFile, $message);
        $actualJson = \file_get_contents($actualFile);
        $expectedJson = \file_get_contents($expectedFile);
        static::assertJson($expectedJson, $message);
        static::assertJson($actualJson, $message);
        $constraintExpected = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\JsonMatches($expectedJson);
        $constraintActual = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\JsonMatches($actualJson);
        static::assertThat($expectedJson, $constraintActual, $message);
        static::assertThat($actualJson, $constraintExpected, $message);
    }
    /**
     * Asserts that two JSON files are not equal.
     *
     * @param string $expectedFile
     * @param string $actualFile
     * @param string $message
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public static function assertJsonFileNotEqualsJsonFile(string $expectedFile, string $actualFile, string $message = '') : void
    {
        static::assertFileExists($expectedFile, $message);
        static::assertFileExists($actualFile, $message);
        $actualJson = \file_get_contents($actualFile);
        $expectedJson = \file_get_contents($expectedFile);
        static::assertJson($expectedJson, $message);
        static::assertJson($actualJson, $message);
        $constraintExpected = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\JsonMatches($expectedJson);
        $constraintActual = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\JsonMatches($actualJson);
        static::assertThat($expectedJson, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot($constraintActual), $message);
        static::assertThat($actualJson, new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot($constraintExpected), $message);
    }
    public static function logicalAnd() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalAnd
    {
        $constraints = \func_get_args();
        $constraint = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalAnd();
        $constraint->setConstraints($constraints);
        return $constraint;
    }
    public static function logicalOr() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalOr
    {
        $constraints = \func_get_args();
        $constraint = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalOr();
        $constraint->setConstraints($constraints);
        return $constraint;
    }
    public static function logicalNot(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Constraint $constraint) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot($constraint);
    }
    public static function logicalXor() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalXor
    {
        $constraints = \func_get_args();
        $constraint = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalXor();
        $constraint->setConstraints($constraints);
        return $constraint;
    }
    public static function anything() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsAnything
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsAnything();
    }
    public static function isTrue() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsTrue
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsTrue();
    }
    public static function callback(callable $callback) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Callback
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Callback($callback);
    }
    public static function isFalse() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsFalse
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsFalse();
    }
    public static function isJson() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsJson
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsJson();
    }
    public static function isNull() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsNull
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsNull();
    }
    public static function isFinite() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsFinite
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsFinite();
    }
    public static function isInfinite() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsInfinite
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsInfinite();
    }
    public static function isNan() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsNan
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsNan();
    }
    public static function attribute(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Constraint $constraint, string $attributeName) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Attribute
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Attribute($constraint, $attributeName);
    }
    public static function contains($value, bool $checkForObjectIdentity = \true, bool $checkForNonObjectIdentity = \false) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContains
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContains($value, $checkForObjectIdentity, $checkForNonObjectIdentity);
    }
    public static function containsOnly(string $type) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContainsOnly
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContainsOnly($type);
    }
    public static function containsOnlyInstancesOf(string $className) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContainsOnly
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContainsOnly($className, \false);
    }
    /**
     * @param int|string $key
     */
    public static function arrayHasKey($key) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ArrayHasKey
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ArrayHasKey($key);
    }
    public static function equalTo($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = \false, bool $ignoreCase = \false) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsEqual
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsEqual($value, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }
    public static function attributeEqualTo(string $attributeName, $value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = \false, bool $ignoreCase = \false) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Attribute
    {
        return static::attribute(static::equalTo($value, $delta, $maxDepth, $canonicalize, $ignoreCase), $attributeName);
    }
    public static function isEmpty() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsEmpty
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsEmpty();
    }
    public static function isWritable() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsWritable
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsWritable();
    }
    public static function isReadable() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsReadable
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsReadable();
    }
    public static function directoryExists() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\DirectoryExists
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\DirectoryExists();
    }
    public static function fileExists() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\FileExists
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\FileExists();
    }
    public static function greaterThan($value) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\GreaterThan
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\GreaterThan($value);
    }
    public static function greaterThanOrEqual($value) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalOr
    {
        return static::logicalOr(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsEqual($value), new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\GreaterThan($value));
    }
    public static function classHasAttribute(string $attributeName) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ClassHasAttribute
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ClassHasAttribute($attributeName);
    }
    public static function classHasStaticAttribute(string $attributeName) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ClassHasStaticAttribute
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ClassHasStaticAttribute($attributeName);
    }
    public static function objectHasAttribute($attributeName) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ObjectHasAttribute
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ObjectHasAttribute($attributeName);
    }
    public static function identicalTo($value) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsIdentical
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsIdentical($value);
    }
    public static function isInstanceOf(string $className) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsInstanceOf
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsInstanceOf($className);
    }
    public static function isType(string $type) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsType
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsType($type);
    }
    public static function lessThan($value) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LessThan
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LessThan($value);
    }
    public static function lessThanOrEqual($value) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalOr
    {
        return static::logicalOr(new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsEqual($value), new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LessThan($value));
    }
    public static function matchesRegularExpression(string $pattern) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\RegularExpression
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\RegularExpression($pattern);
    }
    public static function matches(string $string) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringMatchesFormatDescription
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringMatchesFormatDescription($string);
    }
    public static function stringStartsWith($prefix) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringStartsWith
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringStartsWith($prefix);
    }
    public static function stringContains(string $string, bool $case = \true) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringContains
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringContains($string, $case);
    }
    public static function stringEndsWith(string $suffix) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringEndsWith
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringEndsWith($suffix);
    }
    public static function countOf(int $count) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Count
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Count($count);
    }
    /**
     * Fails a test with the given message.
     *
     * @param string $message
     *
     * @throws AssertionFailedError
     */
    public static function fail(string $message = '') : void
    {
        self::$count++;
        throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\AssertionFailedError($message);
    }
    /**
     * Returns the value of an attribute of a class or an object.
     * This also works for attributes that are declared protected or private.
     *
     * @param object|string $classOrObject
     * @param string        $attributeName
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function readAttribute($classOrObject, string $attributeName)
    {
        if (!self::isValidAttributeName($attributeName)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'valid attribute name');
        }
        if (\is_string($classOrObject)) {
            if (!\class_exists($classOrObject)) {
                throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'class name');
            }
            return static::getStaticAttribute($classOrObject, $attributeName);
        }
        if (\is_object($classOrObject)) {
            return static::getObjectAttribute($classOrObject, $attributeName);
        }
        throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'class name or object');
    }
    /**
     * Returns the value of a static attribute.
     * This also works for attributes that are declared protected or private.
     *
     * @param string $className
     * @param string $attributeName
     *
     * @throws Exception
     * @throws ReflectionException
     *
     * @return mixed
     */
    public static function getStaticAttribute(string $className, string $attributeName)
    {
        if (!\class_exists($className)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'class name');
        }
        if (!self::isValidAttributeName($attributeName)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'valid attribute name');
        }
        $class = new \ReflectionClass($className);
        while ($class) {
            $attributes = $class->getStaticProperties();
            if (\array_key_exists($attributeName, $attributes)) {
                return $attributes[$attributeName];
            }
            $class = $class->getParentClass();
        }
        throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception(\sprintf('Attribute "%s" not found in class.', $attributeName));
    }
    /**
     * Returns the value of an object's attribute.
     * This also works for attributes that are declared protected or private.
     *
     * @param object $object
     * @param string $attributeName
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function getObjectAttribute($object, string $attributeName)
    {
        if (!\is_object($object)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(1, 'object');
        }
        if (!self::isValidAttributeName($attributeName)) {
            throw \_PhpScoper5b2c11ee6df50\PHPUnit\Util\InvalidArgumentHelper::factory(2, 'valid attribute name');
        }
        try {
            $attribute = new \ReflectionProperty($object, $attributeName);
        } catch (\ReflectionException $e) {
            $reflector = new \ReflectionObject($object);
            while ($reflector = $reflector->getParentClass()) {
                try {
                    $attribute = $reflector->getProperty($attributeName);
                    break;
                } catch (\ReflectionException $e) {
                }
            }
        }
        if (isset($attribute)) {
            if (!$attribute || $attribute->isPublic()) {
                return $object->{$attributeName};
            }
            $attribute->setAccessible(\true);
            $value = $attribute->getValue($object);
            $attribute->setAccessible(\false);
            return $value;
        }
        throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception(\sprintf('Attribute "%s" not found in object.', $attributeName));
    }
    /**
     * Mark the test as incomplete.
     *
     * @param string $message
     *
     * @throws IncompleteTestError
     */
    public static function markTestIncomplete(string $message = '') : void
    {
        throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\IncompleteTestError($message);
    }
    /**
     * Mark the test as skipped.
     *
     * @param string $message
     *
     * @throws SkippedTestError
     */
    public static function markTestSkipped(string $message = '') : void
    {
        throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\SkippedTestError($message);
    }
    /**
     * Return the current assertion count.
     */
    public static function getCount() : int
    {
        return self::$count;
    }
    /**
     * Reset the assertion counter.
     */
    public static function resetCount() : void
    {
        self::$count = 0;
    }
    private static function isValidAttributeName(string $attributeName) : bool
    {
        return \preg_match('/[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*/', $attributeName);
    }
}
