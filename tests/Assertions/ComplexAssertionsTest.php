<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Tests\Testing\Assertions;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\ExpectationFailedException;
use Korowai\Testing\Assertions\ComplexAssertions;
use Korowai\Testing\Examples\ExampleFooClass;
use Korowai\Testing\Examples\ExampleBarClass;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ComplexAssertionsTest extends TestCase
{
    use ComplexAssertions;

    // Required by the trait.
    public static function getObjectProperty(object $object, string $key, array $getters = null)
    {
        $getter = substr($key, -2) === '()' ? substr($key, 0, -2) : $getters[$key] ?? null;
        if ($getter !== null) {
            return call_user_func([$object, $getter]);
        } else {
            return $object->{$key};
        }
    }

    public function staticMethodsThatMustAppear()
    {
        return [
            ['assertObjectEachProperty'],
            ['assertObjectEachPropertyArrayValue'],
            ['assertArrayEachValue'],
        ];
    }

    /**
     * @dataProvider staticMethodsThatMustAppear
     */
    public function test__staticMethodExists(string $name)
    {
        $classAndMethod = [self::class, $name];
        self::assertTrue(method_exists(...$classAndMethod));
        $method = new \ReflectionMethod(...$classAndMethod);
        self::assertTrue($method->isStatic());
    }

    public function test__assertObjectEachProperty()
    {
        $test = $this->getMockBuilder('TestCaseMock')
                     ->setMethods(['assertFoo', 'assertBar', 'assertQux'])
                     ->getMock();

        $test->expects($this->once())
             ->method('assertFoo')
             ->with('FOO', 'Foo', 'Lorem ipsum');

        $test->expects($this->once())
             ->method('assertBar')
             ->with('BAR', 'Bar', 'Lorem ipsum');

        $test->expects($this->never())
             ->method('assertQux');

        self::assertObjectEachProperty(
            [
                'getFoo()' => [$test, 'assertFoo'],
                'getBar()' => [$test, 'assertBar'],
                'qux'      => [$test, 'assertQux'],
            ],
            [
                'getFoo()' => 'FOO',
                'getBar()' => 'BAR',
                'baz' => 'BAZ'
            ],
            new ExampleBarClass([
                'foo' => 'Foo',
                'bar' => 'Bar',
                'baz' => 'Baz',
                'qux' => 'Qux'
            ]),
            'Lorem ipsum'
        );
    }

    public function test__assertObjectEachPropertyArrayValue()
    {
        $test = $this->getMockBuilder('TestCaseMock')
                     ->setMethods(['assertFoo', 'assertBar', 'assertQux'])
                     ->getMock();

        $test->expects($this->exactly(3))
             ->method('assertFoo')
             ->withConsecutive(
                 ['F', 'F', 'Lorem ipsum'],
                 ['O', 'o', 'Lorem ipsum'],
                 ['O', 'o', 'Lorem ipsum']
             );

        $test->expects($this->exactly(3))
             ->method('assertBar')
             ->withConsecutive(
                 ['B', 'B', 'Lorem ipsum'],
                 ['A', 'a', 'Lorem ipsum'],
                 ['R', 'r', 'Lorem ipsum']
             );

        $test->expects($this->never())
             ->method('assertQux');

        self::assertObjectEachPropertyArrayValue(
            [
                'getFoo()' => [$test, 'assertFoo'],
                'getBar()' => [$test, 'assertBar'],
                'qux'      => [$test, 'assertQux'],
            ],
            [
                'getFoo()' => ['F', 'O', 'O'],
                'getBar()' => ['B', 'A', 'R'],
                'baz'      => ['B', 'A', 'Z'],
            ],
            new ExampleBarClass([
                'foo'      => ['F', 'o', 'o'],
                'bar'      => ['B', 'a', 'r'],
                'baz'      => ['B', 'a', 'z'],
                'qux'      => ['Q', 'u', 'x']
            ]),
            'Lorem ipsum'
        );
    }

    public function test__assertArrayEachValue__withDifferentKeys()
    {
        $test = $this->getMockBuilder('TestCaseMock')
                     ->setMethods(['assertFoo'])
                     ->getMock();

        $test->expects($this->never())
             ->method('assertFoo');

        $regexp = '/^Lorem ipsum.\n'.
                    'Failed asserting that two arrays have identical keys.\n'.
                    'Failed asserting that two arrays are identical.$/';
        self::expectException(ExpectationFailedException::class);
        self::expectExceptionMessageMatches($regexp);

        self::assertArrayEachValue(
            [$test, 'assertFoo'],
            [0, 'a' => 'A', 'b' => 'B'],
            [0, 'b' => 'A', 'a' => 'B'],
            'Lorem ipsum.'
        );
    }

    public function test__assertArrayEachValue()
    {
        $test = $this->getMockBuilder('TestCaseMock')
                     ->setMethods(['assertFoo'])
                     ->getMock();

        $test->expects($this->exactly(3))
             ->method('assertFoo')
             ->withConsecutive(
                 [ 0,   0,  'Lorem ipsum'],
                 ['A', 'A', 'Lorem ipsum'],
                 ['B', 'C', 'Lorem ipsum']
             );

        self::assertArrayEachValue(
            [$test, 'assertFoo'],
            [0, 'a' => 'A', 'b' => 'B'],
            [0, 'a' => 'A', 'b' => 'C'],
            'Lorem ipsum'
        );
    }
}

// vim: syntax=php sw=4 ts=4 et:
