<?php

use One23\Helpers\Number;

class NumberTest extends \Tests\TestCase
{
    public function test_val(): void
    {
        $this->assertTrue(
            Number::val(true) === 1
        );

        $this->assertTrue(
            Number::val(false) === 0
        );

        $this->assertTrue(
            is_null(Number::val([]))
        );

        $this->assertTrue(
            is_null(Number::val(new \stdClass()))
        );

        $this->assertTrue(
            is_int(Number::val(123))
        );

        $this->assertFalse(
            is_int(Number::val(123.12))
        );

        $this->assertTrue(
            is_float(Number::val(123.12))
        );

        $this->assertEquals(
            123,
            Number::val(123)
        );

        $this->assertEquals(
            null,
            Number::val('abc')
        );

        $this->assertEquals(
            123.123,
            Number::val('0123.123')
        );

        $this->assertEquals(
            123.123,
            Number::val(function() {
                return 123.123;
            })
        );

        $this->assertEquals(
            123.123,
            Number::val(function() {
                return '+0123,123';
            })
        );

        $this->assertEquals(
            -123.123,
            Number::val(function() {
                return '-0123,123';
            })
        );

        $this->assertEquals(
            null,
            Number::val(null)
        );
    }

    public function test_float(): void
    {
        $this->assertTrue(
            is_float(Number::float(123))
        );

        $this->assertTrue(
            is_float(Number::float(123.123))
        );

        $this->assertTrue(
            ! is_float(Number::float('abc'))
        );
    }

    public function test_int(): void
    {
        $this->assertTrue(
            is_int(Number::int(123))
        );

        $this->assertTrue(
            is_int(Number::int(123.123))
        );

        $this->assertTrue(
            ! is_int(Number::int('abc'))
        );
    }

    public function test_last(): void
    {
        $this->assertEquals(
            123.123,
            Number::last(300, 400, null, 500, 123.123)
        );

        $this->assertEquals(
            123.123,
            Number::last('abc', 123.123)
        );

        $this->assertEquals(
            123.123,
            Number::last(123.123, null)
        );

        $this->assertNull(
            Number::last()
        );

        $this->assertNull(
            Number::last(null, 'abc', null)
        );

        $this->assertEquals(
            123.123,
            Number::last(null, 200, '123.123', 'abc')
        );

        $this->assertEquals(
            123.123,
            Number::last(null, 111, function() {
                return '123.123';
            }, null)
        );
    }

    public function test_first(): void
    {
        $this->assertEquals(
            123.123,
            Number::first(123.123)
        );

        $this->assertEquals(
            123.123,
            Number::first('abc', 123.123)
        );

        $this->assertEquals(
            123.123,
            Number::first(123.123, null)
        );

        $this->assertNull(
            Number::first()
        );

        $this->assertNull(
            Number::first(null, 'abc', null)
        );

        $this->assertEquals(
            123.123,
            Number::first(null, '123.123')
        );

        $this->assertEquals(
            123.123,
            Number::first(null, function() {
                return '123.123';
            })
        );
    }

    public function test_money(): void
    {
        $this->assertEquals(
            123,
            Number::money(123.123, 0)
        );

        $this->assertEquals(
            123.1,
            Number::money(123.123, 1)
        );

        $this->assertEquals(
            123.12,
            Number::money(123.123, 2)
        );

        $this->assertEquals(
            123.123,
            Number::money(123.123, 3)
        );

        $this->assertEquals(
            0,
            Number::money(-123.123, 4)
        );

        $this->assertEquals(
            0,
            Number::money(null, 19)
        );
    }

    public function test_get(): void
    {
        $this->assertEquals(
            123.123,
            Number::get(123.123)
        );

        $this->assertEquals(
            123.123,
            Number::get('abc', 123.123)
        );

        $this->assertEquals(
            null,
            Number::get('abc', null)
        );

        $this->assertEquals(
            1,
            Number::get(123, 1, 0, 100)
        );

        $this->assertEquals(
            1,
            Number::get(123, 1, null, 100)
        );

        $this->assertEquals(
            1,
            Number::get(123, 1, 150, null)
        );
    }

    public function test_uniq(): void
    {
        $this->assertEquals(
            [1, 2, 3],
            Number::uniq(...[1, 2, 3, 3, 2, 1])
        );

        $this->assertEquals(
            [1.12, 2.23, 3.33, 3, 2, 1],
            Number::uniq(...['abc', 1.12, 2.23, null, 3.33, 'def', 'ghi', 3, 2, 1])
        );

        $this->assertEquals(
            [],
            Number::uniq(...['abc', null, 'def', 'ghi'])
        );
    }

    public function test_all(): void
    {
        $this->assertEquals(
            [1, 2, 3, -2, 1, 2, -1.23],
            Number::all(...['abc', 1, 2, 3, -2, 1, 2, '-1,23', 'def', 'ghi', 'jkl'])
        );

        $this->assertEquals(
            [1, 2, 3],
            Number::all(...[1, 2, 3])
        );

        $this->assertEquals(
            [1, 2, 3],
            Number::all(...['abc', 1, 2, 3])
        );

        $this->assertEquals(
            [1, 2, 3],
            Number::all(...['abc', 1, 2, 3, 'def'])
        );

        $this->assertEquals(
            [1, 2, 3],
            Number::all(...['abc', 1, 2, 3, 'def', 'ghi'])
        );

        $this->assertEquals(
            [1, 3],
            Number::all(...['abc', 1, null, 3, 'def', 'ghi'])
        );

        $this->assertEquals(
            [],
            Number::all(...['abc', null, 'def', 'ghi', 'jkl'])
        );
    }

    public function test_min(): void
    {
        $this->assertEquals(
            1,
            Number::min(1, 2, 3)
        );

        $this->assertEquals(
            -2.11,
            Number::min(3, '-2.11', null)
        );

        $this->assertEquals(
            -1.1,
            Number::min(2, 1, 3, -1.1)
        );

        $this->assertEquals(
            1.1,
            Number::min(2, 1.1, 3, 'abc')
        );

        $this->assertEquals(
            null,
            Number::min('a', null, 'abc')
        );
    }

    public function test_max(): void
    {
        $this->assertEquals(
            3.33,
            Number::max(1, 2, 3.33)
        );

        $this->assertEquals(
            3.44,
            Number::max(3.44, 2, null)
        );

        $this->assertEquals(
            3.5,
            Number::max(2, 1, 3.5)
        );

        $this->assertEquals(
            3.4,
            Number::max(2, 1, 3.4, 'abc')
        );

        $this->assertEquals(
            null,
            Number::max('a', null, 'abc')
        );
    }
}
