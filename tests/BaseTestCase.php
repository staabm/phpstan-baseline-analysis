<?php

namespace staabm\PHPStanBaselineAnalysis\Tests;

use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase {
    /**
     * Asserts that two variables have the same type and value.
     * Used on objects, it asserts that two variables reference
     * the same object.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     *
     * @param mixed $actual
     *
     * @psalm-template ExpectedType
     * @psalm-param ExpectedType $expected
     * @psalm-assert =ExpectedType $actual
     */
    public static function assertSame($expected, $actual, string $message = ''): void
    {
        $expected = self::normalizeNewlines($expected);
        $actual = self::normalizeNewlines($actual);

        static::assertThat(
            $actual,
            new IsIdentical($expected),
            $message
        );
    }

    static private function normalizeNewlines(string $string):string {
        return str_replace("\r\n", "\n", $string);
    }
}