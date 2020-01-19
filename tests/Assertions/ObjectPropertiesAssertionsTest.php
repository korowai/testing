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
use Korowai\Testing\Assertions\ObjectPropertiesAssertions;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ObjectPropertiesAssertionsTest extends TestCase
{
    use ObjectPropertiesAssertions;

    // Required by the trait.
    public static function getObjectPropertyGetters($objectOfClass) : array
    {
        return ['salary' => 'getSalary'];
    }

    public function staticMethodsThatMustAppear()
    {
        return [
            ['assertHasPropertiesSameAs'],
            ['assertNotHasPropertiesSameAs'],
            ['hasPropertiesIdenticalTo'],
        ];
    }

    /**
     * @dataProvider staticMethodsThatMustAppear
     */
    public function test__staticMethodExists(string $name)
    {
        $classAndMethod = [self::class, $name];
        self::assertTrue(method_exists(...$classAndMethod));
        $method = new \ReflectionMethod(...$classAndMethod);
        self::assertTrue($method->isStatic());
    }

    public static function propertiesSameAs__cases()
    {
        $esmith = new class {
            public $name = 'Emily';
            public $last = 'Smith';
            public $age = 20;
            public $husband = null;
            public $family = [];
            private $salary = 98;
            public function getSalary()
            {
                return $this->salary;
            }
            public function getDebit()
            {
                return -$this->salary;
            }
            public function marry($husband)
            {
                $this->husband = $husband;
                $this->family[] = $husband;
            }
        };

        $jsmith = new class {
            public $name = 'John';
            public $last = 'Smith';
            public $age = 21;
            public $wife = null;
            public $family = [];
            private $salary = 123;
            public function getSalary()
            {
                return $this->salary;
            }
            public function getDebit()
            {
                return -$this->salary;
            }
            public function marry($wife)
            {
                $this->wife = $wife;
                $this->family[] = $wife;
            }
        };

        $esmith->marry($jsmith);
        $jsmith->marry($esmith);

        $registry = new class {
            public $persons = [];
            public $families = [];
            public function addFamily(string $key, array $persons)
            {
                $this->families[$key] = $persons;
                $this->persons = array_merge($this->persons, $persons);
            }
        };

        $registry->addFamily('smith', [$esmith, $jsmith]);

        return [
            [
                'expect'  => ['name' => 'John', 'last' => 'Smith', 'age' => 21, 'wife' => $esmith],
                'object'  => $jsmith
            ],
            [
                'expect'  => [
                    'name' => 'John',
                    'last' => 'Smith',
                    'age' => 21,
                    'wife' => $esmith,
                ],
                'object'  => $jsmith
            ],
            [
                'expect'  => ['name' => 'John', 'last' => 'Smith', 'age' => 21],
                'object'  => $jsmith
            ],
            [
                'expect'  => ['name' => 'John', 'last' => 'Smith'],
                'object'  => $jsmith
            ],
            [
                'expect'  => ['age' => 21],
                'object'  => $jsmith
            ],
            [
                'expect'  => ['age' => 21, 'salary' => 123, 'debit' => -123],
                'object'  => $jsmith,
                'getters' => function (object $o) {
                    return ['salary' => 'getSalary', 'debit' => 'getDebit'];
                }
            ],
            [
                'expect'  => ['age' => 21, 'getSalary()' => 123, 'getDebit()' => -123],
                'object'  => $jsmith
            ],
            [
                'expect'  => [
                    'name' => 'John',
                    'last' => 'Smith',
                    'age' => 21,
                    'wife' => self::hasPropertiesIdenticalTo([
                        'name' => 'Emily',
                        'last' => 'Smith',
                        'age' => 20,
                        'husband' => $jsmith,
                        'getSalary()' => 98
                    ])
                ],
                'object'  => $jsmith
            ],
            [
                'expect'  => [
                    'name' => 'John',
                    'last' => 'Smith',
                    'age' => 21,
                    'wife' => self::hasPropertiesIdenticalTo([
                        'name' => 'Emily',
                        'last' => 'Smith',
                        'age' => 20,
                        'husband' => self::hasPropertiesIdenticalTo([
                            'name' => 'John',
                            'last' => 'Smith',
                            'age' => 21,
                            'getSalary()' => 123,
                        ]),
                        'getSalary()' => 98
                    ])
                ],
                'object'  => $jsmith
            ],
            [
                'expect'  => [
                    'age' => 21,
                    'salary' => 123,
                    'debit' => -123,
                    'wife' => self::hasPropertiesIdenticalTo([
                        'name' => 'Emily',
                        'salary' => 98,
                        'debit' => -98,
                    ], function (object $o) {
                        return ['salary' => 'getSalary', 'debit' => 'getDebit'];
                    })
                ],
                'object'  => $jsmith,
                'getters' => function (object $o) {
                    return ['salary' => 'getSalary', 'debit' => 'getDebit'];
                }
            ],
            [
                'expect' => [
                    'family' => [ $esmith ],
                ],
                'object' => $jsmith
            ],
            [
                'expect' => [
                    'family' => [
                        self::hasPropertiesIdenticalTo(['name' => 'Emily', 'last' => 'Smith']),
                    ],
                ],
                'object' => $jsmith
            ],
            [
                'expect' => [
                    'persons' => [
                        self::hasPropertiesIdenticalTo(['name' => 'Emily', 'last' => 'Smith']),
                        self::hasPropertiesIdenticalTo(['name' => 'John', 'last' => 'Smith']),
                    ],
                    'families' => [
                        'smith' => [
                            self::hasPropertiesIdenticalTo(['name' => 'Emily', 'last' => 'Smith']),
                            self::hasPropertiesIdenticalTo(['name' => 'John', 'last' => 'Smith']),
                        ]
                    ]
                ],
                'object' => $registry
            ],
            [
                'expect' => [
                    'persons' => [
                        $esmith,
                        $jsmith,
                    ],
                    'families' => [
                        'smith' => [
                            $esmith,
                            $jsmith,
                        ]
                    ]
                ],
                'object' => $registry
            ],
        ];
    }

    public static function propertiesNotSameAs__cases()
    {
        $hbrown = new class {
            public $name = 'Helen';
            public $last = 'Brown';
            public $age = 44;
        };

        $esmith = new class {
            public $name = 'Emily';
            public $last = 'Smith';
            public $age = 20;
            public $husband = null;
            public $family = [];
            private $salary = 98;
            public function getSalary()
            {
                return $this->salary;
            }
            public function getDebit()
            {
                return -$this->salary;
            }
            public function marry($husband)
            {
                $this->husband = $husband;
                $this->family[] = $husband;
            }
        };

        $jsmith = new class {
            public $name = 'John';
            public $last = 'Smith';
            public $age = 21;
            public $wife = null;
            public $family = [];
            private $salary = 123;
            public function getSalary()
            {
                return $this->salary;
            }
            public function getDebit()
            {
                return -$this->salary;
            }
            public function marry($wife)
            {
                $this->wife = $wife;
                $this->family[] = $wife;
            }
        };

        $esmith->marry($jsmith);
        $jsmith->marry($esmith);

        $registry = new class {
            public $persons = [];
            public $families = [];
            public function addFamily(string $key, array $persons)
            {
                $this->families[$key] = $persons;
                $this->persons = array_merge($this->persons, $persons);
            }
        };

        $registry->addFamily('smith', [$esmith, $jsmith]);

        return [
            [
                'expect' => ['name' => 'John', 'last' => 'Brown', 'age' => 21],
                'object' => $jsmith
            ],
            [
                'expect' => ['name' => 'John', 'last' => 'Smith', 'wife' => null],
                'object' => $jsmith
            ],
            [
                'expect' => ['name' => 'John', 'last' => 'Smith', 'wife' => 'Emily'],
                'object' => $jsmith
            ],
            [
                'expect' => ['name' => 'John', 'last' => 'Smith', 'wife' => $hbrown],
                'object' => $jsmith
            ],
            [
                'expect' => ['name' => 'John', 'last' => 'Brown'],
                'object' => $jsmith
            ],
            [
                'expect' => ['age' => 19],
                'object' => $jsmith
            ],
            [
                'expect' => ['age' => 21, 'salary' => 1230],
                'object' => $jsmith,
                'getters' => function (object $o) {
                    return ['salary' => 'getSalary', 'debit' => 'getDebit'];
                }
            ],
            [
                'expect' => ['age' => 21, 'salary' => 123, 'debit' => -1230],
                'object' => $jsmith,
                'getters' => function (object $o) {
                    return ['salary' => 'getSalary', 'debit' => 'getDebit'];
                }
            ],
            [
                'expect' => ['age' => 21, 'getSalary()' => 1230],
                'object' => $jsmith
            ],
            [
                'expect'  => [
                    'name' => 'John',
                    'last' => 'Smith',
                    'age' => 21,
                    'wife' => [
                        'name' => 'Emily',
                        'last' => 'Smith',
                        'age' => 20,
                        'husband' => [
                            'name' => 'John',
                            'last' => 'Smith',
                            'age' => 21,
                            'getSalary()' => 123,
                        ],
                        'getSalary()' => 98
                    ]
                ],
                'object'  => $jsmith
            ],
            [
                'expect' => [
                    'family' => [
                        ['name' => 'Emily', 'last' => 'Smith'],
                    ],
                ],
                'object' => $jsmith
            ],
            [
                'expect' => [
                    'persons' => [
                        ['name' => 'Emily', 'last' => 'Smith'],
                        ['name' => 'John', 'last' => 'Smith'],
                    ],
                    'families' => [
                        'smith' => [
                            ['name' => 'Emily', 'last' => 'Smith'],
                            ['name' => 'John', 'last' => 'Smith'],
                        ]
                    ]
                ],
                'object' => $registry
            ]
        ];
    }

    /**
     * @dataProvider propertiesSameAs__cases
     */
    public function test__hasPropertiesIdenticalTo__withMatchingProperties(
        array $expect,
        object $object,
        callable $getters = null
    ) {
        self::assertTrue(self::hasPropertiesIdenticalTo($expect, $getters)->matches($object));
    }

    /**
     * @dataProvider propertiesNotSameAs__cases
     */
    public function test__hasPropertiesIdenticalTo__withNonMatchingProperties(
        array $expected,
        object $object,
        callable $getters = null
    ) {
        self::assertFalse(self::hasPropertiesIdenticalTo($expected, $getters)->matches($object));
    }

    public function test__hasPropertiesIdenticalTo__withNonObject()
    {
        $matcher = self::hasPropertiesIdenticalTo(['a' => 'A']);
        self::assertFalse($matcher->matches(123));

        $regexp = '/^123 has required properties with prescribed values$/';
        if (method_exists(self::class, 'assertMatchesRegularExpression')) {
            self::assertMatchesRegularExpression($regexp, $matcher->failureDescription(123));
        } else {
            self::assertRegExp($regexp, $matcher->failureDescription(123));
        }
    }

    public function test__hasPropertiesIdenticalTo__withInvalidArray()
    {
        self::expectException(\PHPUnit\Framework\Exception::class);
        self::expectExceptionMessage('The array of expected properties contains 3 invalid key(s)');

        self::hasPropertiesIdenticalTo(['a' => 'A', 0 => 'B', 2 => 'C', 7 => 'D', 'e' => 'E']);
    }

    public function test__hasPropertiesIdenticalTo__withInvalidGetter()
    {
        $object = new class {
            protected $a;
        };

        self::expectException(\PHPUnit\Framework\Exception::class);
        self::expectExceptionMessage('$object->xxx() is not callable');

        self::hasPropertiesIdenticalTo(['xxx()' => 'A'])->matches($object);
    }

    public function test__hasPropertiesIdenticalTo__withInvalidGetterOption()
    {
        $object = new class {
            protected $a;
        };

        self::expectException(\PHPUnit\Framework\Exception::class);
        self::expectExceptionMessage('$object->xxx() is not callable');

        $getters = function (object $o) {
            return ['a' => 'xxx'];
        };
        self::hasPropertiesIdenticalTo(['a' => 'A'], $getters)->matches($object);
    }

    protected static function adjustCase(array $case, string $message = '')
    {
        $args = func_get_args();
        if (is_callable($case[2] ?? null)) {
            $case[] = $case[2];
            $case[2] = $args[1] ?? '';
        } elseif (($msg = $args[1] ?? null) !== null) {
            $case[2] = $msg;
        }
        return $case;
    }

    /**
     * @dataProvider propertiesSameAs__cases
     */
    public function test__assertHasPropertiesSameAs__withMatchingProperties(
        array $expected,
        object $object,
        callable $getters = null
    ) {
        self::assertHasPropertiesSameAs(...(self::adjustCase(func_get_args())));
    }

    /**
     * @dataProvider propertiesNotSameAs__cases
     */
    public function test__assertHasPropertiesSameAs__withNonMatchingProperties(
        array $expected,
        object $object,
        callable $getters = null
    ) {
        $regexp = '/^Lorem ipsum.\n'.
                    'Failed asserting that object class\@.+ has required properties with prescribed values/';
        self::expectException(ExpectationFailedException::class);
        self::expectExceptionMessageMatches($regexp);

        self::assertHasPropertiesSameAs(...(self::adjustCase(func_get_args(), 'Lorem ipsum.')));
    }

    /**
     * @dataProvider propertiesNotSameAs__cases
     */
    public function test__assertNotHasPropertiesSameAs__withNonMatchingProperties(
        array $expected,
        object $object,
        callable $getters = null
    ) {
        self::assertNotHasPropertiesSameAs(...(self::adjustCase(func_get_args())));
    }

    /**
     * @dataProvider propertiesSameAs__cases
     */
    public function test__assertNotHasPropertiesSameAs__whithMatchingProperties(
        array $expected,
        object $object,
        callable $getters = null
    ) {
        $regexp = '/^Lorem ipsum.\n'.
                    'Failed asserting that object class@.+ does not have required properties with prescribed values/';
        self::expectException(ExpectationFailedException::class);
        self::expectExceptionMessageMatches($regexp);

        self::assertNotHasPropertiesSameAs(...(self::adjustCase(func_get_args(), 'Lorem ipsum.')));
    }
}

// vim: syntax=php sw=4 ts=4 et:
