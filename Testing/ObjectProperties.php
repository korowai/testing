<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Testing;

/**
 * Specifies properties of an object.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
final class ObjectProperties extends \ArrayObject implements ObjectPropertiesInterface
{
    /**
     * Initializes the object.
     */
    public function __construct(array $properties)
    {
        parent::__construct($properties);
    }

    /**
     * {@inheritdoc}
     */
    public function getArrayForComparison() : array
    {
        $array = $this->getArrayCopy();
        array_walk_recursive($array, function (&$v, $k) {
            $v = $this->getValueForComparison($v);
        });
        return $array;
    }

    private function getValueForComparison($value)
    {
        return ($value instanceof ObjectPropertiesInterface) ? $value->getArrayForComparison() : $value;
    }
}

// vim: syntax=php sw=4 ts=4 et:
