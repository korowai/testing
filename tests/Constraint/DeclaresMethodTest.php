<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Tests\Testing\Constraint;

use Korowai\Testing\TestCase;
use Korowai\Testing\Constraint\DeclaresMethod;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class DeclaresMethodTest extends TestCase
{
    public function test__extends__Constraint(): void
    {
        $this->assertExtendsClass(Constraint::class, DeclaresMethod::class);
    }

    public function test__construct(): void
    {
        $this->assertInstanceOf(DeclaresMethod::class, new DeclaresMethod('foo'));
    }

    public function test__toString(): void
    {
        $this->assertSame('declares method foo()', (new DeclaresMethod('foo'))->toString());
    }

    public function test__evaluate__onObject(): void
    {
        $constraint = new DeclaresMethod('test__evaluate__onObject');
        $this->assertTrue($constraint->evaluate($this, '', true));
        $this->assertNull($constraint->evaluate($this, '', false));
        $this->assertThat($this, $constraint);
    }

    public function test__evaluate__onObjectWithFailure(): void
    {
        $constraint = new DeclaresMethod('assertThat');

        $this->assertFalse($constraint->evaluate($this, '', true));

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("Failed asserting that class ".__class__." declares method assertThat().");

        $this->assertThat($this, $constraint);
    }

    public function test__evaluate__onClass(): void
    {
        $constraint = new DeclaresMethod('test__evaluate__onClass');
        $this->assertTrue($constraint->evaluate($this, '', true));
        $this->assertNull($constraint->evaluate($this, '', false));
        $this->assertThat(self::class, $constraint);
    }

    public function test__evaluate__onClassWithFailure(): void
    {
        $constraint = new DeclaresMethod('assertThat');

        $this->assertFalse($constraint->evaluate($this, '', true));

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("Failed asserting that class ".__class__." declares method assertThat().");

        $this->assertThat(self::class, $constraint);
    }

    public function test__evaluate__onString(): void
    {
        $constraint = new DeclaresMethod('test__evaluate__onString');
        $this->assertFalse($constraint->evaluate('@##@##', '', true));
    }

    public function test__evaluate__onStringWithFailure(): void
    {
        $constraint = new DeclaresMethod('test__evaluate__onStringWithFailure');

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("Failed asserting that string declares method test__evaluate__onStringWithFailure().");
        $this->assertThat('@##@##', $constraint);
    }

    public function test__evaluate__onArray(): void
    {
        $constraint = new DeclaresMethod('test__evaluate__onArray');
        $this->assertFalse($constraint->evaluate([], '', true));
    }

    public function test__evaluate__onArrayWithFailure(): void
    {
        $constraint = new DeclaresMethod('test__evaluate__onArrayWithFailure');

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("Failed asserting that array declares method test__evaluate__onArrayWithFailure().");
        $this->assertThat([], $constraint);
    }
}

// vim: syntax=php sw=4 ts=4 et:
