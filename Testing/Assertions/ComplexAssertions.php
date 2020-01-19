<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Testing\Assertions;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
trait ComplexAssertions
{
    /**
     * Returns object's property identified by *$key*.
     *
     * @param  object $object
     * @param  string $key
     * @param  array $getters
     * @return mixed
     */
    abstract public static function getObjectProperty(object $object, string $key, array $getters = null);

    /**
     * Asserts that two variables have the same type and value.
     * Used on objects, it asserts that two variables reference
     * the same object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    abstract public static function assertSame($expected, $actual, string $message = '') : void;

    /**
     * Passes object's property values to individual assert callbacks.
     *
     * For each key *$key* common to *$asserts* and *$expected* the method invokes
     *
     *      call_user_func($asserts[$key], $expected[$key], $property, $message);
     *
     * where *$property* is retrieved by
     *
     *      $property = static::getObjectProperty($object, $key);
     *
     * @param  array $asserts
     *      An array of key-value pairs with property names as keys and
     *      assertion callbacks as values.
     * @param  array $expected
     *      An array of key-value pairs with property names as keys and
     *      property expected values as values.
     * @param  object $object
     *      The object, whose properties are to be examined.
     * @param  string $message
     *      Optional message.
     */
    public static function assertObjectEachProperty(
        array $asserts,
        array $expected,
        object $object,
        string $message = ''
    ) : void {
        $expected = array_intersect_key($expected, $asserts);
        foreach ($expected as $key => $value) {
            $property = static::getObjectProperty($object, $key);
            call_user_func($asserts[$key], $value, $property, $message);
        }
    }

    /**
     * The method passes items of object's property values to individual assert
     * callbacks. That means, for array valued property, each item is passed
     * for assertion to the callback specific to this property.
     *
     * For each key *$key* common to *$asserts* and *$expected* the method invokes
     *
     *      static::assertArrayEachValue($asserts[$key], $expected[$key], $property, $message);
     *
     * where *$property* is retrieved by
     *
     *      $property = static::getObjectProperty($object, $key);
     *
     * @param  array $asserts
     *      An array of key-value pairs with property names as keys and
     *      assertion callbacks as values.
     * @param  array $expected
     *      An array of key-value pairs with property names as keys and
     *      property expected values as values.
     * @param  object $object
     *      The object, whose properties are to be examined.
     * @param  string $message
     *      Optional message.
     */
    public static function assertObjectEachPropertyArrayValue(
        array $asserts,
        array $expected,
        object $object,
        string $message = ''
    ) : void {
        $asserts = array_map(function ($assert) {
            return function (array $expected, $items, string $message = '') use ($assert) {
                static::assertArrayEachValue($assert, $expected, $items, $message);
            };
        }, $asserts);
        static::assertObjectEachProperty($asserts, $expected, $object, $message);
    }

    /**
     * Examines all values of *$array* against *$expected* using *$assert*
     * callback.
     *
     * The method perform two steps (in order):
     *
     * - asserts that *$array* and *$expected* have exactly same keys,
     * - for each *$key => $value* pair from *$array* invokes
     *
     *          call_user_func($assert, $expected[$key], $value, $message);
     *
     * @param  callable $assert
     *      The assertion callback applied to each of *$array*.
     * @param  array $expected
     *      Expected items.
     * @param  mixed $array
     *      The items to be examined.
     * @param  string $message
     */
    public static function assertArrayEachValue(
        callable $assert,
        array $expected,
        array $array,
        string $message = ''
    ) : void {
        $m = 'Failed asserting that two arrays have identical keys.';
        $expectKeys = array_keys($expected);
        $actualKeys = array_keys($array);
        static::assertSame($expectKeys, $actualKeys, empty($message) ? $m : $message."\n".$m);
        foreach ($array as $key => $value) {
            call_user_func($assert, $expected[$key], $value, $message);
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
