<?php

use One23\Helpers\Enums;
use One23\Helpers\Value;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
{
    public function test_first(): void
    {
        $this->assertEquals(
            'a',
            Value::first('a', 'b', 'c')
        );

        $this->assertEquals(
            'a',
            Value::first(null, 'a', 'b', 'c', null)
        );

        $this->assertEquals(
            'a',
            Value::first(null, null, 'a', 'b', 'c', null)
        );

        $this->assertNull(
            Value::first(null, null, null)
        );
    }

    public function test_last(): void
    {
        $this->assertEquals(
            'c',
            Value::last('a', 'b', 'c')
        );

        $this->assertEquals(
            'c',
            Value::last(null, 'a', 'b', 'c', null)
        );

        $this->assertEquals(
            'c',
            Value::last(null, null, 'a', 'b', 'c', null)
        );

        $this->assertNull(
            Value::last(null, null, null)
        );
    }

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

        $this->assertTrue(
            Value::val(true)
        );

        $this->assertFalse(
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
        $this->assertTrue(
            Value::bool(true)
        );

        $this->assertTrue(
            Value::bool(1)
        );

        $this->assertTrue(
            Value::bool('1')
        );

        $this->assertFalse(
            Value::bool(false)
        );

        $this->assertFalse(
            Value::bool(0)
        );

        $this->assertFalse(
            Value::bool('0')
        );

        $this->assertNull(
            Value::bool(null, true)
        );

        $this->assertFalse(
            Value::bool(null, false)
        );

        $this->assertTrue(
            Value::bool(function() {
                return true;
            })
        );

        $this->assertFalse(
            Value::bool(function() {
                return false;
            })
        );
    }
}
