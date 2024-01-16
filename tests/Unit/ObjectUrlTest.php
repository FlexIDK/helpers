<?php

use One23\Helpers\Arr;
use One23\Helpers\Exceptions\Url as Exception;
use One23\Helpers\ObjectUrl;

class ObjectUrlTest extends \Tests\TestCase
{
    public function test_encode_decode(): void
    {
        $components = [
            'scheme' => 'https',
            'user' => 'а+б:в.г',
            'pass' => 'а+б@в.г',
            'host' => 'яндекс.ru',
            'port' => 8080,
            'fragment' => '#a+б:в@г д',
        ];

        $u = new ObjectUrl(
            $components,
            [
                'acceptAuth' => true,
                'acceptPort' => true,
            ]
        );

        $this->assertEquals(
            'https://%D0%B0%2B%D0%B1%3A%D0%B2.%D0%B3:%D0%B0%2B%D0%B1%40%D0%B2.%D0%B3@xn--d1acpjx3f.ru:8080/#%23a%2B%D0%B1%3A%D0%B2%40%D0%B3%20%D0%B4',
            $u->toString()
        );

        //

        $url = (new ObjectUrl(
            $u->toString(),
            [
                'acceptAuth' => true,
                'acceptPort' => true,
            ]
        ));

        $this->assertEquals(
            $components['user'],
            $url->getUser()
        );

        $this->assertEquals(
            $components['pass'],
            $url->getPass()
        );

        $this->assertEquals(
            $components['fragment'],
            $url->getFragment()
        );

        $this->assertEquals(
            $components['host'],
            $url->getHostHuman()
        );
    }

    public function test_to_array()
    {
        $url = new ObjectUrl(
            'https://user:pass@[2001:0db8:85a3:0000:0000:8a2e:0370:7334]:8080/path?queryBlank=&queryNull[][]#fragment',
            [
                'acceptAuth' => true,
                'acceptIp' => true,
                'acceptPort' => true,
            ]
        );

        $this->assertEquals(
            Arr::filterNull([
                'scheme' => 'https',
                'host' => '[2001:0db8:85a3:0000:0000:8a2e:0370:7334]',
                'port' => 8080,
                'user' => 'user',
                'pass' => 'pass',
                'path' => '/path',
                'query' => [
                    'queryBlank' => '',
                    'queryNull' => [
                        0 => [
                            0 => null,
                        ],
                    ],
                ],
                'fragment' => 'fragment',
            ]),
            $url->toArray()
        );
    }

    public function test_port()
    {
        $url = new ObjectUrl('https://www.example.com:8080/path?#fragment', [
            'acceptPort' => true,
        ]);
        $this->assertEquals(
            '8080',
            $url->getPort()
        );

        $url = new ObjectUrl('https://www.example.com:443/path?#fragment', [
            'acceptPort' => null,
        ]);
        $this->assertEquals(
            '443',
            $url->getPort()
        );

        $this->assertException(function() {
            new ObjectUrl('https://www.example.com:123/path?#fragment', [
                'acceptPort' => false,
            ]);
        }, Exception::class);

        $this->assertException(function() {
            new ObjectUrl('https://www.example.com/path?#fragment', [
                'acceptPort' => true,
            ]);
        }, Exception::class);
    }

    public function test_scheme(): void
    {
        $url = new ObjectUrl('https://www.example.com/path?#fragment');
        $this->assertEquals(
            'https',
            $url->getScheme()
        );

        $this->assertEquals(
            'http',
            $url->setScheme('http')->getScheme()
        );

        $this->assertEquals(
            'ftp',
            $url->setScheme('ftp', ['onlyHttp' => null])->getScheme()
        );

        $this->assertEquals(
            'ftp',
            $url->setScheme('ftp', ['onlyHttp' => false])->getScheme()
        );

        $this->assertEquals(
            'one23-scheme',
            $url->setScheme('one23-scheme', ['onlyHttp' => null])->getScheme()
        );

        $url = new ObjectUrl('https://www.example.com/path?#fragment');

        $this->assertException(function() use ($url) {
            $url->setScheme('ftp', ['onlyHttp' => true])->getScheme();
        }, Exception::class);

        $this->assertException(function() use ($url) {
            $url->setScheme('http', ['onlyHttp' => false])->getScheme();
        }, Exception::class);

        $this->assertException(function() {
            new ObjectUrl('/www.example.com:123/path?#fragment', [
                'defaultScheme' => 'http',
            ]);
        }, Exception::class);

        $this->assertException(function() {
            new ObjectUrl('абв://www.example.com:123/path?#fragment', [
                'onlyHttp' => null,
            ]);
        }, Exception::class);

        $this->assertEquals(
            'https://www.example.com/path#fragment',
            (new ObjectUrl(
                '//www.example.com/path?#fragment',
                [
                    'defaultScheme' => 'https',
                ]
            ))->toString()
        );

        $this->assertEquals(
            'ftp://www.example.com/#fragment',
            (new ObjectUrl(
                '//www.example.com?#fragment',
                [
                    'defaultScheme' => 'ftp',
                    'onlyHttp' => null,
                ]
            ))->toString()
        );
    }

    public function test_host(): void
    {
        $url = new ObjectUrl('https://www.example.com/path?#fragment');

        $this->assertEquals(
            'www.example.com',
            $url->getHost()
        );

        $this->assertEquals(
            'xn--d1acpjx3f.xn--p1ag',
            $url->setHost('яндекс.ру')->getHost()
        );

        $this->assertEquals(
            'test.ru',
            $url->setHost('test.ru')->getHost()
        );

        $this->assertEquals(
            'www.facebook.ru',
            $url->setHost('www.facebook.ru')->getHost()
        );

        $this->assertEquals(
            'xn--d1acpjx3f.ru',
            $url->setHost('яндекс.ru')->getHost()
        );

        $this->assertEquals(
            'abc.facebook.ru',
            $url->setHost('abc.facebook.ru', [
                'minHostLevel' => 3,
            ])->getHost()
        );

        //

        $this->assertException(function() use ($url) {
            $url->setHost('www.ru', [
                'minHostLevel' => 2,
            ]);
        }, Exception::class);

        $this->assertException(function() use ($url) {
            $url->setHost('www.facebook.ru', [
                'minHostLevel' => 3,
            ]);
        }, Exception::class);

        // ipv4

        $this->assertEquals(
            '127.0.0.1',
            $url->setHost('127.0.0.1', [
                'acceptIp' => null,
            ])->getHost()
        );

        $this->assertEquals(
            '127.0.0.1',
            $url->setHost('127.0.0.1', [
                'acceptIp' => true,
            ])->getHost()
        );

        $this->assertException(function() use ($url) {
            $url->setHost('127.0.0.1', [
                'acceptIp' => false,
            ]);
        }, Exception::class);

        // ipv6

        $this->assertEquals(
            '[2001:0db8:85a3:0000:0000:8a2e:0370:7334]',
            $url->setHost('[2001:0db8:85a3:0000:0000:8a2e:0370:7334]', [
                'acceptIp' => null,
            ])->getHost()
        );

        $this->assertEquals(
            '[::1]',
            $url->setHost('[::1]', [
                'acceptIp' => true,
            ])->getHost()
        );

        $this->assertException(function() use ($url) {
            $url->setHost('[2001:0db8:85a3:0000:0000:8a2e:0370:7334]', [
                'acceptIp' => false,
            ]);
        }, Exception::class);

        // invalid host

        $this->assertException(function() use ($url) {
            $url->setHost('a..b.ru');
        }, Exception::class);

        $this->assertException(function() use ($url) {
            $url->setHost('-abc.ru');
        }, Exception::class);

        $this->assertException(function() use ($url) {
            $url->setHost('abc-.ru');
        }, Exception::class);

        $this->assertException(function() use ($url) {
            $url->setHost('-.ru');
        }, Exception::class);

        $this->assertException(function() use ($url) {
            $url->setHost('a@b.ru');
        }, Exception::class);

        $this->assertException(function() use ($url) {
            $url->setHost('a%b.ru');
        });

        $this->assertException(function() use ($url) {
            $url->setHost('a?b.ru');
        }, Exception::class);

        $this->assertException(function() use ($url) {
            $url->setHost('a,b.ru');
        }, Exception::class);

        $this->assertException(function() use ($url) {
            $url->setHost('a+b.ru');
        }, Exception::class);

        $this->assertException(function() use ($url) {
            $url->setHost('*.abc.ru');
        }, Exception::class);

        $this->assertEquals(
            '*.abc.ru',
            $url->setHost('*.abc.ru', [
                'allowWildcard' => true,
            ])->getHost()
        );

        //

        $this->assertException(function() {
            new ObjectUrl(
                'http://localhost/',
                [
                    'minHostLevel' => 2,
                ]
            );
        });

        $a = array_fill(0, 127, 'a');
        $maxUrl = 'https://' . implode('.', $a) . '/';

        $this->assertEquals(
            $maxUrl,
            (new ObjectUrl($maxUrl))->toString()
        );

        $this->assertException(function() use ($maxUrl) {
            new ObjectUrl(
                $maxUrl,
                [
                    'maxHostLevel' => 126,
                ]
            );
        });

        $this->assertException(function() use ($maxUrl) {
            new ObjectUrl(
                $maxUrl,
                [
                    'maxHostLength' => 100,
                ]
            );
        });
    }

    public function test_host_human(): void
    {
        $url = new ObjectUrl('https://www.example.com/path?#fragment');

        $this->assertEquals(
            'www.example.com',
            $url->getHost()
        );

        $this->assertEquals(
            'яндекс.ру',
            $url->setHost('яндекс.ру')->getHostHuman()
        );

        $this->assertEquals(
            'test.ru',
            $url->setHost('test.ru')->getHostHuman()
        );

        $this->assertEquals(
            'facebook.ru',
            $url->setHost('www.facebook.ru')->getHostHuman()
        );

        $this->assertEquals(
            'яндекс.ru',
            $url->setHost('www.яндекс.ru')->getHostHuman()
        );
    }

    public function test_host_1_level(): void
    {
        $url = new ObjectUrl('https://www.example.com/path?#fragment');

        $this->assertEquals(
            'com',
            $url->getHost1Level()
        );

        $this->assertEquals(
            'xn--p1ag',
            $url->setHost('яндекс.ру')->getHost1Level()
        );

        $this->assertEquals(
            'ru',
            $url->setHost('test.ru')->getHost1Level()
        );

        $this->assertEquals(
            'ru',
            $url->setHost('www.facebook.ru')->getHost1Level()
        );

        $this->assertEquals(
            'ru',
            $url->setHost('www.яндекс.ru')->getHost1Level()
        );
    }

    public function test_host_2_level(): void
    {
        $url = new ObjectUrl('https://www.example.com/path?#fragment');

        $this->assertEquals(
            'example.com',
            $url->getHost2Level()
        );

        $this->assertEquals(
            'xn--d1acpjx3f.xn--p1ag',
            $url->setHost('яндекс.ру')->getHost2Level()
        );

        $this->assertEquals(
            'test.ru',
            $url->setHost('test.ru')->getHost2Level()
        );

        $this->assertEquals(
            'facebook.ru',
            $url->setHost('www.dev.facebook.ru')->getHost2Level()
        );

        $this->assertEquals(
            'xn--d1acpjx3f.ru',
            $url->setHost('www.яндекс.ru')->getHost2Level()
        );
    }

    public function test_path(): void
    {
        $url = new ObjectUrl('https://www.example.com/path?#fragment');

        $url->setPath(null);
        $this->assertEquals(
            '/',
            $url->getPath()
        );

        $url->setPath('');
        $this->assertEquals(
            '/',
            $url->getPath()
        );

        $url->setPath('/abc//abc');
        $this->assertEquals(
            '/abc/abc',
            $url->getPath()
        );

        $url->setPath('/abc/abc/');
        $this->assertEquals(
            '/abc/abc/',
            $url->getPath()
        );
    }

    public function test_query(): void
    {
        $url = new ObjectUrl('https://www.example.com/path?#fragment');

        //

        $url->setQuery('a[]=1&a[]=2&b[a]=1&b[b]=2', true)
            ->removeQuery(['c', 'd']);
        $this->assertEquals(
            'a[]=1&a[]=2&b[a]=1&b[b]=2',
            $url->getQueryString()
        );

        //

        $url->setQuery('a=b', null);
        $this->assertEquals(
            'a=b',
            $url->getQueryString()
        );

        $url->setQuery('a=1&c=d', false);
        $this->assertEquals(
            'a=b&c=d',
            $url->getQueryString()
        );

        $url->setQuery('a=2&c=d&e=f', true);
        $this->assertEquals(
            'a=2&c=d&e=f',
            $url->getQueryString()
        );

        $url->setQuery('x=z&y=w&z=1', true)
            ->removeQuery('a');
        $this->assertEquals(
            'c=d&e=f&x=z&y=w&z=1',
            $url->getQueryString()
        );

        $url->removeQuery(['b', 'c.1']);
        $this->assertEquals(
            'c=d&e=f&x=z&y=w&z=1',
            $url->getQueryString()
        );

        $url->setQuery('a[]=1&a[]=2&b[a]=1&b[b]=2', true)
            ->removeQuery(['c', 'e']);
        $this->assertEquals(
            'x=z&y=w&z=1&a[]=1&a[]=2&b[a]=1&b[b]=2',
            $url->getQueryString()
        );

        $url->setQuery('a[]=1&a[]=2&b[a]=1&b[b]=2', true)
            ->removeQuery(['a', 'b.a']);
        $this->assertEquals(
            'x=z&y=w&z=1&b[b]=2',
            $url->getQueryString()
        );

        //
        $url->setQuery([
            0 => [
                0 => 1,
                1 => 2,
                2 => [
                    3 => 4,
                ],
            ],
            1 => 2,
        ]);
        $this->assertEquals(
            '0[]=1&0[]=2&0[][3]=4&1=2',
            $url->getQueryString()
        );

        //
        $url->setQuery([
            0 => 1,
            1 => 2,
        ]);
        $this->assertEquals(
            '0=1&1=2',
            $url->getQueryString()
        );

        //
        $url->setQuery([
            'queryBlank' => '',
            'queryNull' => [
                0 => [
                    0 => null,
                ],
            ],
        ]);
        $this->assertEquals(
            'queryBlank=&queryNull[][]',
            $url->getQueryString()
        );

        //
        $url->setQuery('');
        $this->assertEquals(
            '',
            $url->getQueryString()
        );

        //
        $url->setQuery([
            'a' => 'b',
            'c' => [
                'd' => 'e',
            ],
            '1' => [
                '2' => [
                    '3' => '4',
                ],
            ],
            'bool1' => true,
            'bool0' => false,
            'blank' => '',
            'null' => null,
        ]);
        $this->assertEquals(
            'a=b&c[d]=e&1[2][3]=4&bool1=1&bool0=0&blank=&null',
            $url->getQueryString()
        );

        //
        $url->setQuery([
            'a=0' => 'a=0',
            'a 1' => 'a 1',
            'a 2' => null,
            'a 3' => [
                'a 4' => 'a 4',
                'a 5' => [
                    'a 6' => 'a 6',
                    'a 7' => null,
                ],
            ],
        ]);
        $this->assertEquals(
            'a%3D0=a%3D0&a+1=a+1&a+2&a+3[a+4]=a+4&a+3[a+5][a+6]=a+6&a+3[a+5][a+7]',
            $url->getQueryString()
        );
    }

    public function test_url(): void
    {
        $url = new ObjectUrl('https://www.example.com/path?#fragment');

        $url->setHost('www.яндекс.ru');
        $this->assertEquals(
            'https://www.xn--d1acpjx3f.ru/path#fragment',
            $url->getUrlIdn()
        );

        $this->assertEquals(
            'https://www.яндекс.ru/path#fragment',
            $url->getUrlHuman()
        );
    }

    public function test_auth(): void
    {
        $url = new ObjectUrl('https://www.example.com/path?#fragment');

        $this->assertNull(
            $url->getAuth()
        );

        $url = new ObjectUrl(
            'https://user:pass@www.example.com/path?#fragment',
            [
                'acceptAuth' => null,
            ],
        );
        $this->assertEquals(
            ['user', 'pass'],
            $url->getAuth()
        );

        $url = new ObjectUrl(
            'https://user@www.example.com/path?#fragment',
            [
                'acceptAuth' => true,
            ],
        );
        $this->assertEquals(
            ['user', null],
            $url->getAuth()
        );

        $this->assertException(function() {
            new ObjectUrl(
                'https://user@www.example.com/path?#fragment',
                [
                    'acceptAuth' => false,
                ],
            );
        }, Exception::class);
    }

    public function test_fragment(): void
    {
        new ObjectUrl('http://aliexpress.ru/fsd');
        new ObjectUrl('https://www.ozon.ru/context/detail/id/144037117/?gclsrc=aw.ds');

        $this->assertException(function() {
            new ObjectUrl('https://www.localhost/');
        }, Exception::class);

        $this->assertException(function() {
            new ObjectUrl('http://localhost/');
        }, Exception::class);

        $this->assertException(function() {
            new ObjectUrl('http://aliexpress.ru:80/fsd');
        }, Exception::class);

        $this->assertException(function() {
            new ObjectUrl('http://[2001:0db8:85a3:0000:0000:8a2e:0370:7334]/');
        }, Exception::class);

        $this->assertException(function() {
            new ObjectUrl('http://127.0.0.1/');
        }, Exception::class);

        $this->assertException(function() {
            new ObjectUrl('http://abc:abc@test.ru/');
        }, Exception::class);

        $this->assertException(function() {
            new ObjectUrl('http://abc@test.ru/');
        }, Exception::class);
    }

    public function test_is_scheme(): void
    {
        $url = new ObjectUrl('https://www.example.com/path?#fragment');
        $this->assertTrue(
            $url->isHttp()
        );

        $this->assertFalse(
            $url->isScheme(['ftp', 'ssh'])
        );

        $this->assertTrue(
            $url->isScheme(['http', 'ssh', 'https'])
        );

        $url = new ObjectUrl('ftp://www.example.com/path?#fragment', ['onlyHttp' => null]);
        $this->assertFalse(
            $url->isHttp()
        );

        $this->assertTrue(
            $url->isScheme(['ftp', 'ssh'])
        );

        $this->assertFalse(
            $url->isScheme(['http', 'ssh'])
        );
    }

    public function test_is_ip(): void
    {
        $url = new ObjectUrl('https://www.example.com/path?#fragment');

        $this->assertFalse(
            $url->isIp()
        );

        $url->setHost('127.0.0.1', ['acceptIp' => true]);
        $this->assertTrue(
            $url->isIp()
        );

        $this->assertTrue(
            $url->isIpV4()
        );

        $this->assertFalse(
            $url->isIpV6()
        );

        $url->setHost('[2001:0db8:85a3:0000:0000:8a2e:0370:7334]', ['acceptIp' => true]);
        $this->assertTrue(
            $url->isIp()
        );

        $this->assertFalse(
            $url->isIpV4()
        );

        $this->assertTrue(
            $url->isIpV6()
        );
    }
}
