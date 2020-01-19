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

use Korowai\Testing\Assertions\ClassAssertions;
use Korowai\Testing\Assertions\ObjectPropertiesAssertions;
use Korowai\Testing\Assertions\PregAssertions;
use Korowai\Testing\Assertions\ComplexAssertions;
use Korowai\Testing\Traits\ObjectPropertiesUtils;
use Korowai\Testing\Traits\PregUtils;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use ClassAssertions;
    use ObjectPropertiesAssertions;
    use PregAssertions;
    use ComplexAssertions;
    use ObjectPropertiesUtils;
    use PregUtils;

    /**
     * Returns a key-value array which maps class names onto arrays of property
     * getters. Each array of property getters is a key-value array with keys
     * being property names and values being names of corresponding getter methods.
     *
     * @return array
     */
    public static function objectPropertyGettersMap() : array
    {
        return [];
    }
}

// vim: syntax=php sw=4 ts=4 et:
