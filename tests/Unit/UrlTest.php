<?php

use One23\Helpers\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    public function test_is_ip(): void
    {
        $this->assertTrue(Url::isIp('http://127.0.0.1/'));
        $this->assertTrue(Url::isIpV4('http://127.0.0.1/'));
        $this->assertFalse(Url::isIpV6('http://127.0.0.1/'));

        $this->assertFalse(Url::isIp('http://test.ru/'));
        $this->assertFalse(Url::isIpV4('http://test.ru/'));
        $this->assertFalse(Url::isIpV6('http://test.ru/'));

        $this->assertTrue(Url::isIp('http://[::1]/'));
        $this->assertTrue(Url::isIpV6('http://[::1]/'));
        $this->assertFalse(Url::isIpV4('http://[::1]/'));
    }

    public function test_is_http(): void
    {
        $this->assertTrue(Url::isHttp('http://test.ru/'));
        $this->assertTrue(Url::isHttp('https://test.ru/'));
        $this->assertFalse(Url::isHttp('ftp://test.ru/'));
    }

    public function test_host(): void
    {
        $this->assertEquals(
            'test.ru',
            Url::host('http://test.ru/')
        );

        $this->assertEquals(
            'test.ru',
            Url::host('http://test.ru:80/')
        );

        $this->assertEquals(
            'xn--d1acpjx3f.xn--p1ag',
            Url::host('http://яндекс.ру:80')
        );

        $this->assertEquals(
            '127.0.0.1',
            Url::host('http://127.0.0.1:80/')
        );

        $this->assertEquals(
            '[::1]',
            Url::host('http://[::1]:80/')
        );
    }

    public function test_host_human(): void
    {
        $this->assertEquals(
            'test.ru',
            Url::hostHuman('http://test.ru/')
        );

        $this->assertEquals(
            'test.ru',
            Url::hostHuman('http://test.ru:80/')
        );

        $this->assertEquals(
            'яндекс.ру',
            Url::hostHuman('http://яндекс.ру:80')
        );

        $this->assertEquals(
            'яндекс.ру',
            Url::hostHuman('http://www.яндекс.ру:80')
        );

        $this->assertEquals(
            'test.ru',
            Url::hostHuman('http://www.test.ru:80')
        );

        $this->assertEquals(
            'www.test.ru',
            Url::hostHuman('http://www.www.test.ru:80')
        );

        $this->assertEquals(
            '127.0.0.1',
            Url::hostHuman('http://127.0.0.1:80/')
        );

        $this->assertEquals(
            '[::1]',
            Url::hostHuman('http://[::1]:80/')
        );
    }

    public function test_host_2_level(): void
    {
        $this->assertEquals(
            'test.ru',
            Url::host2level('http://test.ru/')
        );

        $this->assertEquals(
            'test.ru',
            Url::host2level('http://www.test.ru:80/')
        );

        $this->assertEquals(
            'xn--d1acpjx3f.xn--p1ag',
            Url::host2level('http://abc.яндекс.ру:80')
        );

        $this->assertEquals(
            'xn--d1acpjx3f.xn--p1ag',
            Url::host2level('http://www.яндекс.ру:80')
        );

        $this->assertEquals(
            'test.ru',
            Url::host2level('http://www.www.test.ru:80')
        );

        $this->assertEquals(
            '127.0.0.1',
            Url::host2level('http://127.0.0.1:80/')
        );

        $this->assertEquals(
            '[::1]',
            Url::host2level('http://[::1]:80/')
        );
    }
}
