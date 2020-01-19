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

/**
 * Constraint that accepts classes that implement given interface.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
abstract class InheritanceConstraint extends Constraint
{
    /**
     * @var string
     */
    private $className;

    /**
     * Initializes the constraint.
     *
     * @param  string $className Name of the interface that is expected to be implemented by a class.
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * Returns short description of what we examine, e.g. ``'impements interface'``.
     *
     * @return string
     */
    abstract public function getLeadingString() : string;

    /**
     * Returns an array of "inherited classes" -- eiher interfaces *$class*
     * implements, parent classes it extends or traits it uses, depending on
     * the actual implementation of this constraint.
     *
     * @param  string $class
     * @return array
     */
    abstract public function getInheritedClassesFor(string $class) : array;

    /**
     * Checks if *$string* may be used as an argument to ``getInheritedClassesFor()``
     *
     * @param  string $string
     * @return bool
     */
    abstract public function supportsClass(string $string) : bool;

    /**
     * Returns a string representation of the constraint.
     */
    public function toString() : string
    {
        return sprintf('%s %s', $this->getLeadingString(), $this->className);
    }

    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * @param  mixed $other value or object to evaluate
     */
    public function matches($other) : bool
    {
        if (is_object($other)) {
            $other = get_class($other);
        }
        if (!is_string($other) || !$this->supportsClass($other)) {
            return false;
        }
        return in_array($this->className, $this->getInheritedClassesFor($other), true);
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
            $other = get_class($other).' object';
        } elseif (!is_string($other) || !$this->supportsClass($other)) {
            $other = $this->exporter()->export($other);
        }
        return $other.' '.$this->toString();
    }
}

// vim: syntax=php sw=4 ts=4 et:
