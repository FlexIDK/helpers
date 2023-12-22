<?php

use One23\Helpers\Arr;
use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase
{

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
                '1.2' => 5,
                '1.3' => 4,
                '1.4' => 6,
            ],
            Arr::dotMerge(
                [
                    0 => 5,
                    '1.2' => 3,
                    '1.3' => 4,
                ],
                [
                    1 => 6,
                    '1.2' => 5,
                    '1.4' => 6,
                ]
            )
        );

    }

    public function test_str(): void
    {
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
            [1, 'a', 'b', 'c', 2, 3, 6, 'g', 7],
            Arr::flat([1, ['a,b,c', '1,2,3'], 'd' => 6, 'e' => '2', 'f' => 'g,7'], ',')
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
            Arr::flat([1, [2, [3]]], ',')
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
