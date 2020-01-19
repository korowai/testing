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
 * Accepts objects or classes that declare a method given by name.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
final class DeclaresMethod extends Constraint
{
    /**
     * @var string
     */
    private $expected;

    /**
     * Constraint that asserts that the class or object it is evaluated for
     * declares a given method. Note, that the meaning of "declares" is
     * different from "has".
     *
     * Initializes the constraint.
     *
     * @param  string $expected
     *      Method name.
     */
    public function __construct(string $expected)
    {
        $this->expected = $expected;
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString() : string
    {
        return 'declares method '.$this->expected.'()';
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param  mixed $other value or object to evaluate
     */
    protected function matches($other) : bool
    {
        $class = is_object($other) ? get_class($other) : $other;
        try {
            $reflection = new \ReflectionMethod($class, $this->expected);
        } catch (\ReflectionException $exception) {
            return false;
        }
        return $reflection->getDeclaringClass()->getName() === $class;
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param  mixed $other evaluated value or object
     * @return string
     */
    protected function failureDescription($other) : string
    {
        return $this->getSubjectString($other).' '.$this->toString();
    }

    /**
     * Returns a string describing $other.
     *
     * @param  mixed $other
     * @return string
     */
    protected function getSubjectString($other) : string
    {
        if (is_object($other) || is_string($other)) {
            try {
                $reflection = new \ReflectionClass($other);
                $kind = ($reflection->isInterface() ? 'interface' : ($reflection->isTrait() ? 'trait' : 'class'));
                return $kind.' '.$reflection->getName();
            } catch (\ReflectionException $exception) {
                return gettype($other);
            }
        } else {
            return gettype($other);
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
