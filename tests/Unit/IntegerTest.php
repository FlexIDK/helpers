<?php

use One23\Helpers\Integer;
use Tests\TestCase;

class IntegerTest extends TestCase
{
    public function test_val(): void
    {
        $this->assertEquals(
            123,
            Integer::val(123)
        );

        $this->assertEquals(
            null,
            Integer::val('abc')
        );

        $this->assertEquals(
            123,
            Integer::val('0123')
        );

        $this->assertEquals(
            123,
            Integer::val(function() {
                return 123;
            })
        );

        $this->assertEquals(
            123,
            Integer::val((float)123.456)
        );
    }

    public function test_last(): void
    {
        $this->assertEquals(
            123,
            Integer::last(123)
        );

        $this->assertEquals(
            123,
            Integer::last('abc', 123)
        );

        $this->assertEquals(
            256,
            Integer::last(123, null, 256, null)
        );

        $this->assertEquals(
            123,
            Integer::last(null, 123, 'abc', null)
        );

        $this->assertEquals(
            null,
            Integer::last('abc', 'cde', null)
        );

        $this->assertNull(
            Integer::last(null, 'abc', 'null', null)
        );

        $this->assertNull(
            Integer::last()
        );
    }

    public function test_first(): void
    {
        $this->assertEquals(
            123,
            Integer::first(123)
        );

        $this->assertEquals(
            123,
            Integer::first('abc', 123)
        );

        $this->assertEquals(
            123,
            Integer::first(123, null)
        );

        $this->assertEquals(
            123,
            Integer::first(null, 123, null)
        );

        $this->assertEquals(
            null,
            Integer::first('abc', 'cde', null)
        );

        $this->assertNull(
            Integer::first(null, 'abc', 'null', null)
        );

        $this->assertNull(
            Integer::first()
        );
    }

    public function test_get(): void
    {
        $this->assertEquals(
            123,
            Integer::get(123)
        );

        $this->assertEquals(
            123,
            Integer::get('abc', 123)
        );

        $this->assertEquals(
            null,
            Integer::get('abc', null)
        );

        $this->assertEquals(
            1,
            Integer::get(123, 1, 0, 100)
        );

        $this->assertEquals(
            123,
            Integer::get(123, 1, 0, null)
        );

        $this->assertEquals(
            1,
            Integer::get(-123, 1, 0, 100)
        );

        $this->assertEquals(
            50,
            Integer::get(50, 1, 0, 100)
        );

        $this->assertEquals(
            null,
            Integer::get(150, null, null, 100)
        );
    }

    public function test_get_or_null(): void
    {
        $this->assertEquals(
            123,
            Integer::getOrNull(123)
        );

        $this->assertTrue(
            Integer::getOrNull('abc') === null
        );

        $this->assertTrue(
            Integer::getOrNull(0) === 0
        );

        $this->assertEquals(
            -1,
            Integer::getOrNull(-1)
        );
    }

    public function test_get_or_zero(): void
    {
        $this->assertEquals(
            123,
            Integer::getOrZero(123)
        );

        $this->assertTrue(
            Integer::getOrZero('abc') === 0
        );

        $this->assertTrue(
            Integer::getOrZero(null) === 0
        );

        $this->assertEquals(
            -1,
            Integer::getOrZero(-1)
        );

        $this->assertTrue(
            Integer::getOrZero(0) === 0
        );
    }

    public function test_all(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            Integer::all(...[1, 2, 3])
        );

        $this->assertEquals(
            [1, 2, 3],
            Integer::all(...['abc', 1, 2, 3])
        );

        $this->assertEquals(
            [1, 2, 3],
            Integer::all(...['abc', 1, 2.12, 3, 'def'])
        );

        $this->assertEquals(
            [1, 2, 3],
            Integer::all(...['abc', 1.12, 2.23, null, 3.33, 'def', 'ghi'])
        );
    }

    public function test_uniq(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            Integer::uniq(...[1, 2, 3, 3, 2, 1])
        );

        $this->assertEquals(
            [1, 2, 3],
            Integer::uniq(...['abc', 1.12, 2.23, null, 3.33, 'def', 'ghi', 3, 2, 1])
        );

        $this->assertEquals(
            [],
            Integer::uniq(...['abc', null, 'def', 'ghi'])
        );
    }

    public function test_min()
    {
        $this->assertEquals(
            1,
            Integer::min(...[1, 2, 3, 3, 2, 1])
        );

        $this->assertEquals(
            1,
            Integer::min(...['abc', 1.12, 2.23, null, 3.33, 'def', 'ghi', 3, 2, 1])
        );

        $this->assertEquals(
            null,
            Integer::min(...['abc', null, 'def', 'ghi'])
        );

        $this->assertEquals(
            null,
            Integer::min()
        );

        $this->assertEquals(
            1,
            Integer::min(...[2, 3, 4, 1])
        );
    }

    public function test_max()
    {
        $this->assertEquals(
            3,
            Integer::max(...[1, 2, 3, 3, 2, 1])
        );

        $this->assertEquals(
            3,
            Integer::max(...['abc', 1.12, 2.23, null, 3.33, 'def', 'ghi', 3, 2, 1])
        );

        $this->assertEquals(
            null,
            Integer::max(...['abc', null, 'def', 'ghi'])
        );

        $this->assertEquals(
            null,
            Integer::max()
        );

        $this->assertEquals(
            4,
            Integer::max(...[2, 3, 4, 1])
        );
    }

    public function test_minMax()
    {
        $this->assertEquals(
            [3, 10],
            Integer::minMax(...[4, 6, 3, 3, 10, 8, 7, 9])
        );
    }
}
