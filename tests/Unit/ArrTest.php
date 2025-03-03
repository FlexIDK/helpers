<?php

use One23\Helpers\Arr;
use Tests\TestCase;

class ArrTest extends TestCase
{
    public function test_first_by_keys()
    {
        $this->assertEquals(
            2,
            Arr::firstByKeys(['a' => null, 'b' => 2], ['a', 'b'], skipEmpty: true)
        );

        $this->assertEquals(
            1,
            Arr::firstByKeys(['a' => 1, 'b' => 2], ['a'])
        );

        $this->assertEquals(
            null,
            Arr::firstByKeys(['a' => null, 'b' => 2], ['a', 'b'], skipEmpty: false)
        );

        $this->assertEquals(
            null,
            Arr::firstByKeys(['a' => null, 'b' => 2], ['c', 'd'])
        );

        $this->assertEquals(
            2,
            Arr::firstByKeys(['a' => null, 'b' => 4, 'c' => ['a' => 1, 'b' => 2, 'c' => 3]], ['c.b', 'd.a', 'a.0', 'b'])
        );
    }

    public function test_random_values()
    {
        $this->assertNull(
            Arr::randomValue([])
        );

        $this->assertEquals(
            [],
            Arr::randomValues([])
        );

        $this->assertCount(
            2,
            Arr::randomValues([1, 2, 3, 4, 5], 2)
        );

        $this->assertCount(
            5,
            Arr::randomValues([1, 2, 3, 4, 5], 10)
        );

        $arr = ['a', 'b', 'c', 'd', 'e'];
        $val = Arr::randomValue($arr);

        $this->assertNotFalse(array_search($val, $arr));
        $this->assertFalse(array_search($val, ['x', 'y', 'z']));
    }

    public function test_sum(): void
    {
        $this->assertEquals(
            [
                'a' => [['a'], ['a']],
                'b' => [['b']],
                'c' => [['d']],
            ],
            Arr::sum(
                [
                    'a' => ['a'],
                    'b' => ['b'],
                ],
                [
                    'a' => ['a'],
                    'c' => ['d'],
                ],
            )
        );

        $this->assertEquals(
            [
                'a' => ['a', 'c'],
                'b' => ['b', 'b'],
                'c' => ['d'],
            ],
            Arr::sum(
                [
                    'a' => 'a',
                    'b' => 'b',
                ],
                [
                    'a' => 'c',
                    'b' => 'b',
                    'c' => 'd',
                ],
            )
        );

        $this->assertEquals(
            [
                'a' => 2,
                'b' => 2,
                'c' => 3,
            ],
            Arr::sum(
                [
                    'a' => 1,
                    'b' => 2,
                ],
                [
                    'a' => 1,
                    'c' => 3,
                ],
            )
        );

        $this->assertEquals(
            [
                'a' => 3,
                'b' => 2,
                'c' => 6,
            ],
            Arr::sum(
                [
                    'a' => 1,
                    'b' => 2,
                ],
                [
                    1, 2, 3, 4,
                ],
                [
                    'a' => 1,
                    'c' => 3,
                ],
                [
                    'a' => 1,
                    'c' => 3,
                ],
            )
        );

        $this->assertEquals(
            [],
            Arr::sum()
        );

        $this->assertEquals(
            [],
            Arr::sum([])
        );
    }

    public function test_substr(): void
    {
        $this->assertEquals(
            '{"a":1,"b":2,"c":3}',
            Arr::substr(['a' => 1, 'b' => 2, 'c' => 3])
        );

        $this->assertEquals(
            '{"a":1,"b":2,"c":3}',
            Arr::substr(['a' => 1, 'b' => 2, 'c' => 3], 32)
        );

        $this->assertEquals(
            '{"a":1,"...',
            Arr::substr(['a' => 1, 'b' => 2, 'c' => 3], 8)
        );
    }

    public function test_key_start_with(): void
    {
        $arr = [
            'abc_1' => 1,
            'abc_2' => 1,
            'abc_3' => 1,
            'xxx_1' => 1,
            'xxx_2' => 1,
            'xxx_3' => 1,
            1 => 1,
        ];

        $this->assertEquals(
            [
                'abc_1' => 1,
                'abc_2' => 1,
                'abc_3' => 1,
            ],
            Arr::keyStartWith(
                $arr,
                'abc_'
            )
        );

        $this->assertEquals(
            [
                'xxx_1' => 1,
                'xxx_2' => 1,
                'xxx_3' => 1,
            ],
            Arr::keyStartWith(
                $arr,
                'xxx_'
            )
        );

        $this->assertEquals(
            [
                'abc_1' => 1,
                'abc_2' => 1,
                'abc_3' => 1,
                'xxx_1' => 1,
                'xxx_2' => 1,
                'xxx_3' => 1,
            ],
            Arr::keyStartWith(
                $arr,
                ['abc_', 'xxx_']
            )
        );

        $this->assertEquals(
            [],
            Arr::keyStartWith(
                $arr,
                ['yyy_']
            )
        );
    }

    public function test_only_type(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            Arr::onlyType([1, 2, 3, 1, 2, 3, '4', '5', [], true], 'integer', ['uniq' => true])
        );

        $this->assertEquals(
            [1, 2, 3],
            Arr::onlyType([1, 2, 3, '4', '5', [], true], 'integer')
        );

        $this->assertEquals(
            ['4', '5'],
            Arr::onlyType([1, 2, 3, '4', '5', [], true], 'string')
        );

        $this->assertEquals(
            [1, 2.2, 3.3, 4, 5],
            Arr::onlyType([1, 2.2, 3.3, '4', '5', [], true], 'numeric')
        );

        $this->assertEquals(
            [1, 2.2, 3.3],
            Arr::onlyType([1, 2.2, 3.3, '4', '5', [], true], 'float')
        );
    }

    public function test_key_map(): void
    {
        $this->assertEquals(
            [1, 2],
            Arr::keyMap([
                1,
                ['id' => 1],
                ['id' => 2],
                ['id' => '3'],
                ['id' => '2'],
                ['id' => '1'],
            ], 'id', ['uniq' => true, 'type' => 'integer'])
        );

        $this->assertEquals(
            [1, 2, 3],
            Arr::keyMap([
                ['id' => 1],
                ['id' => 2],
                ['id' => '3'],
                ['id' => '2'],
                ['id' => '1'],
            ], 'id', ['uniq' => true])
        );

        $this->assertEquals(
            ['a', 'b', 'c', '2', '1'],
            Arr::keyMap([
                ['id' => 'a'],
                ['id' => 'b'],
                ['id' => 'c'],
                ['id' => '2'],
                ['id' => '1'],
            ], 'id', ['uniq' => true, 'type' => 'string'])
        );

        $this->assertEquals(
            ['a', 'b', 'c'],
            Arr::keyMap([
                ['id' => 'a'],
                ['id' => 'b'],
                ['id' => 'c'],
                ['id' => 2],
                ['id' => 1],
            ], 'id', ['uniq' => true, 'type' => 'string'])
        );
    }

    public function test_dot_merge(): void
    {
        $this->assertEquals(
            [
                'a.b' => 1,
                'a.c' => 4,
                'a.d' => 5,
                'b.b' => 3,
                'b.c' => 4,
                'b.d.e' => 5,
            ],
            Arr::dotMerge(
                [
                    'a' => [
                        'b' => 1,
                        'c' => 2,
                    ],
                ],
                [
                    'a' => [
                        'c' => 4,
                        'd' => 5,
                    ],
                    'b' => [
                        'b' => 3,
                        'c' => 4,
                        'd' => [
                            'e' => 5,
                        ],
                    ],
                ]
            )
        );

        $this->assertEquals(
            [
                'a.b' => 1,
                'b.b.c' => 4,
                'b.b.d' => 5,
                'b.b.e' => 6,
            ],
            Arr::dotMerge(
                [
                    'a.b.c' => 1,
                    'a.b.d' => 2,
                    'a.b.e' => 3,
                    'b.b.c' => 3,
                    'b.b.d' => 2,
                    'b.b.e' => 1,
                ],
                [
                    'a.b' => 1,
                    'b.b.c' => 4,
                    'b.b.d' => 5,
                    'b.b.e' => 6,
                ]
            )
        );

        $this->assertEquals(
            [
                'a.b.c' => 1,
                'a.b.d' => 3,
                'a.b.e' => 3,
                'b.b.c' => 4,
                'b.b.d' => 5,
                'b.b.e' => 6,

            ],
            Arr::dotMerge(
                [
                    'a.b.c' => 1,
                    'a.b.d' => 2,
                    'a.b.e' => 3,
                ],
                [
                    'a.b.d' => 3,
                    'b.b.c' => 4,
                    'b.b.d' => 5,
                    'b.b.e' => 6,
                ]
            )
        );
    }

    public function test_dot()
    {
        $this->assertEquals(
            [
                '0.0' => 1,
                '0.1' => 2,
                '0.2.3' => 4,
                '1' => 2,
            ],
            Arr::dot([
                0 => [
                    0 => 1,
                    1 => 2,
                    2 => [
                        3 => 4,
                    ],
                ],
                1 => 2,
            ])
        );

        $this->assertEquals(
            [
                '1' => 2,
                '0.0' => 1,
                '0.1' => 2,
                '0.2.3' => 4,
            ],
            Arr::dot([
                1 => 2,
                0 => [
                    0 => 1,
                    1 => 2,
                    2 => [
                        3 => 4,
                    ],
                ],
            ])
        );

        $this->assertEquals(
            [
                0 => [
                    0 => 1,
                    1 => 2,
                    2 => [
                        3 => 4,
                    ],
                ],
                1 => 2,
            ],
            Arr::undot([
                '0.0' => 1,
                '0.1' => 2,
                '0.2.3' => 4,
                '1' => 2,
            ]),
        );

        $this->assertEquals(
            [
                '0' => 5,
                '1' => 6,
                '2' => 6,
            ],
            Arr::dotMerge(
                [
                    0 => 5,
                    '1.2' => 3,
                    '1.3' => 4,
                    2 => 6,
                ],
                [
                    1 => 6,
                ]
            )
        );
    }

    public function test_str(): void
    {
        $this->assertEquals(
            ['a', 'b', 'c'],
            Arr::str('a,b,c,1,2,3')
        );

        $this->assertEquals(
            ['a', 'b', 'c'],
            Arr::str(['a', ['b', ['c']]])
        );

        $this->assertEquals(
            ['a', 'b', 'c'],
            Arr::str('a,b,c')
        );

        $this->assertEquals(
            ['a', 'b', 'c', 'e'],
            Arr::str(['a,b,c', 1, 2, '3,e,4', '5'])
        );
    }

    public function test_ids(): void
    {
        $this->assertEquals(
            [0],
            Arr::ids('0', 0)
        );

        $this->assertEquals(
            [0],
            Arr::ids(0, 0)
        );

        $this->assertEquals(
            [],
            Arr::ids(0, 1)
        );

        $this->assertEquals(
            [3, 2, 1, 4, 5],
            Arr::ids([3, 2, 1, 2, 3, 1, 4, 5, 5], null, 5)
        );

        $this->assertEquals(
            [1, 2, 3, 4, 5],
            Arr::ids([1, 2, 3, 1, 2, 3, 4, 5, 6, 7], null, 5)
        );

        // test ids
        $this->assertEquals(
            [1, 2, 3],
            Arr::ids([1, [2, ['a' => 3]]])
        );

        $this->assertEquals(
            [],
            Arr::ids(['a', 'b', 'c,d,e'])
        );

        $this->assertEquals(
            [1, 2, 3],
            Arr::ids([1, 2, 3, 'a', 'b', 'c'])
        );

        $this->assertEquals(
            [1, 2, 3],
            Arr::ids([1, 2, 3, 4.3, 5.5, 6.6, 'a', 'b', 'c'])
        );

        $this->assertEquals(
            [1, 2, 3],
            Arr::ids([1, 2, 3, 'a', 'b', 'c', '1', '2', '3'])
        );

        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            Arr::ids([1, 2, 3, 'a', 'b', 'c', '1', '2', '3', 'd' => '4', 'e' => '5', 'f' => '6'])
        );

        // test min
        $this->assertEquals(
            [2, 3, 4, 5, 6],
            Arr::ids([1, 2, 3, 'a', 'b', 'c', '1', '2', '3', 'd' => '4', 'e' => '5', 'f' => '6'], 2)
        );

        // test max
        $this->assertEquals(
            [1, 2, 3, 4, 5],
            Arr::ids([1, 2, 3, 'a', 'b', 'c', '1', '2', '3', 'd' => '4', 'e' => '5', 'f' => '6'], null, 5)
        );
    }

    public function test_flat(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            Arr::flat('1,2,3,a,b,c', ['type' => 'integer'])
        );

        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            Arr::flat([1, 2, 3, '4', '5', '6', 2, 1, 3], ['type' => 'integer'])
        );

        $this->assertEquals(
            ['a', 'b', 'c'],
            Arr::flat('1,2,3,a,b,c', ['type' => 'string'])
        );

        $this->assertEquals(
            [1, 'a', 'b', 'c', 2, 3, 6, 'g', 7],
            Arr::flat([1, ['a,b,c', '1,2,3'], 'd' => 6, 'e' => '2', 'f' => 'g,7'])
        );

        $this->assertEquals(
            [1, 2, 3],
            Arr::flat([1, 2, 3])
        );

        $this->assertEquals(
            [1, 2, 3],
            Arr::flat([1, [2], 3])
        );

        $this->assertEquals(
            [1, 2, 3],
            Arr::flat([1, [2, [3]]])
        );

        $this->assertEquals(
            [1, 2, 3],
            Arr::flat([1, [2, [3]]])
        );
    }

    public function test_filter_null(): void
    {
        $this->assertEquals(
            [0 => 1, 2 => 2, 4 => 3],
            Arr::filterNull([1, null, 2, null, 3])
        );

        $this->assertEquals(
            ['a' => 1, 'c' => 2, 'e' => 3],
            Arr::filterNull(['a' => 1, 'b' => null, 'c' => 2, 'd' => null, 'e' => 3])
        );
    }

    public function test_in_array()
    {
        $this->assertTrue(
            Arr::inArray(1, [1, 2, 3])
        );

        $this->assertFalse(
            Arr::inArray(4, [1, 2, 3])
        );

        $this->assertTrue(
            Arr::inArray(3, [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ], 'id')
        );

        $this->assertFalse(
            Arr::inArray(4, [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ], 'id')
        );
    }

    public function test_search()
    {
        $this->assertTrue(
            Arr::search(1, [1, 2, 3]) === 0
        );

        $this->assertTrue(
            Arr::search(4, [1, 2, 3]) === false
        );

        $this->assertTrue(
            Arr::search(3, [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ], 'id') === 2
        );

        $this->assertTrue(
            Arr::search(4, [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ], 'id') === false
        );
    }
}
