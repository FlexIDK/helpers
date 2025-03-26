<?php

use One23\Helpers\Number;
use Tests\TestCase;

class NumberTest extends TestCase
{
    public function test_val(): void
    {
        foreach ([
            ['-123.123', -123.123],
            ['-123-234.123', -123],
            ['-123-234', -123],
            [
                '- 1\'12"12`14 234,45 3,44',
                -1121214234.453,
            ],
            ['abc', null],
            [true, 1],
            [false, 0],
            [[], null],
            [new \stdClass, null],
            ['123', 123],
            ['123.', 123],
            ['', null],
            ['123.123', 123.123],
            ['0123.123', 123.123],
            [function() {
                return '-123.123';
            }, -123.123],
            [null, null],
            ['+0123,123', 123.123],
            [',124', 0.124],
            ['.1 2 4', 0.124],
            ['abc 123.23 sfd', 123.23],
            [' 1 234.45 123', 1234.45123],
            [' 1 234.45.123', 1234.45],
            [' 1 234.4 5,1 2 3', 1234.45],
            [' 1"234\'123`123,45 ', 1234123123.45],
            ['abc 123 asd .23 sfd', 123],
            ['abc 1`2"3 asd .23 sfd', 123],
        ] as $item) {
            $this->assertEquals(
                $item[1],
                Number::val($item[0]),
            );
        }

        $this->assertTrue(
            is_int(Number::val(123))
        );

        $this->assertFalse(
            is_int(Number::val(123.12))
        );

        $this->assertTrue(
            is_float(Number::val(123.12))
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

    public function test_round(): void
    {
        $this->assertEquals(
            '-8.374890123789E+34',
            (string)(Number::round(-83748901237890471839274123094817230, 2))
        );

        $this->assertEquals(
            '0',
            (string)(Number::round(-0.000000000000000000000001, 2))
        );

        $this->assertEquals(
            '-0.33',
            (string)(Number::round(-1 / 3, 2))
        );

        $this->assertEquals(
            (string)0.67,
            (string)(Number::round(2 / 3, 2))
        );

        $this->assertEquals(
            '-1.43',
            (string)(Number::round(-50 / 35, 2))
        );

        $this->assertEquals(
            (string)2,
            (string)(Number::round(+2.00 / 1.00001, 2))
        );

        $this->assertEquals(
            (string)1.52,
            (string)(Number::round(100 / 66, 2))
        );

        $this->assertEquals(
            '8.374890123789E+34',
            (string)(Number::round(83748901237890471839274123094817230, 2))
        );
    }

    public function test_money(): void
    {
        $this->assertNull(
            Number::money((-12905 / 242419), 4)
        );

        $this->assertEquals(
            (string)0,
            (string)(Number::money(0, 4) * 100)
        );

        $this->assertEquals(
            (string)5.32,
            (string)(Number::money((12905 / 242419), 4) * 100)
        );

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

        $this->assertEquals(
            null,
            Number::money(123471328974890172389478912378497182937489127340897123894712380947213, 19)
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
            18,
            Number::min(18, 18)
        );

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
            3.3,
            Number::min(3.3, 3.3)
        );

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

    public function test_min_max(): void
    {
        $this->assertEquals(
            [18, 18],
            Number::minMax(18, 18)
        );

        $this->assertEquals(
            [18, 18],
            Number::minMax('a', 18, 'b', null)
        );

        $this->assertEquals(
            [1, 4],
            Number::minMax(1, 2, 4, 3)
        );

        $this->assertEquals(
            null,
            Number::minMax('a', 'b')
        );
    }
}
