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
trait PregUtils
{
    /**
     * Returns keys of *$array* at given *$positions*.
     *
     * Let
     *
     *      $keys = array_keys($array),
     *
     * then the function returns a *$result* array with
     *
     *      $result[$i] = $keys[$i] ?? $i;
     *
     * for each ``$i`` from *$positions*.
     *
     * @param  array $array
     *      An array to take keys from.
     * @param  array $positions
     *      An array of integer offsets.
     * @return array
     */
    public static function pregTupleKeysAt(array $array, array $positions) : array
    {
        $keys = array_keys($array);
        return array_map(function (int $i) use ($keys) {
            return $keys[$i] ?? $i;
        }, $positions);
    }

    /**
     * Transforms array of *$strings* into array *$tuples*, where
     * ``$tuple[$i][0] = $strings[$i]``. If *$key* is given and is not null,
     * then ``$tuple[$i][1] = [$key => [$strings[$i], $offset]]``.
     *
     * @param  array $strings
     * @param  string|null $key
     * @param  int $offset
     * @return array
     */
    public static function stringsToPregTuples(array $strings, string $key = null, int $offset = 0)
    {
        if ($key === null) {
            return array_map(function (string $item) {
                return [$item];
            }, $strings);
        } else {
            return array_map(function (string $item) use ($key, $offset) {
                return [$item, [$key => [$item, $offset]]];
            }, $strings);
        }
    }

    /**
     * Takes an array of capture groups *$captures*, as returned by
     * ``preg_match()``, and transforms them by adding *$offset* to all
     * *$captures[\*][1]*. If *$shiftMain* is ``false``, then *$captures[0]*
     * (whole-pattern capture) is excluded from shifting.
     *
     * @param  array $captures
     * @param  int $offset
     * @param  bool $shiftMain
     *
     * @return array Returns the transformed captures.
     */
    public static function shiftPregCaptures(array $captures, int $offset, bool $shiftMain = true) : array
    {
        foreach ($captures as $key => $capture) {
            if (($key !== 0 || $shiftMain) && is_array($capture)) {
                $captures[$key][1] += $offset;
            }
        }
        return $captures;
    }

    /**
     * Takes an array of capture groups, as returned by ``preg_match()``,
     * and transforms all captures with ``shiftPregCaptures($captures, strlen($prefix), !(bool)$prefixMain)``.
     *
     * If *$prefixMain* is ``true`` aknd capture group ``$captures[0][0]`` is
     * present, the *$captures[0][0]* gets prefixed with *$prefix*. In this
     * case, its offset (``$captures[0][1]``) is preserved.
     *
     * @param  array $captures
     * @param  string $prefix
     * @param  mixed $prefixMain
     *
     * @return array Returns the transformed captures.
     */
    public static function prefixPregCaptures(array $captures, string $prefix, $prefixMain = false) : array
    {
        if ($prefixMain) {
            $prefixMain = is_string($prefixMain) ? $prefixMain : $prefix;
            if (is_array($captures[0] ?? null)) {
                $captures[0][0] = $prefixMain.$captures[0][0];
            } elseif (is_string($captures[0] ?? null)) {
                $captures[0] = $prefixMain.$captures[0];
            }
        }
        return static::shiftPregCaptures($captures, strlen($prefix), !(bool)$prefixMain);
    }

    /**
     * A version of array_merge() for PCRE captures.
     *
     * The function behaves as PHP's ``array_merge($left, $right)`` as long as
     * *$mergeMain* is missing or is ``null``. When *$mergeMain* is not null,
     * the behavior is changed with respect to array elements at offset 0 (the
     * whole-pattern capture). If *$mergeMain* it is ``true``, then the
     * function tries to set *$result[0]* to either *$right[0]* or *$left[0]*
     * (if *$right[0]* is not set). If *$mergeMain* is not null and is not a
     * bool, it's returned at offset 0 of the resultant array.
     *
     * **Examples**:
     *
     *      assert(self::mergePregCaptures(['A', 'x' => 'L'], ['B', 'x' => 'R']) === [0 => 'A', 'x' => 'R', 1 => 'B']);
     *      assert(self::mergePregCaptures(['A', 'x' => 'L'], ['B', 'x' => 'R'], true) === [0 => 'B', 'x' => 'R']);
     *      assert(self::mergePregCaptures(['A', 'x' => 'L'], ['B', 'x' => 'R'], 'M') === [0 => 'M', 'x' => 'R']);
     *
     * @param  array $left
     * @param  array $right
     * @param  mixed $mergeMain
     *
     * @return array
     */
    public static function mergePregCaptures(array $left, array $right, $mergeMain = null) : array
    {
        if ($mergeMain) {
            $main = is_bool($mergeMain) ? ($right[0] ?? $left[0] ?? null) : $mergeMain;
            $main = ($main === null) ? [] : [$main];
            unset($left[0]);
            unset($right[0]);
        } else {
            $main = [];
        }
        return array_merge($main, $left, $right);
    }

    /**
     * Takes a two-element array *$tuple*, prepends *$prefix* to *$tuple[0]*
     * and, if *$tuple[1]* is present, transforms it with
     * ``prefixPregCaptures($tuple[1], $prefix, $prefixMain);``.
     *
     * @param  array $tuple
     * @param  string $prefix
     * @param  mixed $prefixMain
     *
     * @return array Returns two-element array with prefixed *$tuple[0]* at
     *         offset 0 and transformed *tuple[1]* at offset 1.
     */
    public static function prefixPregTuple(array $tuple, string $prefix, $prefixMain = false) : array
    {
        [$_0, $_1] = self::pregTupleKeysAt($tuple, [0, 1]);
        $tuple[$_0] = $prefix.$tuple[$_0];
        if (($captures = $tuple[$_1] ?? null) !== null) {
            $tuple[$_1] = static::prefixPregCaptures($captures, $prefix, $prefixMain);
        }
        return $tuple;
    }

    /**
     * Takes a two-element array *$tuple*, appends *$suffix* to *$tuple[0]*.
     *
     * If *$suffixMain* is ``true`` and capture group ``$tuple[1][0][0]`` is
     * present, the *$tuple[1][0][0]* gets suffixed with *$suffix* as well.
     * If *$suffixMain* is a string and capture group ``$tuple[1][0][0]`` is
     * present, the *$tuple[1][0][0]* gets suffixed with *$suffixMain*.
     *
     * @param  array $tuple
     * @param  string $suffix
     * @param  mixed $suffixMain
     *
     * @return array Returns two-element array with suffixed *$tuple[0]* at
     *         offset 0 and transformed *arguments[1]* at offset 1.
     */
    public static function suffixPregTuple(array $tuple, string $suffix, $suffixMain = false) : array
    {
        [$_0, $_1] = self::pregTupleKeysAt($tuple, [0, 1]);
        $tuple[$_0] = $tuple[$_0].$suffix;
        if (($captures = $tuple[$_1] ?? null) !== null) {
            if ($suffixMain && ($captures[0][0] ?? null) !== null) {
                $captures[0][0] = $captures[0][0].(is_string($suffixMain) ? $suffixMain : $suffix);
            }
            $tuple[$_1] = $captures;
        }
        return $tuple;
    }

    /**
     * Applies multiple transformations to *$tuple*, as specified by *$options*.
     *
     * The function applies following transformations, in order:
     *
     * - if *$options['prefix']* (string) is present and is not null, then:
     *
     *   ```
     *   $tuple = static::prefixPregTuple($tuple, $options['prefix'], $options['prefixMain'] ?? false);
     *   ```
     *
     * - if *$options['merge']* (array) and is not null or *$options['mergeMain']* is present and is not null, then:
     *
     *   ```
     *   $tuple[1] = static::mergePregCaptures($tuple[1] ?? [], $options['merge'], $options['mergeMain'] ?? null);
     *   ```
     *
     * - if *$options['suffix']* (string) is present and is not null, then:
     *
     *   ```
     *   $tuple = static::suffixPregTuple($tuple, $options['suffix'], $options['suffixMain'] ?? false);
     *   ```
     *
     * @param  array $tuple
     * @param  array $options Supported options:
     *
     * - *merge*,
     * - *mergeMain*,
     * - *prefix*,
     * - *prefixMain*,
     * - *suffix*,
     * - *suffixMain*.
     *
     * @return array Returns transformed *$tuple*.
     */
    public static function transformPregTuple(array $tuple, array $options = []) : array
    {
        static $defaults = [
            'prefix' => null,
            'prefixMain' => null,
            'merge' => [],
            'mergeMain' => null,
            'suffix' => null,
            'suffixMain' => false,
        ];
        $options = array_merge($defaults, array_intersect_key($options, $defaults));
        extract($options);

        [$_0, $_1] = self::pregTupleKeysAt($tuple, [0, 1]);
        if ($prefix !== null) {
            $tuple = static::prefixPregTuple($tuple, $prefix, $prefixMain);
        }
        if ($merge || $mergeMain !== null) {
            $tuple[$_1] = static::mergePregCaptures($tuple[$_1] ?? [], $merge ?? [], $mergeMain);
        }
        if ($suffix !== null) {
            $tuple = static::suffixPregTuple($tuple, $suffix, $suffixMain);
        }
        return $tuple;
    }

    /**
     * Joins two PREG tuples, *$left* and *$right* with *$glue*.
     *
     * @param  array $left
     * @param  array $right
     * @param  array $options
     * @return array
     */
    public static function joinTwoPregTuples(array $left, array $right, array $options = [])
    {
        static $defaults = [
            'glue' => '',
            'joinMain' => false
        ];
        $options = array_merge($defaults, array_intersect_key($options, $defaults));
        extract($options);

        $left = array_values($left);    // string keys get lost, sorry
        $right = array_values($right);  // string keys get lost, sorry
        $options = ['suffix' => $glue.$right[0], 'suffixMain' => $joinMain];
        if (($captures = $right[1] ?? null) !== null) {
            $options['merge'] = static::shiftPregCaptures($captures, strlen($left[0].$glue));
        }
        return static::transformPregTuple($left, $options);
    }

    /**
     * Joins multiple PREG *$tuples* with a glue.
     *
     * @param  array $tuples
     *      An array of tuples to be "concatenated".
     * @param  array $options
     *      Supported options:
     *
     * - *glue*,
     * - *joinMain*, and
     * - all options supported by ``transformPregTuple()``.
     *
     * @return array
     */
    public static function joinPregTuples(array $tuples, array $options = [])
    {
        if (empty($tuples)) {
            $message = '$tuples array passed to '.__class__.'::'.__function__.'() can not be empty';
            throw new \InvalidArgumentException($message);
        }

        $left = array_shift($tuples);
        $joint = array_reduce($tuples, function (array $carry, array $tuple) use ($options) {
            return static::joinTwoPregTuples($carry, $tuple, $options);
        }, $left);

        return static::transformPregTuple($joint, $options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
