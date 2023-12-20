<?php

use One23\Helpers\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    public function test_ip(): void
    {
        $this->assertEquals(
            true,
            Str::isIp('::1')
        );

        $this->assertEquals(
            true,
            Str::isIp('127.0.0.1')
        );

        $this->assertEquals(
            true,
            Str::isIp('::')
        );

        $this->assertEquals(
            true,
            Str::isIp('2001:0db8:85a3:0000:0000:8a2e:0370:7334')
        );

        $this->assertEquals(
            true,
            Str::isIp('[2001:0db8:85a3:0000:0000:8a2e:0370:7334]')
        );

        $this->assertEquals(
            false,
            Str::isIp('0.0.12.256')
        );
    }

    public function test_ip_v4(): void
    {
        $this->assertEquals(
            true,
            Str::isIpV4('8.8.8.8')
        );

        $this->assertEquals(
            false,
            Str::isIpV4('8.8.256.8')
        );

        $this->assertEquals(
            false,
            Str::isIpV4('::1')
        );

        $this->assertEquals(
            false,
            Str::isIpV4('2001:0db8:85a3:0000:0000:8a2e:0370:7334')
        );
    }

    public function test_ip_v6(): void
    {
        $this->assertEquals(
            true,
            Str::isIpV6('::1')
        );

        $this->assertEquals(
            true,
            Str::isIpV6('2001:0db8:85a3:0000:0000:8a2e:0370:7334')
        );

        $this->assertEquals(
            true,
            Str::isIpV6('[2001:0db8:85a3:0000:0000:8a2e:0370:7334]')
        );

        $this->assertEquals(
            false,
            Str::isIpV6('1.1.1.1')
        );
    }

    public function test_val(): void
    {
        $this->assertEquals(
            '123',
            Str::val('123')
        );

        $this->assertEquals(
            true,
            Str::val(123) === '123'
        );

        $this->assertEquals(
            'abc',
            Str::val('  abc  ')
        );

        $this->assertEquals(
            'abc',
            Str::val('', 'abc')
        );

        $this->assertEquals(
            '  abc  ',
            Str::val('  abc  ', 'abc', false)
        );
    }

    public function test_or_null(): void
    {
        $this->assertEquals(
            null,
            Str::orNull('')
        );

        $this->assertEquals(
            null,
            Str::orNull('  ')
        );

        $this->assertEquals(
            null,
            Str::orNull(null)
        );

        $this->assertEquals(
            'abc',
            Str::orNull('abc')
        );
    }

    public function test_or_blank(): void
    {
        $this->assertEquals(
            '',
            Str::orBlank('')
        );

        $this->assertEquals(
            '',
            Str::orBlank('  ')
        );

        $this->assertEquals(
            '',
            Str::orBlank(null)
        );

        $this->assertEquals(
            'abc',
            Str::orBlank('abc')
        );
    }

    public function test_trim(): void
    {
        $this->assertEquals(
            'asd asd',
            Str::trim("  asd  \n\t\r\v asd")
        );
    }

    public function test_tag(): void
    {
        $this->assertEquals(
            'asd asd',
            Str::tag("  ASD  \n\t\r\v AsD")
        );

        $this->assertEquals(
            null,
            Str::tag("  \n\t\r\v ")
        );
    }

    public function test_email(): void
    {
        $this->assertEquals(
            'test@test.com',
            Str::email('TEST@tesT.CoM')
        );

        $this->assertEquals(
            null,
            Str::email('test@test')
        );

        $this->assertEquals(
            null,
            Str::email('test@test.')
        );

        $this->assertEquals(
            'test@test.c',
            Str::email('test@test.c')
        );

        $this->assertEquals(
            null,
            Str::email('test@test..com')
        );

        $this->assertEquals(
            null,
            Str::email('test@test.com?subject=123')
        );
    }

    public function test_is_email(): void
    {
        $this->assertEquals(
            true,
            Str::isEmail('TEST+abc@tesT.CoM')
        );
    }
}
