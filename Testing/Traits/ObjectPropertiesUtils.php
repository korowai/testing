<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Testing\Traits;

/**
 * Example trait for testing purposes.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
trait ObjectPropertiesUtils
{
    /**
     * Returns a key-value array which maps class names onto arrays of property
     * getters. Each array of property getters is a key-value array with keys
     * being property names and values being names of corresponding getter methods.
     *
     * @return array
     */
    abstract public static function objectPropertyGettersMap() : array;

    /**
     * Returns array of property getters intended to be used with objects of
     * given *$class*.
     *
     * @param  mixed $objectOrClass
     *       An object of a fully qualified class name.
     * @return array
     */
    public static function getObjectPropertyGetters($objectOrClass) : array
    {
        $class = is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;

        if (!is_string($class)) {
            throw new \InvalidArgumentException(
                'Argument 1 to '.__class__.'::'.__function__.'() must be of type object or string, '.
                gettype($class).' given.'
            );
        }

        if (!class_exists($class) && !interface_exists($class) && !trait_exists($class)) {
            throw new \InvalidArgumentException(
                'Argument 1 to '.__class__.'::'.__function__.'() must be an object or '.
                'a class, interface, or trait name, "'.$class.'" given.'
            );
        }

        $all = class_implements($class);
        $classes = array_merge(class_parents($class), [$class => $class]);

        foreach ($classes as $key => $val) {
            $all = array_merge($all, class_uses($key), [$key => $val]);
        }

        return array_merge(...array_map(function (string $key) {
            $getters = static::objectPropertyGettersMap();
            return $getters[$key] ?? [];
        }, array_keys($all)));
    }

    /**
     * Returns object's property identified by *$key*.
     *
     * @param  object $object
     * @param  string $key
     * @param  array $getters
     * @return mixed
     */
    public static function getObjectProperty(object $object, string $key, array $getters = null)
    {
        $getters = $getters ?? static::getObjectPropertyGetters($object);
        $getter = substr($key, -2) === '()' ? substr($key, 0, -2) : $getters[$key] ?? null;
        if ($getter !== null) {
            return call_user_func([$object, $getter]);
        } else {
            return $object->{$key};
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
