<?php

use One23\Helpers\ObjectUrl;
use One23\Helpers\Url;

class UrlTest extends \Tests\TestCase
{
    public function test_parse(): void
    {
        $this->assertTrue(
            is_array(Url::parse('http://test.ru/'))
        );

        $this->assertTrue(
            is_array(Url::parse('http://test.ru:80/'))
        );

        $this->assertTrue(
            is_array(Url::parse('http://abc:abc@test.ru:80/'))
        );

        $this->assertTrue(
            is_array(Url::parse('http://192.168.0.1:80/'))
        );

        $this->assertTrue(
            is_array(Url::parse('http://[::1]:80/'))
        );

        $this->assertNull(Url::parse('test.ru'));
        $this->assertNull(Url::parse('//test.ru'));
    }

    public function test_build(): void
    {
        $this->assertEquals(
            'http://test.ru/',
            Url::build([
                'scheme' => 'http',
                'host' => 'test.ru',
            ])
        );

        $this->assertEquals(
            'https://abc.ru:80/abc',
            Url::build(
                [
                    'scheme' => 'http',
                    'host' => 'test.ru',
                    'port' => 80,
                    'path' => '/abc',
                ],
                [
                    'scheme' => 'https',
                    'host' => 'abc.ru',
                ]
            )
        );

        $this->assertNull(
            Url::build([
                'scheme' => 'http',
                'host' => null,
                'port' => 80,
                'path' => '/abc',
            ])
        );

        $this->assertNull(
            Url::build('test.ru')
        );
    }

    public function test_object(): void
    {
        $this->assertInstanceOf(
            ObjectUrl::class,
            Url::object('http://test.ru/')
        );
    }

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

        $this->assertFalse(Url::isIpV4('test.ru'));
    }

    public function test_is_http(): void
    {
        $this->assertTrue(Url::isHttp(
            new ObjectUrl('http://test.ru/')
        ));

        $this->assertTrue(Url::isHttp([
            'scheme' => 'https',
            'host' => 'test.ru',
        ]));

        $this->assertFalse(Url::isHttp([
            'scheme' => 'ftp',
            'host' => 'test.ru',
        ]));

        $this->assertTrue(Url::isHttp('http://test.ru/'));
        $this->assertTrue(Url::isHttp('https://test.ru/'));
        $this->assertTrue(Url::isHttp('https://127.0.0.1/'));
        $this->assertTrue(Url::isHttp('https://[::1]/'));
        $this->assertFalse(Url::isHttp('ftp://test.ru/'));
        $this->assertFalse(Url::isHttp('test.ru'));
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
            'яндекс.ру',
            Url::host('http://яндекс.ру:80', ['hostHuman' => true])
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
            'δοκιμή.परीक्षा',
            Url::hostHuman('http://www.xn--jxalpdlp.xn--11b5bs3a9aj6g:80')
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

        $this->assertEquals(
            'яндекс.ру',
            Url::host2level('http://www.яндекс.ру:80', ['hostHuman' => true])
        );
    }
}
