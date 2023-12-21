<?php

use One23\Helpers\Datetime;
use PHPUnit\Framework\TestCase;

class DatetimeTest extends TestCase
{
    public function test_val()
    {
        $this->assertEquals(
            '2021-01-01 12:34:56',
            Datetime::val('2021-01-01 12:34:56')->toDateTimeString()
        );

        $this->assertNull(
            Datetime::val('abc')
        );

        $this->assertEquals(
            '2021-01-01 12:34:56',
            Datetime::val('abc', '2021-01-01 12:34:56')->toDateTimeString()
        );

        $this->assertEquals(
            '2021-01-01 12:34:56',
            Datetime::val('2021-01-01 12:34:56', '2021-01-01 12:34:57')->toDateTimeString()
        );
    }

    public function test_to_mutable(): void
    {
        $this->assertTrue(
            Datetime::toMutable(
                Datetime::parse('2021-01-01 12:34:56')
            )[0] instanceof \Carbon\Carbon
        );

        $this->assertTrue(
            Datetime::toMutable(
                null,
                Datetime::parse('2021-01-01 12:34:56')
            )[1] instanceof \Carbon\Carbon
        );

        $this->assertFalse(
            Datetime::toMutable(
                null
            )[0] instanceof \Carbon\Carbon
        );
    }

    public function test_to_immutable(): void
    {
        $this->assertTrue(
            Datetime::toImmutable(
                Datetime::parse('2021-01-01 12:34:56')
            )[0] instanceof \Carbon\CarbonImmutable
        );

        $this->assertTrue(
            Datetime::toImmutable(
                null,
                Datetime::parse('2021-01-01 12:34:56')
            )[1] instanceof \Carbon\CarbonImmutable
        );

        $this->assertFalse(
            Datetime::toImmutable(
                null
            )[0] instanceof \Carbon\CarbonImmutable
        );
    }

    public function test_is_between(): void
    {
        $this->assertFalse(
            Datetime::isBetween(
                null,
                '2021-01-01 12:01:12',
                '2021-02-01 13:23:12'
            )
        );

        $this->assertFalse(
            Datetime::isBetween(
                '2021-01-01 12:01:12',
                null,
                '2020-01-01 12:01:11',
            )
        );

        $this->assertFalse(
            Datetime::isBetween(
                '2021-01-01 12:01:12',
                '2021-02-01 13:23:12',
                '2021-03-01 14:23:12'
            )
        );

        $this->assertFalse(
            Datetime::isBetween(
                '2021-01-01 12:01:12',
                '2021-02-02 13:23:12',
                null
            )
        );

        $this->assertTrue(
            Datetime::isBetween(
                '2021-01-01 12:34:56',
                '2021-01-01 12:34:56',
                '2020-01-01 12:34:56',
            )
        );

        $this->assertFalse(
            Datetime::isBetween(
                '2021-01-01 12:34:56',
                '2021-01-01 12:34:57',
                '2021-01-01 12:34:58',
            )
        );

        $this->assertTrue(
            Datetime::isBetween(
                '2021-01-01 02:23:45',
                '2020-12-01 23:33:44',
                '2021-02-01 00:45:00'
            )
        );

        $this->assertTrue(
            Datetime::isBetween(
                '2021-01-01 02:23:45',
                '2020-12-01 23:33:44',
                null
            )
        );

        $this->assertTrue(
            Datetime::isBetween(
                '2021-01-01 23:59:59',
                null,
                '2021-02-01 00:00:00'
            )
        );

        $this->assertFalse(
            Datetime::isBetween(
                '2021-01-01',
                null,
                null
            )
        );
    }

    public function test_fill_by_hours(): void
    {
        $this->assertEquals(
            [
                '2021-01-01 12:00:00' => 'abc',
                '2021-01-01 13:00:00' => 'abc',
                '2021-01-01 14:00:00' => 'abc',
                '2021-01-01 15:00:00' => 'abc',
            ],
            Datetime::fillByHours(
                Datetime::parse('2021-01-01 12:34:56'),
                Datetime::parse('2021-01-01 15:34:56'),
                'abc'
            )
        );

        $this->assertEquals(
            [
                '2021-01-01 12:00:00' => 'abc',
            ],
            Datetime::fillByHours(
                Datetime::parse('2021-01-01 12:34:56'),
                null,
                'abc'
            )
        );

        $this->assertEquals(
            [
                '2021-01-01 12:00:00' => 'abc',
            ],
            Datetime::fillByHours(
                null,
                Datetime::parse('2021-01-01 12:34:56'),
                'abc'
            )
        );
    }

    public function test_to_date_string(): void
    {
        $this->assertNotEquals(
            '2021-01-01',
            Datetime::toDateString('2021-01-02 12:34:56')
        );

        $this->assertEquals(
            '2021-01-01',
            Datetime::toDateString('2021-01-01 12:34:56')
        );

        $this->assertNull(
            Datetime::toDateString('abcdefg', false)
        );
    }

    public function test_to_datetime_string(): void
    {
        $this->assertEquals(
            '2021-01-01 12:34:56',
            Datetime::toDateTimeString('2021-01-01 12:34:56')
        );

        $this->assertNotEquals(
            '2021-01-01 12:34:55',
            Datetime::toDateTimeString('2021-01-01 12:34:56')
        );

        $time = time();
        $this->assertEquals(
            date('Y-m-d H:i:s', $time),
            Datetime::toDateTimeString('abcdefg', true)
        );

        $this->assertNull(
            Datetime::toDateTimeString('abcdefg', false)
        );
    }

    public function test_parse(): void
    {
        $this->assertEquals(
            '2021-01-01 12:34:56',
            Datetime::parse('2021-01-01 12:34:56')->toDateTimeString()
        );

        $time = time();
        $this->assertEquals(
            date('Y-m-d H:i:s', $time),
            Datetime::parse($time)->toDateTimeString()
        );
    }

    public function test_each(): void
    {
        $from = Datetime::parse('2021-01-01 12:34:56');
        $to = Datetime::parse('2021-01-10 11:12:43');

        $dates = [];
        Datetime::each($from, $to, function($date) use (&$dates) {
            $dates[] = $date->toDateString();
        });

        $this->assertEquals(
            [
                '2021-01-01',
                '2021-01-02',
                '2021-01-03',
                '2021-01-04',
                '2021-01-05',
                '2021-01-06',
                '2021-01-07',
                '2021-01-08',
                '2021-01-09',
            ],
            $dates
        );
    }
}
