<?php

use One23\Helpers\DateBetween;
use PHPUnit\Framework\TestCase;

class DateBetweenTest extends TestCase
{
    public function test_before(): void
    {
        $this->assertNull(
            (new DateBetween(
                [
                    '0000-01-01' => null,
                    '2021-01-02' => 'aaa',
                    '9999-01-01' => null,
                ],
                \Carbon\Carbon::make('2021-02-01'),
                \Carbon\Carbon::make('2021-02-10'),
            ))->before()
        );

        $this->assertEquals(
            'aaa',
            (new DateBetween(
                [
                    '0000-01-01' => 'aaa',
                    '2021-01-01' => null,
                    '9999-01-01' => null,
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->before()
        );

        $this->assertEquals(
            'aaa',
            (new DateBetween(
                [
                    '0000-01-01' => null,
                    '2021-01-01' => null,
                    '2021-01-03' => 'bbb',
                    '2021-01-02' => 'aaa',
                    '9999-01-01' => 'ccc',
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->before()
        );

        $this->assertEquals(
            'aaa',
            (new DateBetween(
                [
                    '0000-01-01' => null,
                    '2021-01-01' => null,
                    '9999-01-01' => 'aaa',
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->before()
        );
    }

    public function test_start()
    {
        $this->assertEquals(
            'aaa',
            (new DateBetween(
                [
                    '0000-01-01' => 'aaa',
                    '2021-01-01' => null,
                    '9999-01-01' => null,
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->start()
        );

        $this->assertEquals(
            'bbb',
            (new DateBetween(
                [
                    '0000-01-01' => 'aaa',
                    '2021-01-01' => 'bbb',
                    '9999-01-01' => null,
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->start()
        );

        $this->assertEquals(
            'aaa',
            (new DateBetween(
                [
                    '0000-01-01' => 'aaa',
                    '2021-01-02' => 'bbb',
                    '9999-01-01' => null,
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->start()
        );

        $this->assertEquals(
            'aaa',
            (new DateBetween(
                [
                    '0000-01-01' => null,
                    '2021-01-02' => 'aaa',
                    '9999-01-01' => 'bbb',
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->start()
        );

        $this->assertEquals(
            'bbb',
            (new DateBetween(
                [
                    '0000-01-01' => null,
                    '9999-01-01' => 'bbb',
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->start()
        );

        $this->assertNull(
            (new DateBetween(
                [
                    '0000-01-01' => null,
                    '2021-02-02' => 'bbb',
                    '9999-01-01' => null,
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->start()
        );
    }

    public function test_end()
    {
        $this->assertEquals(
            'bbb',
            (new DateBetween(
                [
                    '0000-01-01' => 'aaa',
                    '2021-01-01' => 'bbb',
                    '9999-01-01' => 'ccc',
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->end()
        );

        $this->assertEquals(
            'ccc',
            (new DateBetween(
                [
                    '0000-01-01' => 'aaa',
                    '2021-01-01' => 'bbb',
                    '2021-01-09' => 'ccc',
                    '9999-01-01' => 'ddd',
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->end()
        );

        $this->assertEquals(
            'ddd',
            (new DateBetween(
                [
                    '0000-01-01' => 'aaa',
                    '2021-01-01' => 'bbb',
                    '2021-01-09' => 'ccc',
                    '2021-01-10' => 'ddd',
                    '9999-01-01' => 'eee',
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->end()
        );

        $this->assertEquals(
            'aaa',
            (new DateBetween(
                [
                    '0000-01-01' => 'aaa',
                    '2021-02-01' => 'bbb',
                    '9999-01-01' => 'ccc',
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->end()
        );

        $this->assertEquals(
            'ccc',
            (new DateBetween(
                [
                    '0000-01-01' => null,
                    '2021-02-01' => 'bbb',
                    '9999-01-01' => 'ccc',
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->end()
        );
    }

    public function test_after(): void
    {
        $this->assertNull(
            (new DateBetween(
                [
                    '0000-01-01' => null,
                    '2021-01-02' => 'aaa',
                    '9999-01-01' => null,
                ],
                \Carbon\Carbon::make('2021-02-01'),
                \Carbon\Carbon::make('2021-02-10'),
            ))->after()
        );

        $this->assertEquals(
            'aaa',
            (new DateBetween(
                [
                    '0000-01-01' => 'aaa',
                    '2021-01-01' => null,
                    '9999-01-01' => null,
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->after()
        );

        $this->assertEquals(
            'bbb',
            (new DateBetween(
                [
                    '0000-01-01' => null,
                    '2021-01-01' => null,
                    '2021-01-03' => 'bbb',
                    '2021-01-02' => 'aaa',
                    '9999-01-01' => null,
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->after()
        );

        $this->assertEquals(
            'aaa',
            (new DateBetween(
                [
                    '0000-01-01' => 'bbb',
                    '2021-01-01' => 'ccc',
                    '9999-01-01' => 'aaa',
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-10'),
            ))->after()
        );
    }

    public function test_all()
    {
        $this->assertEquals(
            [
                '2021-01-01' => 'aaa',
                '2021-01-02' => 'aaa',
                '2021-01-03' => 'aaa',
            ],
            (new DateBetween(
                [
                    '0000-01-01' => null,
                    '2021-01-02' => 'aaa',
                    '9999-01-01' => null,
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-03'),
            ))->all()
        );

        $this->assertEquals(
            [
                '2021-01-01' => 'aaa',
                '2021-01-02' => 'bbb',
                '2021-01-03' => 'bbb',
            ],
            (new DateBetween(
                [
                    '0000-01-01' => 'aaa',
                    '2021-01-02' => 'bbb',
                    '9999-01-01' => null,
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-03'),
            ))->all()
        );

        $this->assertEquals(
            [
                '2021-01-01' => 'aaa',
                '2021-01-02' => 'bbb',
                '2021-01-03' => 'bbb',
            ],
            (new DateBetween(
                [
                    '0000-01-01' => 'aaa',
                    '2021-01-02' => 'bbb',
                    '9999-01-01' => 'ccc',
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-03'),
            ))->all()
        );

        $this->assertEquals(
            [
                '2021-01-01' => 'ccc',
                '2021-01-02' => 'ccc',
                '2021-01-03' => 'ccc',
            ],
            (new DateBetween(
                [
                    '0000-01-01' => null,
                    '2021-01-02' => null,
                    '9999-01-01' => 'ccc',
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-03'),
            ))->all()
        );

        $this->assertEquals(
            [
                '2021-01-01' => 'aaa',
                '2021-01-02' => 'aaa',
                '2021-01-03' => 'aaa',
            ],
            (new DateBetween(
                [
                    '0000-01-01' => 'aaa',
                    '2021-01-02' => null,
                    '9999-01-01' => 'ccc',
                ],
                \Carbon\Carbon::make('2021-01-01'),
                \Carbon\Carbon::make('2021-01-03'),
            ))->all()
        );
    }

    public function test_get()
    {
        $obj = (new DateBetween(
            [
                '0000-01-01' => 'aaa',
                '2021-01-01' => 'bbb',
                '2021-01-02' => 'ccc',
                '2021-01-03' => 'ddd',
                '9999-01-01' => 'eee',
            ],
            \Carbon\Carbon::make('2021-01-01'),
            \Carbon\Carbon::make('2021-01-03'),
        ));

        $this->assertEquals(
            'aaa',
            $obj->get(\Carbon\Carbon::make('2020-01-01'))
        );

        $this->assertEquals(
            'bbb',
            $obj->get(\Carbon\Carbon::make('2021-01-01'))
        );

        $this->assertEquals(
            'ccc',
            $obj->get(\Carbon\Carbon::make('2021-01-02'))
        );

        $this->assertEquals(
            'ddd',
            $obj->get(\Carbon\Carbon::make('2021-01-03'))
        );

        $this->assertEquals(
            'eee',
            $obj->get(\Carbon\Carbon::make('2021-02-01'))
        );

        //

        $obj = (new DateBetween(
            [
                '0000-01-01' => 'aaa',
                '9999-01-01' => 'eee',
            ],
            \Carbon\Carbon::make('2021-01-01'),
            \Carbon\Carbon::make('2021-01-03'),
        ));

        $this->assertEquals(
            'aaa',
            $obj->get(\Carbon\Carbon::make('2020-01-01'))
        );

        $this->assertEquals(
            'aaa',
            $obj->get(\Carbon\Carbon::make('2021-01-01'))
        );

        $this->assertEquals(
            'aaa',
            $obj->get(\Carbon\Carbon::make('2021-01-03'))
        );

        $this->assertEquals(
            'eee',
            $obj->get(\Carbon\Carbon::make('2021-02-01'))
        );

        //

        $obj = (new DateBetween(
            [
                '9999-01-01' => 'eee',
            ],
            \Carbon\Carbon::make('2021-01-01'),
            \Carbon\Carbon::make('2021-01-03'),
        ));

        $this->assertEquals(
            'eee',
            $obj->get(\Carbon\Carbon::make('2020-01-01'))
        );

        $this->assertEquals(
            'eee',
            $obj->get(\Carbon\Carbon::make('2022-01-01'))
        );

        //

        $obj = (new DateBetween(
            [
                '2021-01-01' => 'aaa',
                '2021-01-02' => 'bbb',
                '2021-01-03' => 'ccc',
            ],
            \Carbon\Carbon::make('2021-01-01'),
            \Carbon\Carbon::make('2021-01-03'),
        ));

        $this->assertEquals(
            'aaa',
            $obj->get(\Carbon\Carbon::make('2020-01-01'))
        );

        $this->assertEquals(
            'bbb',
            $obj->get(\Carbon\Carbon::make('2021-01-02'))
        );

        $this->assertEquals(
            'ccc',
            $obj->get(\Carbon\Carbon::make('2022-01-01'))
        );
    }

    public function test_now()
    {
        $obj = (new DateBetween(
            [
                '0000-01-01' => 'aaa',
                '9999-01-01' => 'eee',
            ],
            \Carbon\Carbon::now()->subDay(),
            \Carbon\Carbon::now()->addDay(),
        ));

        $this->assertEquals(
            'aaa',
            $obj->now()
        );

        $obj = (new DateBetween(
            [
                '0000-01-01' => 'aaa',
                \Carbon\Carbon::now()->toDateString() => 'bbb',
                '9999-01-01' => 'eee',
            ],
            \Carbon\Carbon::now()->subDay(),
            \Carbon\Carbon::now()->addDay(),
        ));

        $this->assertEquals(
            'bbb',
            $obj->now()
        );
    }
}
