<?php

use One23\Helpers\Random;

class RandomTest extends \Tests\TestCase
{
    public function test__constructor()
    {
        $this->assertEquals(
            16,
            strlen(
                (new Random('1234567890abcdef'))->generation(16)
            )
        );

        $this->assertMatchesRegularExpression(
            '/^[0-3a-c]{16}$/',
            (new Random('abc123'))->generation(16)
        );
    }

    public function test_byte(): void
    {
        $this->assertEquals(
            16,
            strlen(
                Random::byte(16)
            )
        );

        $this->assertEquals(
            24,
            strlen(
                Random::byte(24)
            )
        );

        $this->assertEquals(
            32,
            strlen(
                Random::byte(32)
            )
        );
    }

    public function test_alpha(): void
    {
        $this->assertEquals(
            4,
            mb_strlen(
                Random::alpha(4, null)
            )
        );

        $this->assertMatchesRegularExpression(
            '/^[a-z]{8}$/',
            Random::alpha(8, false)
        );

        $this->assertEquals(
            16,
            strlen(
                Random::alpha(16, true)
            )
        );

        $this->assertMatchesRegularExpression(
            '/^[A-Z]{24}$/',
            Random::alpha(24, true)
        );

        $this->assertEquals(
            32,
            strlen(
                Random::alpha(32, false)
            )
        );

        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z]{48}$/',
            Random::alpha(48, null)
        );
    }

    public function test_base58(): void
    {
        $this->assertEquals(
            16,
            strlen(
                Random::base58(16)
            )
        );

        $this->assertMatchesRegularExpression(
            '/^[1-9A-HJ-NP-Za-km-z]{16}$/',
            Random::base58(16)
        );
    }

    public function test_base64(): void
    {
        $this->assertEquals(
            16,
            strlen(
                Random::base64(16)
            )
        );

        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9]{16}$/',
            Random::base64(16)
        );
    }

    public function test_digital()
    {
        $this->assertEquals(
            16,
            strlen(
                Random::digital(16)
            )
        );

        $this->assertMatchesRegularExpression(
            '/^[0-9]{16}$/',
            Random::digital(16)
        );
    }

    public function test_alpha_digital(): void
    {
        $this->assertEquals(
            16,
            strlen(
                Random::alphaDigital(16, false)
            )
        );

        $this->assertMatchesRegularExpression(
            '/^[a-z0-9]{16}$/',
            Random::alphaDigital(16, false)
        );

        //
        $this->assertEquals(
            24,
            strlen(
                Random::alphaDigital(24, true)
            )
        );

        $this->assertMatchesRegularExpression(
            '/^[A-Z0-9]{32}$/',
            Random::alphaDigital(32, true)
        );

        //
        $this->assertEquals(
            8,
            strlen(
                Random::alphaDigital(8, null)
            )
        );

        $this->assertMatchesRegularExpression(
            '/^[a-zA-Z0-9]{16}$/',
            Random::alphaDigital(16, null)
        );
    }
}
