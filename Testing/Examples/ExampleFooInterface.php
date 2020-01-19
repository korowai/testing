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
interface ExampleFooInterface
{
    /**
     * Returns *$foo* attribute.
     *
     * @return mixed
     */
    public function getFoo();
}

// vim: syntax=php sw=4 ts=4 et:
