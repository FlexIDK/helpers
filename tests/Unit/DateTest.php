<?php

use Carbon\CarbonInterface;
use One23\Helpers\Date;
use Tests\TestCase;

class DateTest extends TestCase
{
    public function test_to_date_string(): void
    {
        $this->assertNotEquals(
            '2021-01-01',
            Date::toDateString('2021-01-02 12:34:56')
        );

        $this->assertEquals(
            '2021-01-01',
            Date::toDateString('2021-01-01 12:34:56')
        );

        $this->assertNull(
            Date::toDateString('abcdefg', null)
        );

        $this->assertEquals(
            Date::now()->toDateString(),
            Date::toDateString('abcdefg', 'now')
        );
    }

    public function test_is_between(): void
    {
        $this->assertFalse(
            Date::isBetween(
                null,
                '2021-01-01 12:01:12',
                '2021-02-01 13:23:12'
            )
        );

        $this->assertFalse(
            Date::isBetween(
                '2021-01-01 12:01:12',
                null,
                '2020-12-01 11:01:12',
            )
        );

        $this->assertFalse(
            Date::isBetween(
                '2021-01-01 12:01:12',
                '2021-02-01 13:23:12',
                '2021-03-01 14:23:12'
            )
        );

        $this->assertFalse(
            Date::isBetween(
                '2021-01-01',
                '2021-02-02',
                null
            )
        );

        $this->assertTrue(
            Date::isBetween(
                '2021-01-01',
                '2021-02-01',
                '2020-12-01',
            )
        );

        $this->assertTrue(
            Date::isBetween(
                '2021-01-01',
                '2020-12-01',
                '2021-02-01'
            )
        );

        $this->assertTrue(
            Date::isBetween(
                '2021-01-01',
                '2020-12-01',
                null
            )
        );

        $this->assertTrue(
            Date::isBetween(
                '2021-01-01',
                null,
                '2021-02-01'
            )
        );

        $this->assertFalse(
            Date::isBetween(
                '2021-01-01',
                null,
                null
            )
        );
    }

    public function test_parse(): void
    {
        $this->assertEquals(
            '2021-01-01 00:00:00',
            Date::parse('2021-01-01 12:34:56')->toDateTimeString()
        );

        $this->assertEquals(
            date('Y-m-d 00:00:00'),
            Date::parse(time())->toDateTimeString()
        );
    }

    public function test_each(): void
    {
        $from = Date::parse('2021-01-01');
        $to = Date::parse('2021-01-10');

        $dates = [];
        Date::each($from, $to, function($date) use (&$dates) {
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
                '2021-01-10',
            ],
            $dates
        );
    }

    public function test_fill(): void
    {
        $from = Date::parse('2021-01-01');
        $to = Date::parse('2021-01-04');

        $res = Date::fill(
            $from, $to,
            function(CarbonInterface $date) {
                return $date->toDateString();
            },
            function(CarbonInterface $date) {
                return [
                    'date' => $date->toDateString(),
                ];
            }
        );

        $this->assertEquals(
            [
                '2021-01-01' => ['date' => '2021-01-01'],
                '2021-01-02' => ['date' => '2021-01-02'],
                '2021-01-03' => ['date' => '2021-01-03'],
                '2021-01-04' => ['date' => '2021-01-04'],
            ],
            $res
        );
    }

    public function test_fill_by_days(): void
    {
        $from = Date::parse('2021-01-01');
        $to = Date::parse('2021-01-04');

        $res = Date::fillByDays(
            $from, $to,
            [
                'test' => 1,
            ]
        );

        $this->assertEquals(
            [
                '2021-01-01' => ['test' => 1],
                '2021-01-02' => ['test' => 1],
                '2021-01-03' => ['test' => 1],
                '2021-01-04' => ['test' => 1],
            ],
            $res
        );
    }

    public function test_date(): void
    {
        $this->assertEquals(
            date('Y-m-d'),
            Date::date(
                null,
                [
                    'defaultDate' => 'now',
                ]
            )[0]->toDateString()
        );

        $this->assertEquals(
            '2021-01-02',
            Date::date(
                '2021-01-01 12:34:56',
                [
                    'min' => '2021-01-02 00:00:00',
                    'max' => '2021-01-03 23:59:59',
                ]
            )[0]->toDateString()
        );

        $this->assertEquals(
            '2021-01-02',
            Date::date(
                '2021-01-01 12:34:56',
                [
                    'min' => '2021-01-02 00:00:00',
                ]
            )[0]->toDateString()
        );

        $this->assertEquals(
            '2021-01-01',
            Date::date(
                '2021-01-01 12:34:56',
                [
                    'max' => '2021-01-03 23:59:59',
                ]
            )[0]->toDateString()
        );

        $this->assertEquals(
            '2021-01-01',
            Date::date(
                '2021-01-01 12:34:56'
            )[0]->toDateString()
        );

        $this->assertEquals(
            '2021-01-01',
            Date::date(
                '2021-01-01 12:34:56',
                [
                    'min' => '2021-01-01 00:00:00',
                    'max' => '2021-01-01 23:59:59',
                ]
            )[0]->toDateString()
        );
    }

    public function test_from_to(): void
    {
        //
        [$from, $to, $min, $max] = Date::fromTo(
            '2021-01-01',
            '2021-01-04',
            [
                'maxDays' => 2,
            ]
        );

        $this->assertEquals(
            [
                '2021-01-01',
                '2021-01-02',
            ],
            [
                $from->toDateString(),
                $to->toDateString(),
            ]
        );

        //
        [$from, $to, $min, $max] = Date::fromTo(
            '2021-01-01',
            '2021-01-04',
            [
                'maxDays' => 2,
                'sliceBegin' => false,
            ]
        );

        $this->assertEquals(
            [
                '2021-01-03',
                '2021-01-04',
            ],
            [
                $from->toDateString(),
                $to->toDateString(),
            ]
        );

        //
        [$from, $to, $min, $max] = Date::fromTo(
            '2021-01-01',
            '2021-01-30',
            [
                'maxDays' => 30,
                'sliceBegin' => false,
                'min' => '2021-01-02',
                'max' => '2021-01-15',
            ]
        );

        $this->assertEquals(
            [
                '2021-01-02',
                '2021-01-15',
            ],
            [
                $from->toDateString(),
                $to->toDateString(),
            ]
        );
    }
}
