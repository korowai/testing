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

/**
 * Constraint that accepts classes that extend given class.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
final class ExtendsClass extends InheritanceConstraint
{
    /**
     * Returns short description of what we examine, e.g. ``'impements interface'``.
     *
     * @return string
     */
    public function getLeadingString() : string
    {
        return 'extends class';
    }

    /**
     * Returns an array of "inherited classes" -- eiher interfaces *$class*
     * implements, parent classes it extends or traits it uses, depending on
     * the actual implementation of this constraint.
     *
     * @param  string $class
     * @return array
     */
    public function getInheritedClassesFor(string $class) : array
    {
        return class_parents($class);
    }

    /**
     * Checks if *$class* may be used as an argument to ``getInheritedClassesFor()``
     *
     * @param  string $class
     * @return bool
     */
    public function supportsClass(string $class) : bool
    {
        return class_exists($class);
    }
}

// vim: syntax=php sw=4 ts=4 et:
