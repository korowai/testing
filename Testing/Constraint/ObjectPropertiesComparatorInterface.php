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

use Korowai\Testing\ObjectPropertiesInterface;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
interface ObjectPropertiesComparatorInterface
{
    /**
     * Returns actual *$object* properties to be used for comparison. The
     * purpose is to choose and adjust *$object* properties that should be used
     * by implementation for comparison. The returned object contains only
     * properties that are expected by this constraint. The method may also
     * apply its own transformations to particular properties.
     *
     * @param  object $object
     *
     * @return ObjectPropertiesInterface
     */
    public function getActualPropertiesForComparison(object $object) : ObjectPropertiesInterface;

    /**
     * Returns array of expected properties to be used for comparison. The
     * method may also apply its own transformations to particular properties.
     *
     * @return ObjectPropertiesInterface
     */
    public function getExpectedPropertiesForComparison() : ObjectPropertiesInterface;
}

// vim: syntax=php sw=4 ts=4 et:
