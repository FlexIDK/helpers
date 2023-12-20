<?php

use One23\Helpers\Arr;
use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase
{
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
        $this->assertEquals(
            true,
            Arr::inArray(1, [1, 2, 3])
        );

        $this->assertEquals(
            false,
            Arr::inArray(4, [1, 2, 3])
        );

        $this->assertEquals(
            true,
            Arr::inArray(3, [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ], 'id')
        );

        $this->assertEquals(
            false,
            Arr::inArray(4, [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ], 'id')
        );
    }

    public function test_search()
    {
        $this->assertEquals(
            true,
            Arr::search(1, [1, 2, 3]) === 0
        );

        $this->assertEquals(
            true,
            Arr::search(4, [1, 2, 3]) === false
        );

        $this->assertEquals(
            true,
            Arr::search(3, [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ], 'id') === 2
        );

        $this->assertEquals(
            true,
            Arr::search(4, [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ], 'id') === false
        );
    }
}
