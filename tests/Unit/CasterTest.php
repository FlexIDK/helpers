<?php

use One23\Helpers\Caster;
use One23\Helpers\Enums;
use PHPUnit\Framework\TestCase;

class CasterTest extends TestCase
{
    protected function faker(): Faker\Generator
    {
        return \Faker\Factory::create();
    }

    protected function config(): array
    {
        return include __DIR__ . '/../../storage/data/caster.php';
    }

    protected function data(): array
    {
        return [
            'id' => (string)$this->faker()->randomNumber(),
            'type_ids' => [0, '1', '2', '3', 2, 4, -1],
            'tags' => [
                1,
                'test',
                null,
                ...$this->faker()->words(mt_rand(2, 5), false),
                'test',
            ],
            'user_ids' => ['1', '2', '3', 2, 4],
            'last_ids' => [0, -2, 4, 1, 2, 3, null, 5, 6, null, 8, 9, 0],
            'incoming' => ['1.1', '2.2', '3.3', '4.4', 5.5, 2.2, '-123.23'],
            'active' => $this->faker()->shuffleArray([1, 0, '1', '2', true, false, 'true', 'false'])[0],
            'name' => $this->faker()->name(),
            'rating' => (string)$this->faker()->randomFloat(),

            'bool1' => $this->faker()->shuffleArray([3, 0])[0],
            'bool2' => $this->faker()->shuffleArray(['2', '0'])[0],
            'bool3' => $this->faker()->shuffleArray([true, false])[0],
            'bool4' => $this->faker()->shuffleArray(['true', 'false'])[0],
            'bool5' => null,

            'created_at' => (mt_rand(0, 1)
                ? time() - mt_rand(0, 1000000)
                : null),

            'timestamp' => time(),

            'updated_at' => $this->faker()->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
            'birthday' => $this->faker()->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),

            'deleted_at' => $this->faker()->dateTime()->format('Y-m-d H:i:s'),

            'test' => 'test',
        ];
    }

    public function test_caster(): void
    {
        $data = $this->data();
        $config = $this->config();

        $res = Caster::config($data, $config);

        $this->assertIsArray($res);

        // assertArrayHasKey
        foreach ([
            'id', 'type_ids', 'tags', 'user_ids', 'last_ids', 'active', 'incoming', 'name', 'rating', 'created_at', 'updated_at', 'birthday', 'deleted_at', 'test',
            'timestamp',
            'bool1', 'bool2', 'bool3', 'bool4', 'bool5',
        ] as $key) {
            $this->assertArrayHasKey($key, $res);
        }

        // assertIsArray
        foreach ([
            'type_ids', 'tags', 'user_ids', 'last_ids', 'incoming',
        ] as $key) {
            $this->assertIsArray($res[$key]);
        }

        // assertIsInt
        foreach ([
            'id',
        ] as $key) {
            $this->assertIsInt($res[$key]);
        }

        // assertIsFloat
        foreach ([
            'rating',
        ] as $key) {
            $this->assertIsFloat($res[$key]);
        }

        // assertIsBool
        foreach ([
            'active',
            'bool1', 'bool2', 'bool3', 'bool4',
        ] as $key) {
            $this->assertIsBool($res[$key]);
        }

        // assertIsString
        foreach ([
            'name',
            'timestamp', 'updated_at', 'birthday', 'test',
        ] as $key) {
            $this->assertIsString($res[$key]);
        }

        // assertIsNull
        foreach ([
            'bool5',
        ] as $key) {
            $this->assertNull($res[$key]);
        }

        $this->assertTrue(
            $res['deleted_at'] instanceof \Carbon\CarbonInterface
        );
    }

    public function test_caster_only(): void
    {
        $data = $this->data();
        $config = $this->config();

        $res = Caster::config($data, $config, true);

        $this->assertArrayNotHasKey('test', $res);
    }

    public function test_val(): void
    {
        $this->assertNull(Caster::val(['123'], Enums\CasterType::Int));
        $this->assertIsInt(Caster::val(1, Enums\CasterType::Int));
        $this->assertNull(Caster::val('a', Enums\CasterType::Int));
        $this->assertNull(Caster::val(null, Enums\CasterType::Int));
        $this->assertIsInt(Caster::val(2.2, Enums\CasterType::Int));

        $this->assertIsFloat(Caster::val(1, Enums\CasterType::Float));
        $this->assertNull(Caster::val('a', Enums\CasterType::Float));
        $this->assertNull(Caster::val(null, Enums\CasterType::Float));
        $this->assertNull(Caster::val(['123'], Enums\CasterType::Float));
        $this->assertIsFloat(Caster::val(2.2, Enums\CasterType::Float));

        $this->assertIsString(Caster::val(1, Enums\CasterType::Str));
        $this->assertIsString(Caster::val('a', Enums\CasterType::Str));
        $this->assertIsNotString(Caster::val(null, Enums\CasterType::Str));
        $this->assertIsNotString(Caster::val(['123'], Enums\CasterType::Str));
        $this->assertIsString(Caster::val(2.2, Enums\CasterType::Str));

        $this->assertIsBool(Caster::val(2, Enums\CasterType::Boolean));
        $this->assertIsBool(Caster::val('3', Enums\CasterType::Boolean));
        $this->assertIsBool(Caster::val(0, Enums\CasterType::Boolean));
        $this->assertIsBool(Caster::val('0', Enums\CasterType::Boolean));
        $this->assertIsBool(Caster::val('true', Enums\CasterType::Boolean));
        $this->assertIsBool(Caster::val(true, Enums\CasterType::Boolean));
        $this->assertIsBool(Caster::val(false, Enums\CasterType::Boolean));
        $this->assertIsNotBool(Caster::val(null, Enums\CasterType::Boolean));

        $this->assertIsString(Caster::val(time(), Enums\CasterType::Date));
        $this->assertIsString(Caster::val(time(), Enums\CasterType::Datetime));
        $this->assertIsString(Caster::val(date('Y-m-d'), Enums\CasterType::Date));
        $this->assertIsString(Caster::val('today', Enums\CasterType::Datetime));

        $this->assertIsArray(Caster::val(['1', '2', '3'], Enums\CasterType::Arr));
        $this->assertIsArray(Caster::val(function() {
            return ['1', '2', '3'];
        }, Enums\CasterType::Arr));

        $this->assertEquals(
            'abc',
            Caster::val(' abc ', Enums\CasterType::Str, [Enums\CasterFilter::Trim]),
        );
        $this->assertEquals(
            'abc',
            Caster::val('AbC', Enums\CasterType::Str, [Enums\CasterFilter::Lower]),
        );
        $this->assertEquals(
            'ABC',
            Caster::val(' abc ', Enums\CasterType::Str, [Enums\CasterFilter::Trim, Enums\CasterFilter::Upper]),
        );

        $this->assertEquals(
            null,
            Caster::val('0', Enums\CasterType::Int, [Enums\CasterFilter::Gt0]),
        );

        $this->assertEquals(
            2,
            Caster::val('2', Enums\CasterType::Int, [Enums\CasterFilter::Gt0]),
        );

        $this->assertEquals(
            2,
            Caster::val('2', Enums\CasterType::Int, [Enums\CasterFilter::Gte0]),
        );

        $this->assertEquals(
            0,
            Caster::val('0', Enums\CasterType::Int, [Enums\CasterFilter::Gte0]),
        );

        $this->assertEquals(
            null,
            Caster::val('-1', Enums\CasterType::Int, [Enums\CasterFilter::Gte0]),
        );

        $this->assertEquals(
            [null, 1, 2, 3],
            Caster::val(['a', 1, '2', 3], Enums\CasterType::ArrOfInt, [Enums\CasterFilter::ArrUniqueInt]),
        );

        $this->assertEquals(
            [1, 2, 3, 4],
            Caster::val([1, 2, 3, 1, 4.22], Enums\CasterType::ArrOfInt, [Enums\CasterFilter::ArrUniqueInt, Enums\CasterFilter::ArrValues]),
        );

        $this->assertEquals(
            [1, 2, 3, 4 => 4],
            Caster::val([1, 2, 3, 1, 4.22], Enums\CasterType::ArrOfInt, [Enums\CasterFilter::ArrUniqueInt]),
        );

        $this->assertEquals(
            [1, 2, 3],
            Caster::val([1, null, 2, null, 3], Enums\CasterType::ArrOfInt, [Enums\CasterFilter::ArrNotNull, Enums\CasterFilter::ArrValues]),
        );

        $this->assertEquals(
            [1, 2 => 2, 4 => 3],
            Caster::val([1, null, 2, null, 3], Enums\CasterType::ArrOfInt, [Enums\CasterFilter::ArrNotNull]),
        );

        $this->assertEquals(
            ['a', 'b', 'c', 4 => 'd'],
            Caster::val(['a', 'b', 'c', 'a', 'd'], Enums\CasterType::ArrOfStr, [Enums\CasterFilter::ArrUniqueStr]),
        );

        $this->assertEquals(
            ['a', 'b', 'c', 'd'],
            Caster::val(['a', 'b', 'c', 'a', 'd'], Enums\CasterType::ArrOfStr, [Enums\CasterFilter::ArrUniqueStr, Enums\CasterFilter::ArrValues]),
        );
    }
}
