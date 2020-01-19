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
use Korowai\Testing\Examples\ExampleFooClass;
use Korowai\Testing\Examples\ExampleFooInterface;
use Korowai\Testing\Examples\ExampleBazTrait;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ExampleFooClassTest extends TestCase
{
    public function test__implements__ExampleFooInterface()
    {
        $this->assertImplementsInterface(ExampleFooInterface::class, ExampleFooClass::class);
    }

    public function test__uses__ExampleBazTrait()
    {
        $this->assertUsesTrait(ExampleBazTrait::class, ExampleFooClass::class);
    }

    public function test__construct()
    {
        $object = new ExampleFooClass(['foo' => 'FOO', 'baz' => 'BAZ']);
        $this->assertSame('FOO', $object->getFoo());
        $this->assertSame('BAZ', $object->getBaz());
    }

    public function test__setFoo()
    {
        $object = new ExampleFooClass();
        $this->assertNull($object->getFoo());
        $object->setFoo('FOO');
        $this->assertSame('FOO', $object->getFoo());
    }

    public function test__setBaz()
    {
        $object = new ExampleFooClass();
        $this->assertNull($object->getBaz());
        $object->setBaz('BAZ');
        $this->assertSame('BAZ', $object->getBaz());
    }
}

// vim: syntax=php sw=4 ts=4 et:
