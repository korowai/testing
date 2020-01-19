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

use Korowai\Testing\Constraint\HasPregCaptures;
use PHPUnit\Framework\Constraint\LogicalNot;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
trait PregAssertions
{
    /**
     * Evaluates a \PHPUnit\Framework\Constraint matcher object.
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    abstract public static function assertThat($value, Constraint $constraint, string $message = '') : void;

    /**
     * Asserts that an array of *$matches* returned from ``preg_match()`` has
     * capture groups as specified in *$expected*.
     *
     * Checks only entries present in *$expected*, so *$expected = []* accepts
     * any array. Special values may be used in the expectations:
     *
     * - ``['foo' => false]`` asserts that group ``'foo'`` was not captured,
     * - ``['foo' => true]`` asserts that group ``'foo'`` was captured,
     * - ``['foo' => 'FOO']`` asserts that group ``'foo'`` was captured and it's value equals ``'FOO'``.
     *
     * Boolean expectations (``['foo' => true]`` or ``['foo' => false]``) work
     * properly only with arrays obtained from ``preg_match()`` invoked with
     * ``PREG_UNMATCHED_AS_NULL`` flag.
     *
     * @param  array $expected
     *      An array of expectations.
     * @param  array $matches
     *      An array of preg matches to be examined.
     * @param  string $message
     *      Additional message.
     *
     * @throws ExpectationFailedException
     */
    public static function assertHasPregCaptures(array $expected, array $matches, string $message = '') : void
    {
        static::assertThat($matches, static::hasPregCaptures($expected), $message);
    }

    /**
     * Negated assertHasPregCaptures().
     *
     * @param  array $expected
     *      An array of expectations.
     * @param  array $matches
     *      An array of preg matches to be examined.
     * @param  string $message
     *      Additional message.
     *
     * @throws ExpectationFailedException
     * @throws \PHPUnit\Framework\Exception when a non-string keys are found in *$expected*
     */
    public static function assertNotHasPregCaptures(array $expected, array $matches, string $message = '') : void
    {
        static::assertThat($matches, new LogicalNot(static::hasPregCaptures($expected)), $message);
    }

    /**
     * Accepts arrays of matches returned from ``preg_match()`` having capture
     * groups as specified in *$excpected*.
     *
     * Checks only entries present in *$expected*, so *$expected = []* accepts
     * any array. Special values may be used in the expectations:
     *
     * - ``['foo' => false]`` asserts that group ``'foo'`` was not captured,
     * - ``['foo' => true]`` asserts that group ``'foo'`` was captured,
     * - ``['foo' => 'FOO']`` asserts that group ``'foo'`` was captured and its value equals ``'FOO'``.
     *
     * Boolean expectations (``['foo' => true]`` or ``['foo' => false]``) work
     * properly only with arrays obtained from ``preg_match()`` invoked with
     * ``PREG_UNMATCHED_AS_NULL`` flag.
     *
     * @param  array $expected
     *
     * @return HasPregCaptures
     */
    public static function hasPregCaptures(array $expected) : HasPregCaptures
    {
        return new HasPregCaptures($expected);
    }
}

// vim: syntax=php sw=4 ts=4 et:
