<?php

use One23\Helpers\Db;
use Tests\TestCase;

class DbTest extends TestCase
{
    public function test_arr2json(): void
    {
        $this->assertEquals(
            [],
            Db::arr2json([])
        );

        $this->assertEquals(
            [],
            Db::arr2json([1, 2, 3])
        );

        $this->assertEquals(
            [
                'data->a' => 1,
                'data->b' => 2,
                'data->c' => 3,
            ],
            Db::arr2json(['a' => 1, 'b' => 2, 'c' => 3])
        );
    }

    public function test_bool(): void
    {
        $this->assertTrue(
            Db::bool(true) === 1
        );

        $this->assertTrue(
            Db::bool(false) === 0
        );

        $this->assertTrue(
            Db::bool(null) === 0
        );

        $this->assertTrue(
            is_null(Db::bool(null, true))
        );
    }
}
