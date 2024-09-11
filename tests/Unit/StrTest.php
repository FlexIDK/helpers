<?php

use One23\Helpers\Exceptions\Str as Exception;
use One23\Helpers\Str;
use Tests\TestCase;

class StrTest extends TestCase
{
    public function test_hasEntityCharters(): void
    {
        $this->assertTrue(
            ! Str::hasEntityCharters('abc &a abc')
        );

        $this->assertTrue(
            Str::hasEntityCharters('abc &30;abc ')
        );

        $this->assertTrue(
            Str::hasEntityCharters('abc &#123;abc ')
        );

        $this->assertTrue(
            Str::hasEntityCharters('abc &#x1a2;abc ')
        );

        $this->assertTrue(
            ! Str::hasEntityCharters('abc &#x1g2;abc ')
        );

        $this->assertTrue(
            Str::hasEntityCharters('abc &hellip; abc ')
        );
    }

    public function test_first(): void
    {
        $this->assertEquals(
            'a',
            Str::first('a', 'b', 'c')
        );

        $this->assertEquals(
            'a',
            Str::first(null, 'a', 'b', 'c', null)
        );

        $this->assertEquals(
            'a',
            Str::first(null, null, 'a', 'b', 'c', null)
        );

        $this->assertNull(
            Str::first(null, null, null)
        );
    }

    public function test_last(): void
    {
        $this->assertEquals(
            'c',
            Str::last('a', 'b', 'c')
        );

        $this->assertEquals(
            '1',
            Str::last(null, 'a', 'b', 'c', 1)
        );

        $this->assertEquals(
            'c',
            Str::last(null, null, 'a', 'b', 'c', null)
        );

        $this->assertNull(
            Str::last(null, null, null)
        );
    }

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

        $this->assertException(function() {
            Str::md5(null);
        }, Exception::class);

        $this->assertException(function() {
            Str::md5('abc', 0);
        }, Exception::class);

        $this->assertException(function() {
            Str::md5('abc', 33);
        }, Exception::class);
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

    public function test_with(): void
    {
        $this->assertFalse(
            Str::with(null, 'text', false, $match)
        );

        $text = <<<'TEXT'
Lorem ipsum sit amet1amet amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor
TEXT;

        $this->assertFalse(
            Str::with($text, 'text', false, $match)
        );
        $this->assertNull($match);

        $this->assertFalse(
            Str::with($text, 'it', false, $match)
        );
        $this->assertNull($match);

        $this->assertTrue(
            Str::with($text, 'lorem', false, $match)
        );
        $this->assertEquals('lorem', $match);

        $this->assertFalse(
            Str::with($text, 'LOREM', true, $match)
        );
        $this->assertNull($match);

        $this->assertTrue(
            Str::with($text, 'Lorem', true, $match)
        );
        $this->assertEquals('Lorem', $match);

        $this->assertTrue(
            Str::with($text, 'amet', false, $match)
        );
        $this->assertEquals('amet', $match);

        $this->assertTrue(
            Str::with($text, 'dolor', false, $match)
        );
        $this->assertEquals('dolor', $match);

        $this->assertTrue(
            Str::with($text, 'elit', false, $match)
        );
        $this->assertEquals('elit', $match);

        $this->assertTrue(
            Str::with($text, 'Aenean', false, $match)
        );
        $this->assertEquals('aenean', $match);
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
            Str::endWith('abc', 'A', true, $match)
        );
        $this->assertNull($match);

        $this->assertFalse(
            Str::endWith('abc', 'C', true, $match)
        );
        $this->assertNull($match);

        $this->assertTrue(
            Str::endWith('abc', ['e', 'c', 'b'], false, $match)
        );
        $this->assertEquals('c', $match);
    }

    public function test_contains(): void
    {
        $this->assertTrue(
            Str::contains('ABC', 'c', false, $match)
        );
        $this->assertEquals('c', $match);

        $this->assertTrue(
            Str::contains('aBC', ['b'], false, $match)
        );
        $this->assertEquals('b', $match);

        $this->assertTrue(
            Str::contains('abc', 'a')
        );

        $this->assertFalse(
            Str::contains('abc', 'C', true, $match)
        );
        $this->assertNull($match);

        $this->assertFalse(
            Str::contains('abc', 'A')
        );

        $this->assertTrue(
            Str::contains('abc', ['e', 'c', 'f', 'a'], true, $match)
        );
        $this->assertEquals('c', $match);
    }

    public function test_start_with(): void
    {
        $this->assertTrue(
            Str::startWith('abc', ['b', 'c', 'a'], true, $match)
        );
        $this->assertEquals('a', $match);

        $this->assertTrue(
            Str::startWith('ABC', 'a', false, $match)
        );
        $this->assertEquals('a', $match);

        $this->assertTrue(
            Str::startWith('aBC', ['A'], false, $match)
        );
        $this->assertEquals('a', $match);

        $this->assertTrue(
            Str::startWith('abc', 'a')
        );

        $this->assertFalse(
            Str::startWith('abc', 'b', true, $match)
        );
        $this->assertNull($match);

        $this->assertFalse(
            Str::startWith('abc', 'A')
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
