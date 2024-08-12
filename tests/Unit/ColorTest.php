<?php

use One23\Helpers\Color;
use Tests\TestCase;

class ColorTest extends TestCase
{
    public function test_fromCMYK()
    {
        $this->assertEquals(
            '#330000',
            Color::fromCMYK([0, 100, 100, 80])->toHex()
        );

        $this->assertEquals(
            '#19142E',
            Color::fromCMYK([46, 57, 0, 82])->toHex()
        );
    }

    public function test_toCMYK()
    {
        $this->assertEquals(
            ['c' => 0, 'm' => 100, 'y' => 100, 'k' => 0],
            Color::fromHex('#ff0000')->toCMYK()
        );

        $this->assertEquals(
            ['c' => 0, 'm' => 100, 'y' => 100, 'k' => 80],
            Color::fromHex('#330000')->toCMYK()
        );
    }

    public function test_toHSL()
    {
        $this->assertEquals(
            'hsl(300.47, 100%, 50%)',
            Color::fromHex('#ff00fd')->toHSLString()
        );

        $this->assertEquals(
            'hsl(298.06, 98%, 51%)',
            Color::fromHex('#f506fd')->toHSLString()
        );

        $this->assertEquals(
            'hsl(300.88, 100%, 73%)',
            Color::fromHex('#ff76fd')->toHSLString()
        );

        $this->assertEquals(
            'hsl(313.85, 100%, 57%)',
            Color::fromHex('#ff22cc')->toHSLString()
        );

        $this->assertEquals(
            'hsl(60, 82%, 43%)',
            Color::fromRGB([200, 200, 20])->toHSLString()
        );
    }

    public function test_fromHex(): void
    {
        $this->assertEquals(
            '#FA00FA',
            Color::fromHex('FA00FA')->toString()
        );

        $this->assertEquals(
            '#FAFAFA00',
            Color::fromHex('FAFAFA00')->toString()
        );

        $this->assertEquals(
            '#FFAAFF',
            Color::fromHex('#FFAAFF')->toString()
        );

        $this->assertEquals(
            '#0000FF',
            Color::fromHex('#0000FF')->toString()
        );

        $this->assertEquals(
            '#0000FF',
            Color::fromHex('#00f')->toString()
        );

        $this->assertEquals(
            '#0000FF',
            Color::fromHex('00f')->toString()
        );
    }

    public function test_toHex()
    {
        $this->assertEquals(
            '#FF0000',
            Color::fromRGB(['f', '0', '0'])->toHex()
        );

        $this->assertEquals(
            '#00FFFF',
            Color::fromRGB(['00', 'ff', 'ff'])->toHex()
        );

        $this->assertEquals(
            '#FAFAFA',
            Color::fromRGB([250, 250, 250])->toHex()
        );

        $this->assertEquals(
            '#FAFAFA',
            Color::fromRGB(250, 250, 250, 0)->toHex()
        );

        $this->assertEquals(
            '#FAFAFAFF',
            Color::fromRGB(250, 250, 250)->toHex(true)
        );

        $this->assertEquals(
            '#FAFAFA00',
            Color::fromRGB([250, 250, 250, 0])->toHex(true)
        );
    }

    public function test_toRGB()
    {
        $this->assertEquals(
            'rgba(255, 0, 255, 0%)',
            Color::fromRGB(['f', '0', 'f', '00'])->toRGBString()
        );

        $this->assertEquals(
            'rgb(255, 0, 255)',
            Color::fromRGB(['f', '0', 'f', 'ff'])->toRGBString()
        );

        $this->assertEquals(
            ['r' => 0, 'g' => 255, 'b' => 255, 'a' => 100],
            Color::fromRGB(['00', 'ff', 'ff', 'ff'])->toRGBA()
        );

        $this->assertEquals(
            ['r' => 255, 'g' => 0, 'b' => 255, 'a' => 0],
            Color::fromRGB(['f', '0', 'f', '00'])->toRGBA()
        );

        $this->assertEquals(
            ['r' => 250, 'g' => 250, 'b' => 250],
            Color::fromRGB([250, 250, 250])->toRGB()
        );

        $this->assertEquals(
            ['r' => 250, 'g' => 250, 'b' => 250],
            Color::fromRGB([250, 250, 250, 0])->toRGB()
        );

        $this->assertEquals(
            ['r' => 250, 'g' => 250, 'b' => 250, 'a' => 100],
            Color::fromRGB([250, 250, 250])->toRGBA()
        );

        $this->assertEquals(
            ['r' => 250, 'g' => 250, 'b' => 250, 'a' => 0],
            Color::fromRGB([250, 250, 250, 0])->toRGBA()
        );
    }

    public function test_fromRGB(): void
    {
        $this->assertEquals(
            '#FAFAFA',
            Color::fromRGB([250, 250, 250])->toString()
        );

        $this->assertEquals(
            '#FAFAFA00',
            Color::fromRGB([250, 250, 250, 0])->toString()
        );

        $this->assertEquals(
            '#FFFFFF',
            Color::fromRGB(255, 255, 255)->toString()
        );

        $this->assertEquals(
            '#000000',
            Color::fromRGB(0, 0, 0)->toString()
        );

        $this->assertEquals(
            '#00000000',
            Color::fromRGB(0, 0, 0, 0)->toString()
        );

        $this->assertEquals(
            '#000000FF',
            Color::fromRGB(0, 0, 0, 100)->toString()
        );
    }
}
