<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Tests\Testing;

use Korowai\Testing\TestCase;
use Korowai\Testing\Assertions\ClassAssertions;
use Korowai\Testing\Assertions\ObjectPropertiesAssertions;
use Korowai\Testing\Assertions\PregAssertions;
use Korowai\Testing\Assertions\ComplexAssertions;
use Korowai\Testing\Traits\ObjectPropertiesUtils;
use Korowai\Testing\Traits\PregUtils;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class TestCaseTest extends TestCase
{
    public function test__uses__ClassAssertions()
    {
        $this->assertUsesTrait(ClassAssertions::class, TestCase::class);
    }

    public function test__uses__ObjectPropertiesAssertions()
    {
        $this->assertUsesTrait(ObjectPropertiesAssertions::class, TestCase::class);
    }

    public function test__uses__PregAssertions()
    {
        $this->assertUsesTrait(PregAssertions::class, TestCase::class);
    }

    public function test__uses__ComplexAssertions()
    {
        $this->assertUsesTrait(ComplexAssertions::class, TestCase::class);
    }

    public function test__uses__ObjectPropertiesUtils()
    {
        $this->assertUsesTrait(ObjectPropertiesUtils::class, TestCase::class);
    }

    public function test__uses__PregUtils()
    {
        $this->assertUsesTrait(PregUtils::class, TestCase::class);
    }

    public function test__objectPropertyGettersMap()
    {
        $this->assertSame([], TestCase::objectPropertyGettersMap());
    }
}

// vim: syntax=php sw=4 ts=4 et:
