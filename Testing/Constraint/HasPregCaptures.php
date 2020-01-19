<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Testing\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * Constraint that accepts arrays of matches returned from ``preg_match()``
 * having capture groups as specified in the array of expectations.
 *
 * Checks only entries present in the array of expectations. Special values may
 * be used in the expectations:
 *
 * - ``['foo' => false]`` asserts that group ``'foo'`` was not captured,
 * - ``['foo' => true]`` asserts that group ``'foo'`` was captured,
 * - ``['foo' => 'FOO']`` asserts that group ``'foo'`` was captured and it's value equals ``'FOO'``.
 *
 * Boolean expectations (``['foo' => true]`` or ``['foo' => false]``) work
 * properly only with arrays obtained from ``preg_match()`` invoked with
 * ``PREG_UNMATCHED_AS_NULL`` flag.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
final class HasPregCaptures extends Constraint
{
    /**
     * @var array
     */
    private $expected;

    /**
     * Initializes the constraint.
     *
     * @param  array $expected An array of expected values.
     */
    public function __construct(array $expected)
    {
        $this->expected = $expected;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString() : string
    {
        return 'has expected PCRE capture groups';
    }

    /**
     * Evaluates the constraint for parameter $other
     *
     * If $returnResult is set to false (the default), an exception is thrown
     * in case of a failure. null is returned otherwise.
     *
     * If $returnResult is true, the result of the evaluation is returned as
     * a boolean value instead: true in case of success, false in case of a
     * failure.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function evaluate($other, string $description = '', bool $returnResult = false): ?bool
    {
        $success = $this->matches($other);

        if ($returnResult) {
            return $success;
        }

        if (!$success) {
            $f = null;

            if (is_array($other)) {
                [$expected, $actual] = $this->getArraysForComparison($other);
                $f = new ComparisonFailure(
                    $this->expected,
                    $other,
                    $this->exporter()->export($expected),
                    $this->exporter()->export($actual)
                );
            }

            $this->fail($other, $description, $f);
        }

        return null;
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param  mixed $other value or object to evaluate
     */
    public function matches($other) : bool
    {
        if (!is_array($other)) {
            return false;
        }
        [$expected, $actual] = $this->getArraysForComparison($other);
        return ($expected === $actual);
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param  mixed $other evaluated value or object
     */
    public function failureDescription($other) : string
    {
        if (is_object($other)) {
            $what = 'object '.get_class($other);
        } else {
            $what = gettype($other);
        }
        return $what.' '.$this->toString();
    }

    private function getArraysForComparison(array $matches)
    {
        [$expect, $actual] = [[], []];
        foreach ($this->expected as $key => $value) {
            self::updateExpectForComparison($expect, $matches, $key, $value);
            self::updateActualForComparison($actual, $matches, $key);
        }
        return [$expect, $actual];
    }

    private static function updateExpectForComparison(array &$expect, array $matches, $key, $value)
    {
        $exists = ($matches[$key] ?? [null, -1])[0] !== null;
        if ($value === $exists) {
            if (array_key_exists($key, $matches)) {
                $expect[$key] = $matches[$key];
            }
        } else {
            $expect[$key] = $value;
        }
    }

    private static function updateActualForComparison(array &$actual, array $matches, $key)
    {
        if (array_key_exists($key, $matches)) {
            $actual[$key] = $matches[$key];
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
