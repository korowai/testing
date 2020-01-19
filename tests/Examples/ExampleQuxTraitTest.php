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
use Korowai\Testing\Examples\ExampleQuxTrait;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ExampleQuxTraitTest extends TestCase
{
    public function getTestObject()
    {
        return new class {
            use ExampleQuxTrait;
        };
    }

    public function test__setQux()
    {
        $object = $this->getTestObject();
        $this->assertNull($object->getQux());
        $object->setQux('QUX');
        $this->assertSame('QUX', $object->getQux());

        $object->qux = 'qUx';
        $this->assertSame('qUx', $object->getQux());
    }
}

// vim: syntax=php sw=4 ts=4 et:
