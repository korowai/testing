<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Tests\Testing\Traits;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\ExpectationFailedException;
use Korowai\Testing\Traits\ObjectPropertiesUtils;
use Korowai\Testing\Assertions\ObjectPropertiesAssertions;
use Korowai\Testing\Examples\ExampleFooInterface;
use Korowai\Testing\Examples\ExampleBarInterface;
use Korowai\Testing\Examples\ExampleFooClass;
use Korowai\Testing\Examples\ExampleBarClass;
use Korowai\Testing\Examples\ExampleBazTrait;
use Korowai\Testing\Examples\ExampleQuxTrait;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ObjectPropertiesUtilsTest extends TestCase
{
    use ObjectPropertiesUtils;

    private $foo;

    public function getFoo()
    {
        return $this->foo;
    }

    public $qux; // not in getters map.

    // Required by trait
    public static function objectPropertyGettersMap() : array
    {
        return [
            ExampleFooInterface::class => ['foo' => 'getFoo'],
            ExampleBarInterface::class => ['bar' => 'getBar'],
            ExampleBazTrait::class => ['baz' => 'getBaz'],
        ];
    }

    public static function getObjectPropertyGetters__cases()
    {
        return [
            // #0
            [
                'argument'  => ExampleFooInterface::class,
                'getters'   => [
                    'foo'   => 'getFoo'
                ],
            ],
            // #1
            [
                'argument'  => ExampleBarInterface::class,
                'getters'   => [
                    'bar'   => 'getBar'
                ],
            ],
            // #2
            [
                'argument'  => ExampleBazTrait::class,
                'getters'   => [
                    'baz'   => 'getBaz'
                ],
            ],
            // #3
            [
                'argument'  => ExampleQuxTrait::class,
                'getters'   => [
                ],
            ],
            // #4
            [
                'argument'  => ExampleFooClass::class,
                'getters'   => [
                    'foo'   => 'getFoo',
                    'baz'   => 'getBaz',
                ],
            ],
            // #5
            [
                'argument'  => ExampleBarClass::class,
                'getters'   => [
                    'foo'   => 'getFoo',
                    'bar'   => 'getBar',
                    'baz'   => 'getBaz',
                ],
            ],
            // #6
            [
                'argument'  => new ExampleFooClass(),
                'getters'   => [
                    'foo'   => 'getFoo',
                    'baz'   => 'getBaz',
                ]
            ],
            // #7
            [
                'argument'  => new ExampleBarClass(),
                'getters'   => [
                    'foo'   => 'getFoo',
                    'bar'   => 'getBar',
                    'baz'   => 'getBaz',
                ]
            ],
        ];
    }

    /**
     * @dataProvider getObjectPropertyGetters__cases
     */
    public function test__getObjectPropertyGetters($argument, array $getters)
    {
        $this->assertSame($getters, self::getObjectPropertyGetters($argument));
    }

    public function test__getObjectPropertyGetters__withInvalidArgumentType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Argument 1 to '.self::class.'::getObjectPropertyGetters() '.
            'must be of type object or string, array given.'
        );
        self::getObjectPropertyGetters([]);
    }

    public function test__getObjectPropertyGetters__withNonClassString()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Argument 1 to '.self::class.'::getObjectPropertyGetters() '.
            'must be an object or a class, interface, or trait name, '.
            '"Inexistent Planet" given.'
        );
        self::getObjectPropertyGetters('Inexistent Planet');
    }

    public static function getObjectProperty__cases()
    {
        return [
            // #0
            [
                'object' => new ExampleFooClass(['foo' => 'FOO', 'baz' => 'BAZ']),
                'expect' => [
                    'foo' => 'FOO', 'getFoo()' => 'FOO',
                    'baz' => 'BAZ', 'getBaz()' => 'BAZ'
                ],
            ],
            // #1
            [
                'object' => new ExampleBarClass(['foo' => 'FOO', 'bar' => 'BAR', 'baz' => 'BAZ', 'qux' => 'QUX']),
                'expect' => [
                    'foo' => 'FOO', 'getFoo()' => 'FOO',
                    'bar' => 'BAR', 'getBar()' => 'BAR',
                    'baz' => 'BAZ', 'getBaz()' => 'BAZ',
                    'qux' => 'QUX', 'getQux()' => 'QUX', // qux is public, so it works without getter
                ],
            ],
        ];
    }

    /**
     * @dataProvider getObjectProperty__cases
     */
    public function test__getObjectProperty(object $object, array $expect)
    {
        foreach ($expect as $key => $value) {
            $this->assertSame($value, self::getObjectProperty($object, $key));
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
