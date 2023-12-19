<?php

use One23\Helpers\Datetime;
use PHPUnit\Framework\TestCase;

class DatetimeTest extends TestCase
{
    public function test_to_date_string(): void
    {
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
