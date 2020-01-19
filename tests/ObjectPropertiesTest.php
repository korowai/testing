<?php

/*
 * This file is part of Korowai framework.
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Tests\Testing;

use Korowai\Testing\TestCase;
use Korowai\Testing\ObjectProperties;
use Korowai\Testing\ObjectPropertiesInterface;

//use Korowai\Testing\Assertions\ClassAssertions;
//use Korowai\Testing\Assertions\ObjectPropertiesAssertions;
//use Korowai\Testing\Assertions\PregAssertions;
//use Korowai\Testing\Assertions\ComplexAssertions;
//use Korowai\Testing\Traits\ObjectPropertiesUtils;
//use Korowai\Testing\Traits\PregUtils;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ObjectPropertiesTest extends TestCase
{
    public function test__extends__ArrayObject()
    {
        $this->assertExtendsClass(\ArrayObject::class, ObjectProperties::class);
    }

    public function test__implements__ObjectPropertiesInterface()
    {
        $this->assertImplementsInterface(ObjectPropertiesInterface::class, ObjectProperties::class);
    }

    public function test__construct()
    {
        $properties = ['foo' => 'FOO', 'bar' => 'BAR'];
        $op = new ObjectProperties($properties);
        $this->assertSame($properties, (array)$op);
    }

    public static function getArrayForComparison__cases()
    {
        return [
            [
                'properties' => [],
                'expect'     => [],
            ],
            [
                'properties' => ['foo' => 'FOO'],
                'expect'     => ['foo' => 'FOO'],
            ],
            [
                'properties' => [
                    'foo' => new ObjectProperties([
                        'bar' => 'BAR'
                    ]),
                    'baz' => [
                        'QUX',
                        new ObjectProperties([
                            'cor' => 'COR'
                        ]),
                    ]
                ],
                'expect'     => [
                    'foo' => [
                        'bar' => 'BAR'
                    ],
                    'baz' => [
                        'QUX',
                        [
                            'cor' => 'COR'
                        ]
                    ]
                ],
            ],
            [
                'properties' => [
                    'foo' => [
                        [
                            new ObjectProperties([
                                'bar' => 'BAR',
                                'gez' => [
                                    new ObjectProperties([
                                        'qux' => 'QUX'
                                    ])
                                ]
                            ])
                        ]
                    ]
                ],
                'expect' => [
                    'foo' => [
                        [
                            [
                                'bar' => 'BAR',
                                'gez' => [
                                    [
                                        'qux' => 'QUX'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        ];
    }

    /**
     * @dataProvider getArrayForComparison__cases
     */
    public function test__getArrayForComparison(array $properties, array $expect)
    {
        $op = new ObjectProperties($properties);
        $this->assertSame($expect, $op->getArrayForComparison());
    }
}
// vim: syntax=php sw=4 ts=4 et:
