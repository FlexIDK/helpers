<?php

use One23\Helpers\Email;
use Tests\TestCase;

class EmailTest extends TestCase
{
    public function test_mask(): void
    {
        $this->assertEquals(
            null,
            Email::mask(null)
        );

        $this->assertEquals(
            't**t@t***f.c*m',
            Email::mask('TEST@tesT.hhahh.ffff.CoM')
        );

        $this->assertEquals(
            't**t@t**t.c*m',
            Email::mask('TEST@tesT.CoM')
        );

        $this->assertEquals(
            null,
            Email::mask('test@test')
        );

        $this->assertEquals(
            't**t@t**t.c*m',
            Email::mask('test@test.com?subject=123')
        );

        $this->assertEquals(
            't*t@g*g.c*c',
            Email::mask('t@g.c')
        );
    }

    public function test_email(): void
    {
        $this->assertEquals(
            'test@test.com',
            Email::val('TEST@tesT.CoM')
        );

        $this->assertEquals(
            null,
            Email::val('test@test')
        );

        $this->assertEquals(
            null,
            Email::val('test@test.')
        );

        $this->assertEquals(
            'test@test.c',
            Email::val('test@test.c')
        );

        $this->assertEquals(
            null,
            Email::val('test@test..com')
        );

        $this->assertEquals(
            'test@test.com',
            Email::val('test@test.com?subject=123')
        );
    }

    public function test_is_valid(): void
    {
        $this->assertTrue(
            Email::isValid('TEST+abc@tesT.CoM')
        );

        $this->assertFalse(
            Email::isValid('test@test.com?subject=123')
        );
    }
}
