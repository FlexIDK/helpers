<?php

use One23\Helpers\Enums;
use One23\Helpers\Value;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
{
    public function test_val(): void
    {
        $this->assertEquals(
            1,
            Value::val(1)
        );

        $this->assertEquals(
            1.1,
            Value::val(1.1)
        );

        $this->assertEquals(
            '1',
            Value::val('1')
        );

        $this->assertEquals(
            true,
            Value::val(true)
        );

        $this->assertEquals(
            false,
            Value::val(false)
        );

        $this->assertEquals(
            null,
            Value::val(null)
        );

        $this->assertEquals(
            [1, 2, 3],
            Value::val([1, 2, 3])
        );

        $this->assertEquals(
            '1',
            Value::val(function() {
                return '1';
            })
        );

        $this->assertEquals(
            1,
            Value::val(
                Enums\TestInt::Test
            )
        );

        $this->assertEquals(
            'Test',
            Value::val(
                Enums\Test::Test
            )
        );
    }

    public function test_bool(): void
    {
        $this->assertEquals(
            true,
            Value::bool(true)
        );

        $this->assertEquals(
            true,
            Value::bool(1)
        );

        $this->assertEquals(
            true,
            Value::bool('1')
        );

        $this->assertEquals(
            false,
            Value::bool(false)
        );

        $this->assertEquals(
            false,
            Value::bool(0)
        );

        $this->assertEquals(
            false,
            Value::bool('0')
        );

        $this->assertEquals(
            null,
            Value::bool(null, true)
        );

        $this->assertEquals(
            false,
            Value::bool(null, false)
        );

        $this->assertEquals(
            true,
            Value::bool(function() {
                return true;
            })
        );

        $this->assertEquals(
            false,
            Value::bool(function() {
                return false;
            })
        );
    }
}
