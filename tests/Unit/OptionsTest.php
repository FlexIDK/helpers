<?php

use One23\Helpers\Exceptions\Options as Exception;
use One23\Helpers\Options;

class OptionsTest extends \Tests\TestCase
{
    protected static function options(): array
    {
        return [
            'defaultScheme' => [
                'nullable' => true,
                'type' => 'string',
                'min' => 1,
                'max' => 32,
                'default' => null,
            ],
            'allowWildcard' => [
                'nullable' => false,
                'type' => 'bool',
                'default' => false,
            ],
            'onlyHttp' => [
                'nullable' => true,
                'type' => 'bool',
                'default' => true,
            ],
            'minHostLevel' => [
                'nullable' => false,
                'type' => 'int',
                'min' => 2,
                'max' => 127,
                'default' => 2,
            ],
            'maxHostLevel' => [
                'nullable' => false,
                'type' => 'int',
                'min' => 1,
                'max' => 127,
                'default' => 127,
            ],
            'maxHostLength' => [
                'nullable' => false,
                'type' => 'int',
                'min' => 1,
                'max' => 255,
                'default' => 253,
            ],
            'acceptPort' => [
                'nullable' => true,
                'type' => 'bool',
                'default' => false,
            ],
            'acceptIp' => [
                'nullable' => true,
                'type' => 'bool',
                'default' => false,
            ],
            'acceptAuth' => [
                'nullable' => true,
                'type' => 'bool',
                'default' => false,
            ],
            'hostHuman' => [
                'nullable' => true,
                'type' => 'bool',
                'default' => false,
            ],
        ];
    }

    public function test_merge()
    {
        $this->assertEquals(
            [
                'a' => 1,
                'b' => 3,
                'c' => 4,
            ],
            Options::merge(
                [
                    'a' => 1,
                    'b' => 2,
                ],
                [
                    'b' => 3,
                    'c' => 3,
                ],
                [
                    'c' => 4,
                ]
            )
        );

        $this->assertEquals(
            [
                'a' => 1,
                'b' => 2,
                'c' => null,
            ],
            Options::merge(
                [
                    'a' => 1,
                    'b' => 2,
                ],
                null,
                [
                    'c' => null,
                ]
            )
        );
    }

    public function test_default()
    {
        $this->assertEquals(
            [],
            Options::default([])
        );

        $this->assertEquals(
            [
                'defaultScheme' => null,
                'allowWildcard' => false,
                'onlyHttp' => true,
                'minHostLevel' => 2,
                'maxHostLevel' => 127,
                'maxHostLength' => 253,
                'acceptPort' => false,
                'acceptIp' => false,
                'acceptAuth' => false,
                'hostHuman' => false,
            ],
            Options::default(static::options())
        );

        $this->assertEquals(
            [
                'a' => 1,
                'b' => 2,
                'c' => 3,
            ],
            Options::default([
                'a' => [
                    'default' => 1,
                ],
                'b' => [
                    'default' => 2,
                ],
                'c' => [
                    'default' => 3,
                ],
            ])
        );

        $this->assertEquals(
            [
                'a' => 1,
                'c' => 3,
            ],
            Options::default([
                'a' => [
                    'default' => 1,
                ],
                'b' => [
                    'nullable' => true,
                ],
                'c' => [
                    'default' => 3,
                ],
            ])
        );
    }

    public function test_one()
    {
        $this->assertException(function() {
            Options::one(
                null,
                [
                    'nullable' => false,
                    'type' => 'string',
                ]
            );
        });

        $this->assertException(function() {
            Options::one(
                '',
                [
                    'type' => 'string',
                    'min' => 1,
                ]
            );
        });

        $this->assertException(function() {
            Options::one(
                'http',
                [
                    'nullable' => false,
                    'type' => 'string',
                    'max' => 3,
                ]
            );
        });
    }

    public function test_all()
    {
        $this->assertEquals(
            [
                'defaultScheme' => 'http',
                'allowWildcard' => true,
                'onlyHttp' => true,
                'minHostLevel' => 2,
            ],
            Options::all(
                [
                    'defaultScheme' => 'http',
                    'allowWildcard' => true,
                    'onlyHttp' => true,
                    'minHostLevel' => 2,
                ],
                static::options()
            )
        );

        $this->assertEquals(
            [
                'defaultScheme' => 'http',
                'allowWildcard' => true,
                'onlyHttp' => true,
                'minHostLevel' => 2,
                'maxHostLevel' => 127,
                'maxHostLength' => 253,
                'acceptPort' => false,
                'acceptIp' => false,
                'acceptAuth' => false,
                'hostHuman' => false,
            ],
            Options::all(
                [
                    'defaultScheme' => 'http',
                    'allowWildcard' => true,
                    'onlyHttp' => true,
                    'minHostLevel' => 2,
                ],
                static::options(),
                true
            )
        );

        $this->assertException(function() {
            Options::all(
                ['defaultScheme' => true],
                static::options()
            );
        }, Exception::class);

        $this->assertException(function() {
            Options::all(
                ['allowWildcard' => 1],
                static::options()
            );
        }, Exception::class);

        $this->assertException(function() {
            Options::all(
                ['minHostLevel' => 'abc'],
                static::options()
            );
        }, Exception::class);

        $this->assertException(function() {
            Options::all(
                ['minHostLevel' => 1],
                static::options()
            );
        }, Exception::class);

        $this->assertException(function() {
            Options::all(
                ['maxHostLevel' => 128],
                static::options()
            );
        }, Exception::class);
    }
}
