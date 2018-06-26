<?php

namespace _PhpScoper5b2c11ee6df50;

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ArrayHasKey;
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
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LessThan;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalAnd;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalOr;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalXor;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ObjectHasAttribute;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\RegularExpression;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringContains;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringEndsWith;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringMatchesFormatDescription;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringStartsWith;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContains;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContainsOnly;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExpectationFailedException;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\AnyInvokedCount as AnyInvokedCountMatcher;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedAtIndex as InvokedAtIndexMatcher;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedAtLeastCount as InvokedAtLeastCountMatcher;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedAtLeastOnce as InvokedAtLeastOnceMatcher;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedAtMostCount as InvokedAtMostCountMatcher;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedCount as InvokedCountMatcher;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls as ConsecutiveCallsStub;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\Exception as ExceptionStub;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnArgument as ReturnArgumentStub;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnCallback as ReturnCallbackStub;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnSelf as ReturnSelfStub;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnStub;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnValueMap as ReturnValueMapStub;
/**
 * Asserts that an array has a specified key.
 *
 * @param int|string        $key
 * @param array|ArrayAccess $array
 * @param string            $message
 *
 * @throws Exception
 */
function assertArrayHasKey($key, $array, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertArrayHasKey(...\func_get_args());
}
/**
 * Asserts that an array has a specified subset.
 *
 * @param array|ArrayAccess $subset
 * @param array|ArrayAccess $array
 * @param bool              $strict  Check for object identity
 * @param string            $message
 *
 * @throws Exception
 */
function assertArraySubset($subset, $array, bool $strict = \false, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertArraySubset(...\func_get_args());
}
/**
 * Asserts that an array does not have a specified key.
 *
 * @param int|string        $key
 * @param array|ArrayAccess $array
 * @param string            $message
 *
 * @throws Exception
 */
function assertArrayNotHasKey($key, $array, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertArrayNotHasKey(...\func_get_args());
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
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \PHPUnit\Framework\Exception
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertContains($needle, $haystack, string $message = '', bool $ignoreCase = \false, bool $checkForObjectIdentity = \true, bool $checkForNonObjectIdentity = \false) : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertContains(...\func_get_args());
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
 * @throws Exception
 */
function assertAttributeContains($needle, string $haystackAttributeName, $haystackClassOrObject, string $message = '', bool $ignoreCase = \false, bool $checkForObjectIdentity = \true, bool $checkForNonObjectIdentity = \false) : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeContains(...\func_get_args());
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
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \PHPUnit\Framework\Exception
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertNotContains($needle, $haystack, string $message = '', bool $ignoreCase = \false, bool $checkForObjectIdentity = \true, bool $checkForNonObjectIdentity = \false) : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotContains(...\func_get_args());
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
 * @throws Exception
 */
function assertAttributeNotContains($needle, string $haystackAttributeName, $haystackClassOrObject, string $message = '', bool $ignoreCase = \false, bool $checkForObjectIdentity = \true, bool $checkForNonObjectIdentity = \false) : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeNotContains(...\func_get_args());
}
/**
 * Asserts that a haystack contains only values of a given type.
 *
 * @param string    $type
 * @param iterable  $haystack
 * @param null|bool $isNativeType
 * @param string    $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertContainsOnly(string $type, iterable $haystack, ?bool $isNativeType = null, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertContainsOnly(...\func_get_args());
}
/**
 * Asserts that a haystack contains only instances of a given class name.
 *
 * @param string   $className
 * @param iterable $haystack
 * @param string   $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertContainsOnlyInstancesOf(string $className, iterable $haystack, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertContainsOnlyInstancesOf(...\func_get_args());
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
 * @throws Exception
 */
function assertAttributeContainsOnly(string $type, string $haystackAttributeName, $haystackClassOrObject, ?bool $isNativeType = null, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeContainsOnly(...\func_get_args());
}
/**
 * Asserts that a haystack does not contain only values of a given type.
 *
 * @param string    $type
 * @param iterable  $haystack
 * @param null|bool $isNativeType
 * @param string    $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertNotContainsOnly(string $type, iterable $haystack, ?bool $isNativeType = null, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotContainsOnly(...\func_get_args());
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
 * @throws Exception
 */
function assertAttributeNotContainsOnly(string $type, string $haystackAttributeName, $haystackClassOrObject, ?bool $isNativeType = null, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeNotContainsOnly(...\func_get_args());
}
/**
 * Asserts the number of elements of an array, Countable or Traversable.
 *
 * @param int                $expectedCount
 * @param Countable|iterable $haystack
 * @param string             $message
 *
 * @throws Exception
 */
function assertCount(int $expectedCount, $haystack, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertCount(...\func_get_args());
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
 * @throws Exception
 */
function assertAttributeCount(int $expectedCount, string $haystackAttributeName, $haystackClassOrObject, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeCount(...\func_get_args());
}
/**
 * Asserts the number of elements of an array, Countable or Traversable.
 *
 * @param int                $expectedCount
 * @param Countable|iterable $haystack
 * @param string             $message
 *
 * @throws Exception
 */
function assertNotCount(int $expectedCount, $haystack, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotCount(...\func_get_args());
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
 * @throws Exception
 */
function assertAttributeNotCount(int $expectedCount, string $haystackAttributeName, $haystackClassOrObject, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeNotCount(...\func_get_args());
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
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertEquals($expected, $actual, string $message = '', float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = \false, bool $ignoreCase = \false) : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertEquals(...\func_get_args());
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
 * @throws Exception
 */
function assertAttributeEquals($expected, string $actualAttributeName, $actualClassOrObject, string $message = '', float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = \false, bool $ignoreCase = \false) : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeEquals(...\func_get_args());
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
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertNotEquals($expected, $actual, string $message = '', $delta = 0.0, $maxDepth = 10, $canonicalize = \false, $ignoreCase = \false) : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotEquals(...\func_get_args());
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
 * @throws Exception
 */
function assertAttributeNotEquals($expected, string $actualAttributeName, $actualClassOrObject, string $message = '', float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = \false, bool $ignoreCase = \false) : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeNotEquals(...\func_get_args());
}
/**
 * Asserts that a variable is empty.
 *
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertEmpty($actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertEmpty(...\func_get_args());
}
/**
 * Asserts that a static attribute of a class or an attribute of an object
 * is empty.
 *
 * @param string        $haystackAttributeName
 * @param object|string $haystackClassOrObject
 * @param string        $message
 *
 * @throws Exception
 */
function assertAttributeEmpty(string $haystackAttributeName, $haystackClassOrObject, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeEmpty(...\func_get_args());
}
/**
 * Asserts that a variable is not empty.
 *
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertNotEmpty($actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotEmpty(...\func_get_args());
}
/**
 * Asserts that a static attribute of a class or an attribute of an object
 * is not empty.
 *
 * @param string        $haystackAttributeName
 * @param object|string $haystackClassOrObject
 * @param string        $message
 *
 * @throws Exception
 */
function assertAttributeNotEmpty(string $haystackAttributeName, $haystackClassOrObject, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeNotEmpty(...\func_get_args());
}
/**
 * Asserts that a value is greater than another value.
 *
 * @param mixed  $expected
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertGreaterThan($expected, $actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertGreaterThan(...\func_get_args());
}
/**
 * Asserts that an attribute is greater than another value.
 *
 * @param mixed         $expected
 * @param string        $actualAttributeName
 * @param object|string $actualClassOrObject
 * @param string        $message
 *
 * @throws Exception
 */
function assertAttributeGreaterThan($expected, string $actualAttributeName, $actualClassOrObject, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeGreaterThan(...\func_get_args());
}
/**
 * Asserts that a value is greater than or equal to another value.
 *
 * @param mixed  $expected
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertGreaterThanOrEqual($expected, $actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertGreaterThanOrEqual(...\func_get_args());
}
/**
 * Asserts that an attribute is greater than or equal to another value.
 *
 * @param mixed         $expected
 * @param string        $actualAttributeName
 * @param object|string $actualClassOrObject
 * @param string        $message
 *
 * @throws Exception
 */
function assertAttributeGreaterThanOrEqual($expected, string $actualAttributeName, $actualClassOrObject, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeGreaterThanOrEqual(...\func_get_args());
}
/**
 * Asserts that a value is smaller than another value.
 *
 * @param mixed  $expected
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertLessThan($expected, $actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertLessThan(...\func_get_args());
}
/**
 * Asserts that an attribute is smaller than another value.
 *
 * @param mixed         $expected
 * @param string        $actualAttributeName
 * @param object|string $actualClassOrObject
 * @param string        $message
 *
 * @throws Exception
 */
function assertAttributeLessThan($expected, string $actualAttributeName, $actualClassOrObject, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeLessThan(...\func_get_args());
}
/**
 * Asserts that a value is smaller than or equal to another value.
 *
 * @param mixed  $expected
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertLessThanOrEqual($expected, $actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertLessThanOrEqual(...\func_get_args());
}
/**
 * Asserts that an attribute is smaller than or equal to another value.
 *
 * @param mixed         $expected
 * @param string        $actualAttributeName
 * @param object|string $actualClassOrObject
 * @param string        $message
 *
 * @throws Exception
 */
function assertAttributeLessThanOrEqual($expected, string $actualAttributeName, $actualClassOrObject, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeLessThanOrEqual(...\func_get_args());
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
 * @throws Exception
 */
function assertFileEquals(string $expected, string $actual, string $message = '', bool $canonicalize = \false, bool $ignoreCase = \false) : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertFileEquals(...\func_get_args());
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
 * @throws Exception
 */
function assertFileNotEquals(string $expected, string $actual, string $message = '', bool $canonicalize = \false, bool $ignoreCase = \false) : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertFileNotEquals(...\func_get_args());
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
 * @throws Exception
 */
function assertStringEqualsFile(string $expectedFile, string $actualString, string $message = '', bool $canonicalize = \false, bool $ignoreCase = \false) : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertStringEqualsFile(...\func_get_args());
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
 * @throws Exception
 */
function assertStringNotEqualsFile(string $expectedFile, string $actualString, string $message = '', bool $canonicalize = \false, bool $ignoreCase = \false) : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertStringNotEqualsFile(...\func_get_args());
}
/**
 * Asserts that a file/dir is readable.
 *
 * @param string $filename
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertIsReadable(string $filename, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertIsReadable(...\func_get_args());
}
/**
 * Asserts that a file/dir exists and is not readable.
 *
 * @param string $filename
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertNotIsReadable(string $filename, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotIsReadable(...\func_get_args());
}
/**
 * Asserts that a file/dir exists and is writable.
 *
 * @param string $filename
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertIsWritable(string $filename, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertIsWritable(...\func_get_args());
}
/**
 * Asserts that a file/dir exists and is not writable.
 *
 * @param string $filename
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertNotIsWritable(string $filename, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotIsWritable(...\func_get_args());
}
/**
 * Asserts that a directory exists.
 *
 * @param string $directory
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertDirectoryExists(string $directory, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertDirectoryExists(...\func_get_args());
}
/**
 * Asserts that a directory does not exist.
 *
 * @param string $directory
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertDirectoryNotExists(string $directory, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertDirectoryNotExists(...\func_get_args());
}
/**
 * Asserts that a directory exists and is readable.
 *
 * @param string $directory
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertDirectoryIsReadable(string $directory, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertDirectoryIsReadable(...\func_get_args());
}
/**
 * Asserts that a directory exists and is not readable.
 *
 * @param string $directory
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertDirectoryNotIsReadable(string $directory, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertDirectoryNotIsReadable(...\func_get_args());
}
/**
 * Asserts that a directory exists and is writable.
 *
 * @param string $directory
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertDirectoryIsWritable(string $directory, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertDirectoryIsWritable(...\func_get_args());
}
/**
 * Asserts that a directory exists and is not writable.
 *
 * @param string $directory
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertDirectoryNotIsWritable(string $directory, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertDirectoryNotIsWritable(...\func_get_args());
}
/**
 * Asserts that a file exists.
 *
 * @param string $filename
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertFileExists(string $filename, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertFileExists(...\func_get_args());
}
/**
 * Asserts that a file does not exist.
 *
 * @param string $filename
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertFileNotExists(string $filename, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertFileNotExists(...\func_get_args());
}
/**
 * Asserts that a file exists and is readable.
 *
 * @param string $file
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertFileIsReadable(string $file, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertFileIsReadable(...\func_get_args());
}
/**
 * Asserts that a file exists and is not readable.
 *
 * @param string $file
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertFileNotIsReadable(string $file, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertFileNotIsReadable(...\func_get_args());
}
/**
 * Asserts that a file exists and is writable.
 *
 * @param string $file
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertFileIsWritable(string $file, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertFileIsWritable(...\func_get_args());
}
/**
 * Asserts that a file exists and is not writable.
 *
 * @param string $file
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertFileNotIsWritable(string $file, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertFileNotIsWritable(...\func_get_args());
}
/**
 * Asserts that a condition is true.
 *
 * @param mixed  $condition
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertTrue($condition, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertTrue(...\func_get_args());
}
/**
 * Asserts that a condition is not true.
 *
 * @param mixed  $condition
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertNotTrue($condition, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotTrue(...\func_get_args());
}
/**
 * Asserts that a condition is false.
 *
 * @param mixed  $condition
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertFalse($condition, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertFalse(...\func_get_args());
}
/**
 * Asserts that a condition is not false.
 *
 * @param mixed  $condition
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertNotFalse($condition, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotFalse(...\func_get_args());
}
/**
 * Asserts that a variable is null.
 *
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertNull($actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNull(...\func_get_args());
}
/**
 * Asserts that a variable is not null.
 *
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertNotNull($actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotNull(...\func_get_args());
}
/**
 * Asserts that a variable is finite.
 *
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertFinite($actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertFinite(...\func_get_args());
}
/**
 * Asserts that a variable is infinite.
 *
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertInfinite($actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertInfinite(...\func_get_args());
}
/**
 * Asserts that a variable is nan.
 *
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertNan($actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNan(...\func_get_args());
}
/**
 * Asserts that a class has a specified attribute.
 *
 * @param string $attributeName
 * @param string $className
 * @param string $message
 *
 * @throws Exception
 */
function assertClassHasAttribute(string $attributeName, string $className, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertClassHasAttribute(...\func_get_args());
}
/**
 * Asserts that a class does not have a specified attribute.
 *
 * @param string $attributeName
 * @param string $className
 * @param string $message
 *
 * @throws Exception
 */
function assertClassNotHasAttribute(string $attributeName, string $className, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertClassNotHasAttribute(...\func_get_args());
}
/**
 * Asserts that a class has a specified static attribute.
 *
 * @param string $attributeName
 * @param string $className
 * @param string $message
 *
 * @throws Exception
 */
function assertClassHasStaticAttribute(string $attributeName, string $className, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertClassHasStaticAttribute(...\func_get_args());
}
/**
 * Asserts that a class does not have a specified static attribute.
 *
 * @param string $attributeName
 * @param string $className
 * @param string $message
 *
 * @throws Exception
 */
function assertClassNotHasStaticAttribute(string $attributeName, string $className, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertClassNotHasStaticAttribute(...\func_get_args());
}
/**
 * Asserts that an object has a specified attribute.
 *
 * @param string $attributeName
 * @param object $object
 * @param string $message
 *
 * @throws Exception
 */
function assertObjectHasAttribute(string $attributeName, $object, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertObjectHasAttribute(...\func_get_args());
}
/**
 * Asserts that an object does not have a specified attribute.
 *
 * @param string $attributeName
 * @param object $object
 * @param string $message
 *
 * @throws Exception
 */
function assertObjectNotHasAttribute(string $attributeName, $object, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertObjectNotHasAttribute(...\func_get_args());
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
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertSame($expected, $actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertSame(...\func_get_args());
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
 * @throws Exception
 */
function assertAttributeSame($expected, string $actualAttributeName, $actualClassOrObject, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeSame(...\func_get_args());
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
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertNotSame($expected, $actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotSame(...\func_get_args());
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
 * @throws Exception
 */
function assertAttributeNotSame($expected, string $actualAttributeName, $actualClassOrObject, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeNotSame(...\func_get_args());
}
/**
 * Asserts that a variable is of a given type.
 *
 * @param string $expected
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 */
function assertInstanceOf(string $expected, $actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertInstanceOf(...\func_get_args());
}
/**
 * Asserts that an attribute is of a given type.
 *
 * @param string        $expected
 * @param string        $attributeName
 * @param object|string $classOrObject
 * @param string        $message
 *
 * @throws Exception
 */
function assertAttributeInstanceOf(string $expected, string $attributeName, $classOrObject, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeInstanceOf(...\func_get_args());
}
/**
 * Asserts that a variable is not of a given type.
 *
 * @param string $expected
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 */
function assertNotInstanceOf(string $expected, $actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotInstanceOf(...\func_get_args());
}
/**
 * Asserts that an attribute is of a given type.
 *
 * @param string        $expected
 * @param string        $attributeName
 * @param object|string $classOrObject
 * @param string        $message
 *
 * @throws Exception
 */
function assertAttributeNotInstanceOf(string $expected, string $attributeName, $classOrObject, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeNotInstanceOf(...\func_get_args());
}
/**
 * Asserts that a variable is of a given type.
 *
 * @param string $expected
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertInternalType(string $expected, $actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertInternalType(...\func_get_args());
}
/**
 * Asserts that an attribute is of a given type.
 *
 * @param string        $expected
 * @param string        $attributeName
 * @param object|string $classOrObject
 * @param string        $message
 *
 * @throws Exception
 */
function assertAttributeInternalType(string $expected, string $attributeName, $classOrObject, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeInternalType(...\func_get_args());
}
/**
 * Asserts that a variable is not of a given type.
 *
 * @param string $expected
 * @param mixed  $actual
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertNotInternalType(string $expected, $actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotInternalType(...\func_get_args());
}
/**
 * Asserts that an attribute is of a given type.
 *
 * @param string        $expected
 * @param string        $attributeName
 * @param object|string $classOrObject
 * @param string        $message
 *
 * @throws Exception
 */
function assertAttributeNotInternalType(string $expected, string $attributeName, $classOrObject, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertAttributeNotInternalType(...\func_get_args());
}
/**
 * Asserts that a string matches a given regular expression.
 *
 * @param string $pattern
 * @param string $string
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertRegExp(string $pattern, string $string, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertRegExp(...\func_get_args());
}
/**
 * Asserts that a string does not match a given regular expression.
 *
 * @param string $pattern
 * @param string $string
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertNotRegExp(string $pattern, string $string, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotRegExp(...\func_get_args());
}
/**
 * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
 * is the same.
 *
 * @param Countable|iterable $expected
 * @param Countable|iterable $actual
 * @param string             $message
 *
 * @throws Exception
 */
function assertSameSize($expected, $actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertSameSize(...\func_get_args());
}
/**
 * Assert that the size of two arrays (or `Countable` or `Traversable` objects)
 * is not the same.
 *
 * @param Countable|iterable $expected
 * @param Countable|iterable $actual
 * @param string             $message
 *
 * @throws Exception
 */
function assertNotSameSize($expected, $actual, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertNotSameSize(...\func_get_args());
}
/**
 * Asserts that a string matches a given format string.
 *
 * @param string $format
 * @param string $string
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertStringMatchesFormat(string $format, string $string, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertStringMatchesFormat(...\func_get_args());
}
/**
 * Asserts that a string does not match a given format string.
 *
 * @param string $format
 * @param string $string
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertStringNotMatchesFormat(string $format, string $string, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertStringNotMatchesFormat(...\func_get_args());
}
/**
 * Asserts that a string matches a given format file.
 *
 * @param string $formatFile
 * @param string $string
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertStringMatchesFormatFile(string $formatFile, string $string, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertStringMatchesFormatFile(...\func_get_args());
}
/**
 * Asserts that a string does not match a given format string.
 *
 * @param string $formatFile
 * @param string $string
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertStringNotMatchesFormatFile(string $formatFile, string $string, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertStringNotMatchesFormatFile(...\func_get_args());
}
/**
 * Asserts that a string starts with a given prefix.
 *
 * @param string $prefix
 * @param string $string
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertStringStartsWith(string $prefix, string $string, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertStringStartsWith(...\func_get_args());
}
/**
 * Asserts that a string starts not with a given prefix.
 *
 * @param string $prefix
 * @param string $string
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertStringStartsNotWith($prefix, $string, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertStringStartsNotWith(...\func_get_args());
}
/**
 * Asserts that a string ends with a given suffix.
 *
 * @param string $suffix
 * @param string $string
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertStringEndsWith(string $suffix, string $string, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertStringEndsWith(...\func_get_args());
}
/**
 * Asserts that a string ends not with a given suffix.
 *
 * @param string $suffix
 * @param string $string
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertStringEndsNotWith(string $suffix, string $string, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertStringEndsNotWith(...\func_get_args());
}
/**
 * Asserts that two XML files are equal.
 *
 * @param string $expectedFile
 * @param string $actualFile
 * @param string $message
 *
 * @throws Exception
 */
function assertXmlFileEqualsXmlFile(string $expectedFile, string $actualFile, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertXmlFileEqualsXmlFile(...\func_get_args());
}
/**
 * Asserts that two XML files are not equal.
 *
 * @param string $expectedFile
 * @param string $actualFile
 * @param string $message
 *
 * @throws Exception
 */
function assertXmlFileNotEqualsXmlFile(string $expectedFile, string $actualFile, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertXmlFileNotEqualsXmlFile(...\func_get_args());
}
/**
 * Asserts that two XML documents are equal.
 *
 * @param string             $expectedFile
 * @param DOMDocument|string $actualXml
 * @param string             $message
 *
 * @throws Exception
 */
function assertXmlStringEqualsXmlFile(string $expectedFile, $actualXml, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertXmlStringEqualsXmlFile(...\func_get_args());
}
/**
 * Asserts that two XML documents are not equal.
 *
 * @param string             $expectedFile
 * @param DOMDocument|string $actualXml
 * @param string             $message
 *
 * @throws Exception
 */
function assertXmlStringNotEqualsXmlFile(string $expectedFile, $actualXml, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertXmlStringNotEqualsXmlFile(...\func_get_args());
}
/**
 * Asserts that two XML documents are equal.
 *
 * @param DOMDocument|string $expectedXml
 * @param DOMDocument|string $actualXml
 * @param string             $message
 *
 * @throws Exception
 */
function assertXmlStringEqualsXmlString($expectedXml, $actualXml, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertXmlStringEqualsXmlString(...\func_get_args());
}
/**
 * Asserts that two XML documents are not equal.
 *
 * @param DOMDocument|string $expectedXml
 * @param DOMDocument|string $actualXml
 * @param string             $message
 *
 * @throws Exception
 */
function assertXmlStringNotEqualsXmlString($expectedXml, $actualXml, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertXmlStringNotEqualsXmlString(...\func_get_args());
}
/**
 * Asserts that a hierarchy of DOMElements matches.
 *
 * @param DOMElement $expectedElement
 * @param DOMElement $actualElement
 * @param bool       $checkAttributes
 * @param string     $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \PHPUnit\Framework\AssertionFailedError
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertEqualXMLStructure(\DOMElement $expectedElement, \DOMElement $actualElement, bool $checkAttributes = \false, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertEqualXMLStructure(...\func_get_args());
}
/**
 * Evaluates a PHPUnit\Framework\Constraint matcher object.
 *
 * @param mixed      $value
 * @param Constraint $constraint
 * @param string     $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertThat($value, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Constraint $constraint, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertThat(...\func_get_args());
}
/**
 * Asserts that a string is a valid JSON string.
 *
 * @param string $actualJson
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertJson(string $actualJson, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertJson(...\func_get_args());
}
/**
 * Asserts that two given JSON encoded objects or arrays are equal.
 *
 * @param string $expectedJson
 * @param string $actualJson
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertJsonStringEqualsJsonString(string $expectedJson, string $actualJson, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertJsonStringEqualsJsonString(...\func_get_args());
}
/**
 * Asserts that two given JSON encoded objects or arrays are not equal.
 *
 * @param string $expectedJson
 * @param string $actualJson
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertJsonStringNotEqualsJsonString($expectedJson, $actualJson, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertJsonStringNotEqualsJsonString(...\func_get_args());
}
/**
 * Asserts that the generated JSON encoded object and the content of the given file are equal.
 *
 * @param string $expectedFile
 * @param string $actualJson
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertJsonStringEqualsJsonFile(string $expectedFile, string $actualJson, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertJsonStringEqualsJsonFile(...\func_get_args());
}
/**
 * Asserts that the generated JSON encoded object and the content of the given file are not equal.
 *
 * @param string $expectedFile
 * @param string $actualJson
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertJsonStringNotEqualsJsonFile(string $expectedFile, string $actualJson, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertJsonStringNotEqualsJsonFile(...\func_get_args());
}
/**
 * Asserts that two JSON files are equal.
 *
 * @param string $expectedFile
 * @param string $actualFile
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertJsonFileEqualsJsonFile(string $expectedFile, string $actualFile, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertJsonFileEqualsJsonFile(...\func_get_args());
}
/**
 * Asserts that two JSON files are not equal.
 *
 * @param string $expectedFile
 * @param string $actualFile
 * @param string $message
 *
 * @throws Exception
 * @throws ExpectationFailedException
 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
 */
function assertJsonFileNotEqualsJsonFile(string $expectedFile, string $actualFile, string $message = '') : void
{
    \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::assertJsonFileNotEqualsJsonFile(...\func_get_args());
}
function logicalAnd() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalAnd
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::logicalAnd(...\func_get_args());
}
function logicalOr() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalOr
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::logicalOr(...\func_get_args());
}
function logicalNot(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Constraint $constraint) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalNot
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::logicalNot(...\func_get_args());
}
function logicalXor() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalXor
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::logicalXor(...\func_get_args());
}
function anything() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsAnything
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::anything();
}
function isTrue() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsTrue
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::isTrue();
}
function callback(callable $callback) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Callback
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::callback(...\func_get_args());
}
function isFalse() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsFalse
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::isFalse();
}
function isJson() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsJson
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::isJson();
}
function isNull() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsNull
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::isNull();
}
function isFinite() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsFinite
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::isFinite();
}
function isInfinite() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsInfinite
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::isInfinite();
}
function isNan() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsNan
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::isNan();
}
function attribute(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Constraint $constraint, string $attributeName) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Attribute
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::attribute(...\func_get_args());
}
function contains($value, bool $checkForObjectIdentity = \true, bool $checkForNonObjectIdentity = \false) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContains
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::contains(...\func_get_args());
}
function containsOnly(string $type) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContainsOnly
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::containsOnly(...\func_get_args());
}
function containsOnlyInstancesOf(string $className) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\TraversableContainsOnly
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::containsOnlyInstancesOf(...\func_get_args());
}
function arrayHasKey($key) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ArrayHasKey
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::arrayHasKey(...\func_get_args());
}
function equalTo($value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = \false, bool $ignoreCase = \false) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsEqual
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::equalTo(...\func_get_args());
}
function attributeEqualTo(string $attributeName, $value, float $delta = 0.0, int $maxDepth = 10, bool $canonicalize = \false, bool $ignoreCase = \false) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Attribute
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::attributeEqualTo(...\func_get_args());
}
function isEmpty() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsEmpty
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::isEmpty();
}
function isWritable() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsWritable
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::isWritable();
}
function isReadable() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsReadable
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::isReadable();
}
function directoryExists() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\DirectoryExists
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::directoryExists();
}
function fileExists() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\FileExists
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::fileExists();
}
function greaterThan($value) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\GreaterThan
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::greaterThan(...\func_get_args());
}
function greaterThanOrEqual($value) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalOr
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::greaterThanOrEqual(...\func_get_args());
}
function classHasAttribute(string $attributeName) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ClassHasAttribute
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::classHasAttribute(...\func_get_args());
}
function classHasStaticAttribute(string $attributeName) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ClassHasStaticAttribute
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::classHasStaticAttribute(...\func_get_args());
}
function objectHasAttribute($attributeName) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\ObjectHasAttribute
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::objectHasAttribute(...\func_get_args());
}
function identicalTo($value) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsIdentical
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::identicalTo(...\func_get_args());
}
function isInstanceOf(string $className) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsInstanceOf
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::isInstanceOf(...\func_get_args());
}
function isType(string $type) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\IsType
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::isType(...\func_get_args());
}
function lessThan($value) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LessThan
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::lessThan(...\func_get_args());
}
function lessThanOrEqual($value) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\LogicalOr
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::lessThanOrEqual(...\func_get_args());
}
function matchesRegularExpression(string $pattern) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\RegularExpression
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::matchesRegularExpression(...\func_get_args());
}
function matches(string $string) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringMatchesFormatDescription
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::matches(...\func_get_args());
}
function stringStartsWith($prefix) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringStartsWith
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::stringStartsWith(...\func_get_args());
}
function stringContains(string $string, bool $case = \true) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringContains
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::stringContains(...\func_get_args());
}
function stringEndsWith(string $suffix) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\StringEndsWith
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::stringEndsWith(...\func_get_args());
}
function countOf(int $count) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Count
{
    return \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Assert::countOf(...\func_get_args());
}
/**
 * Returns a matcher that matches when the method is executed
 * zero or more times.
 */
function any() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\AnyInvokedCount
{
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\AnyInvokedCount();
}
/**
 * Returns a matcher that matches when the method is never executed.
 */
function never() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedCount
{
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedCount(0);
}
/**
 * Returns a matcher that matches when the method is executed
 * at least N times.
 *
 * @param int $requiredInvocations
 */
function atLeast($requiredInvocations) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedAtLeastCount
{
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedAtLeastCount($requiredInvocations);
}
/**
 * Returns a matcher that matches when the method is executed at least once.
 */
function atLeastOnce() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedAtLeastOnce
{
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedAtLeastOnce();
}
/**
 * Returns a matcher that matches when the method is executed exactly once.
 */
function once() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedCount
{
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedCount(1);
}
/**
 * Returns a matcher that matches when the method is executed
 * exactly $count times.
 *
 * @param int $count
 */
function exactly($count) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedCount
{
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedCount($count);
}
/**
 * Returns a matcher that matches when the method is executed
 * at most N times.
 *
 * @param int $allowedInvocations
 */
function atMost($allowedInvocations) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedAtMostCount
{
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedAtMostCount($allowedInvocations);
}
/**
 * Returns a matcher that matches when the method is executed
 * at the given index.
 *
 * @param int $index
 */
function at($index) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedAtIndex
{
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedAtIndex($index);
}
/**
 * @param mixed $value
 */
function returnValue($value) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnStub
{
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnStub($value);
}
/**
 * @param array $valueMap
 */
function returnValueMap(array $valueMap) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnValueMap
{
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnValueMap($valueMap);
}
/**
 * @param int $argumentIndex
 */
function returnArgument($argumentIndex) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnArgument
{
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnArgument($argumentIndex);
}
/**
 * @param mixed $callback
 */
function returnCallback($callback) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnCallback
{
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnCallback($callback);
}
/**
 * Returns the current object.
 *
 * This method is useful when mocking a fluent interface.
 */
function returnSelf() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnSelf
{
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ReturnSelf();
}
/**
 * @param Throwable $exception
 */
function throwException(\Throwable $exception) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\Exception
{
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\Exception($exception);
}
/**
 * @param mixed $value , ...
 */
function onConsecutiveCalls() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls
{
    $args = \func_get_args();
    return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls($args);
}
