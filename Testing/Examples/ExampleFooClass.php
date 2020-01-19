<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Testing\Examples;

/**
 * Example interface for testing purposes.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ExampleFooClass implements ExampleFooInterface
{
    use ExampleBazTrait;

    private $foo;

    /**
     * Initialized ExampleFooClass instance.
     *
     * @param  array $options Initial values for attributes.
     */
    public function __construct($options = [])
    {
        $this->setFoo($options['foo'] ?? null);
        $this->setBaz($options['baz'] ?? null);
    }

    /**
     * Sets value of the *$foo* attribute.
     *
     * @param  mixed $foo
     *      New attribute value.
     * @return ExampleFooInterface
     *      Returns *$this*.
     */
    public function setFoo($foo) : ExampleFooInterface
    {
        $this->foo = $foo;
        return $this;
    }

    /**
     * {@inheritedoc}
     */
    public function getFoo()
    {
        return $this->foo;
    }
}

// vim: syntax=php sw=4 ts=4 et:
