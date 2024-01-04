<?php

use One23\Helpers\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    public function test_md5(): void
    {
        $this->assertMatchesRegularExpression(
            '/^[a-f0-9]{32}$/',
            Str::md5('abc')
        );

        $this->assertMatchesRegularExpression(
            '/^[a-f0-9]{8}$/',
            Str::md5('abc', 8)
        );

        try {
            Str::md5(null);
            Str::md5('abc', 0);
            Str::md5('abc', 33);

            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_is_crc(): void
    {
        $this->assertTrue(
            Str::isCrc('1234567890abcdef1234567890abcdef')
        );

        $this->assertTrue(
            Str::isCrc('1234567890ABCDEF1234567890ABCDEF')
        );

        $this->assertFalse(
            Str::isCrc('1234567890abcdef1234567890abcde')
        );

        $this->assertTrue(
            Str::isCrc('1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef', 64)
        );

        $this->assertFalse(
            Str::isCrc('1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcde', 64)
        );
    }

    public function test_end_with(): void
    {
        $this->assertTrue(
            Str::endWith('ABC', 'c', false)
        );

        $this->assertTrue(
            Str::endWith('aBC', ['C'], false)
        );

        $this->assertTrue(
            Str::endWith('abc', 'c')
        );

        $this->assertFalse(
            Str::endWith('abc', 'A')
        );

        $this->assertFalse(
            Str::endWith('abc', 'C')
        );

        $this->assertTrue(
            Str::endWith('abc', ['e', 'c', 'b'])
        );
    }

    public function test_contains(): void
    {
        $this->assertTrue(
            Str::contains('ABC', 'c', false)
        );

        $this->assertTrue(
            Str::contains('aBC', ['b'], false)
        );

        $this->assertTrue(
            Str::contains('abc', 'a')
        );

        $this->assertFalse(
            Str::contains('abc', 'C')
        );

        $this->assertFalse(
            Str::contains('abc', 'A')
        );

        $this->assertTrue(
            Str::contains('abc', ['e', 'f', 'a'])
        );
    }

    public function test_start_with(): void
    {
        $this->assertTrue(
            Str::startWith('ABC', 'a', false)
        );

        $this->assertTrue(
            Str::startWith('aBC', ['A'], false)
        );

        $this->assertTrue(
            Str::startWith('abc', 'a')
        );

        $this->assertFalse(
            Str::startWith('abc', 'b')
        );

        $this->assertFalse(
            Str::startWith('abc', 'A')
        );

        $this->assertTrue(
            Str::startWith('abc', ['b', 'c', 'a'])
        );
    }

    public function test_ip(): void
    {
        $this->assertTrue(
            Str::isIp('::1')
        );

        $this->assertTrue(
            Str::isIp('127.0.0.1')
        );

        $this->assertTrue(
            Str::isIp('::')
        );

        $this->assertTrue(
            Str::isIp('2001:0db8:85a3:0000:0000:8a2e:0370:7334')
        );

        $this->assertTrue(
            Str::isIp('[2001:0db8:85a3:0000:0000:8a2e:0370:7334]')
        );

        $this->assertFalse(
            Str::isIp('0.0.12.256')
        );
    }

    public function test_ip_v4(): void
    {
        $this->assertTrue(
            Str::isIpV4('8.8.8.8')
        );

        $this->assertFalse(
            Str::isIpV4('8.8.256.8')
        );

        $this->assertFalse(
            Str::isIpV4('::1')
        );

        $this->assertFalse(
            Str::isIpV4('2001:0db8:85a3:0000:0000:8a2e:0370:7334')
        );
    }

    public function test_ip_v6(): void
    {
        $this->assertTrue(
            Str::isIpV6('::1')
        );

        $this->assertTrue(
            Str::isIpV6('2001:0db8:85a3:0000:0000:8a2e:0370:7334')
        );

        $this->assertTrue(
            Str::isIpV6('[2001:0db8:85a3:0000:0000:8a2e:0370:7334]')
        );

        $this->assertFalse(
            Str::isIpV6('1.1.1.1')
        );
    }

    public function test_val(): void
    {
        $this->assertEquals(
            '123',
            Str::val('123')
        );

        $this->assertTrue(
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
        $this->assertTrue(
            Str::isEmail('TEST+abc@tesT.CoM')
        );
    }
}
