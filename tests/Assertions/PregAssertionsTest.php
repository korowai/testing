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
use Korowai\Testing\Assertions\PregAssertions;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class PregAssertionsTest extends TestCase
{
    use PregAssertions;

    public function hasPregCaptures__success__cases()
    {
        return [
            [[],                                                            []],
            [[0 => false],                                                  []],
            [[0 => false],                                                  [0 => null]],
            [[0 => false],                                                  [0 => [null, -1]]],
            [[0 => false, 'foo' => false, 'bar' => false, 'gez' => false],  []],

            [[],                                                            [0 => 'FOO']],
            [[0 => 'FOO'],                                                  [0 => 'FOO']],
            [[0 => true],                                                   [0 => 'FOO']],
            [[0 => true, 'foo' => false],                                   [0 => 'FOO']],
            [[0 => true, 'bar' => false],                                   [0 => 'FOO']],
            [[0 => true, 'gez' => false],                                   [0 => 'FOO']],
            [[0 => true, 'foo' => false, 'bar' => false],                   [0 => 'FOO']],
            [[0 => true, 'foo' => false, 'gez' => false],                   [0 => 'FOO']],
            [[0 => true, 'foo' => false, 'bar' => false, 'gez' => false],   [0 => 'FOO']],

            [[],                                                            [0 => 'FOO BAR', 'bar' => 'BAR']],
            [[0 => true],                                                   [0 => 'FOO BAR', 'bar' => 'BAR']],
            [[0 => true, 'foo' => false],                                   [0 => 'FOO BAR', 'bar' => 'BAR']],
            [[0 => true, 'bar' => true],                                    [0 => 'FOO BAR', 'bar' => 'BAR']],
            [[0 => true, 'gez' => false],                                   [0 => 'FOO BAR', 'bar' => 'BAR']],
            [[0 => true, 'foo' => false, 'bar' => true],                    [0 => 'FOO BAR', 'bar' => 'BAR']],
            [[0 => true, 'foo' => false, 'gez' => false],                   [0 => 'FOO BAR', 'bar' => 'BAR']],
            [[0 => true, 'foo' => false, 'bar' => true, 'gez' => false],    [0 => 'FOO BAR', 'bar' => 'BAR']],
            [[0 => 'FOO BAR'],                                              [0 => 'FOO BAR', 'bar' => 'BAR']],
            [['bar' => 'BAR'],                                              [0 => 'FOO BAR', 'bar' => 'BAR']],
            [[0 => 'FOO BAR', 'bar' => 'BAR'],                              [0 => 'FOO BAR', 'bar' => 'BAR']],
            [[0 => 'FOO BAR', 'bar' => 'BAR', 'gez' => false],              [0 => 'FOO BAR', 'bar' => 'BAR']],
            [[0 => 'FOO BAR', 'bar' => 'BAR', 'gez' => false],              [0 => 'FOO BAR', 'bar' => 'BAR', 'gez' => null]],

            //
            // PREG_OFFSET_CAPTURE
            //

            [[],                                                            [0 => 'FOO BAR', 'bar' => ['BAR', 4]]],
            [[0 => true],                                                   [0 => 'FOO BAR', 'bar' => ['BAR', 4]]],
            [[0 => true, 'foo' => false],                                   [0 => 'FOO BAR', 'bar' => ['BAR', 4]]],
            [[0 => true, 'bar' => true],                                    [0 => 'FOO BAR', 'bar' => ['BAR', 4]]],
            [[0 => true, 'gez' => false],                                   [0 => 'FOO BAR', 'bar' => ['BAR', 4]]],
            [[0 => true, 'foo' => false, 'bar' => true],                    [0 => 'FOO BAR', 'bar' => ['BAR', 4]]],
            [[0 => true, 'foo' => false, 'gez' => false],                   [0 => 'FOO BAR', 'bar' => ['BAR', 4]]],
            [[0 => true, 'foo' => false, 'bar' => true, 'gez' => false],    [0 => 'FOO BAR', 'bar' => ['BAR', 4]]],
            [[0 => 'FOO BAR'],                                              [0 => 'FOO BAR', 'bar' => ['BAR', 4]]],
            [['bar' => ['BAR', 4]],                                         [0 => 'FOO BAR', 'bar' => ['BAR', 4]]],
            [['bar' => true],                                               [0 => 'FOO BAR', 'bar' => ['BAR', 4]]],
            [[0 => 'FOO BAR', 'bar' => ['BAR', 4]],                         [0 => 'FOO BAR', 'bar' => ['BAR', 4]]],
            [[0 => true, 'bar' => true],                                    [0 => 'FOO BAR', 'bar' => ['BAR', 4]]],
            [[0 => 'FOO BAR', 'bar' => ['BAR', 4], 'gez' => false],         [0 => 'FOO BAR', 'bar' => ['BAR', 4], 'gez' => [null,-1]]],

            // other corner cases
            [['foo' => null],                                               ['foo' => null]],
            [['foo' => [null,-1]],                                          ['foo' => [null,-1]]],
        ];
    }

    /**
     * @dataProvider hasPregCaptures__success__cases
     */
    public function test__hasPregCaptures__success(array $expected, $other)
    {
        $constraint = $this->hasPregCaptures($expected);
        $this->assertTrue($constraint->matches($other));
    }

    /**
     * @dataProvider hasPregCaptures__success__cases
     */
    public function test__assertHasPregCaptures__success(array $expected, $other)
    {
        $this->assertHasPregCaptures($expected, $other);
    }

    /**
     * @dataProvider hasPregCaptures__success__cases
     */
    public function test__assertNotHasPregCapture__failing(array $expected, $other)
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageMatches('/^Failed asserting that array does not have expected PCRE capture groups.$/sD');
        $this->assertNotHasPregCaptures($expected, $other);
    }

    public function hasPregCaptures__failing__cases()
    {
        $re = 'array has expected PCRE capture groups';
        return [
            [['foo' => true],                       [],                     $re],
            [['foo' => true],                       ['foo' => [null,-1]],   $re],

            [['foo' => 'FOO'],                      [],                     $re],
            [['foo' => 'FOO'],                      ['bar' => 'FOO'],       $re],
            [['foo' => 'FOO'],                      ['foo' => [null,-1]],   $re],
            [['foo' => 'FOO'],                      ['foo' => ['FOO',-1]],  $re],

            [['foo' => false],                      ['foo' => 'FOO'],       $re],
            [['foo' => 'BAR'],                      ['foo' => 'FOO'],       $re],
            [['foo' => 'BAR'],                      ['foo' => ['FOO',-1]],  $re],

            // other corner cases
            [['foo' => null],                       [],                     $re],
            [['foo' => [null,-1]],                  ['foo' => null],        $re],
            [['foo' => [null,-1]],                  [],                     $re],
        ];
    }

    public function hasPregCaptures__nonArray__cases()
    {
        $re = 'string has expected PCRE capture groups';
        return [
            [['foo' => false],  'stuff', $re],
        ];
    }

    /**
     * @dataProvider hasPregCaptures__failing__cases
     * @dataProvider hasPregCaptures__nonArray__cases
     */
    public function test__hasPregCaptures__failing(array $expected, $other, string $regexp)
    {
        $constraint = $this->hasPregCaptures($expected);
        $this->assertFalse($constraint->matches($other));

        if (method_exists($this, 'assertMatchesRegularExpression')) {
            // phpunit >= 9.1
            $this->assertMatchesRegularExpression('/^'.$regexp.'$/sD', $constraint->failureDescription($other));
        } else {
            // phpunit < 9.1
            $this->assertRegExp('/^'.$regexp.'$/sD', $constraint->failureDescription($other));
        }
    }

    /**
     * @dataProvider hasPregCaptures__failing__cases
     */
    public function test__assertHasPregCaptures__failing(array $expected, $other, string $regexp)
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageMatches('/^Failed asserting that '.$regexp.'.$/sD');
        $this->assertHasPregCaptures($expected, $other);
    }

    /**
     * @dataProvider hasPregCaptures__failing__cases
     */
    public function test__assertNotHasPregCapture__success(array $expected, $other, string $regexp)
    {
        $this->assertNotHasPregCaptures($expected, $other);
    }
}

// vim: syntax=php sw=4 ts=4 et:
