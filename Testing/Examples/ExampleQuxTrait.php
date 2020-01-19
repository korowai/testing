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
trait ExampleQuxTrait
{
    public $qux;

    /**
     * Sets value of the *$qux* attribute.
     *
     * @param  mixed $qux
     *      New attribute value.
     * @return object
     *      Returns *$this*.
     */
    public function setQux($qux)
    {
        $this->qux = $qux;
        return $this;
    }

    /**
     * Returns *$qux* attribute.
     * @return mixed
     */
    public function getQux()
    {
        return $this->qux;
    }
}

// vim: syntax=php sw=4 ts=4 et:
