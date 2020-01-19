<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Tests\Testing\Examples;

use Korowai\Testing\TestCase;
use Korowai\Testing\Examples\ExampleBazTrait;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ExampleBazTraitTest extends TestCase
{
    public function getTestObject()
    {
        return new class {
            use ExampleBazTrait;
        };
    }

    public function test__setBaz()
    {
        $object = $this->getTestObject();
        $this->assertNull($object->getBaz());
        $object->setBaz('BAZ');
        $this->assertSame('BAZ', $object->getBaz());
    }
}

// vim: syntax=php sw=4 ts=4 et:
