<?php

use One23\Helpers\Exceptions\Version as Exception;
use One23\Helpers\Version;

class VersionTest extends \Tests\TestCase
{
    public function test_compare()
    {
        $this->assertEquals(
            1,
            Version::compare('1.2.3', '1.2.3-alpha')
        );

        $this->assertEquals(
            -1,
            Version::compare('1.2.3-beta', 'v1.2.3.rc')
        );

        $this->assertEquals(
            0,
            Version::compare('1.2.3', 'v1.2.3.0')
        );

        $this->assertEquals(
            1,
            Version::compare('2.2.3', 'v1.2.3.0')
        );

        $this->assertEquals(
            1,
            Version::compare('2.2.3.1', 'v2.2.3.1.rc')
        );

        $this->assertEquals(
            -1,
            Version::compare('2.2.3.1', 'v2.2.3.1.1.rc')
        );

        $this->assertEquals(
            1,
            Version::compare('2.2.13', 'v2.2.3')
        );

        $this->assertEquals(
            -1,
            Version::compare('v0.1.02', '0.1.13')
        );
    }

    public function test_to_string()
    {
        $this->assertException(function() {
            Version::object('abc');
        }, Exception::class);

        $this->assertEquals(
            '1.2.3',
            (Version::object('1.2.3'))
                ->toString()
        );

        $this->assertEquals(
            '1.0.0.1',
            (Version::object('1.0.0.1'))
                ->toString()
        );

        $this->assertEquals(
            '1.0.0',
            (Version::object('1.0.0'))
                ->toString()
        );

        $this->assertEquals(
            '1.2.3',
            (Version::object('v1.2.3'))
                ->toString()
        );

        $this->assertEquals(
            '1.2.3-alpha',
            (Version::object('1.2.3-alpha'))
                ->toString()
        );

        $this->assertEquals(
            '1.2.3-alpha',
            (Version::object('1.2.3a'))
                ->toString()
        );

        $this->assertEquals(
            '1.2.3-beta',
            (Version::object('1.2.3b'))
                ->toString()
        );

        $this->assertEquals(
            '1.2.3-rc',
            (Version::object('1.2.3rc'))
                ->toString()
        );

        $this->assertEquals(
            '1.2.3-rc',
            (Version::object('1.2.3-rc'))
                ->toString()
        );

        $this->assertEquals(
            '1.2.3-rc.1',
            (Version::object('1.2.3rc.1'))
                ->toString()
        );

        $this->assertEquals(
            '1.2.3-alpha.1',
            (Version::object('1.2.a-3.1'))
                ->toString()
        );

        $this->assertEquals(
            '1.2.stable.1',
            (Version::object('1.2.0-stable.1'))
                ->toString()
        );

        $this->assertEquals(
            '1.2.rtm.1',
            (Version::object('1.2.rtm-0.1'))
                ->toString()
        );

        $this->assertEquals(
            '1.2.12-release.1',
            (Version::object('1.2.12-rel.1'))
                ->toString()
        );
    }
}
