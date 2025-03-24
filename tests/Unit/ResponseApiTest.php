<?php

use One23\Helpers\ResponseApi;
use Tests\TestCase;

class ResponseApiTest extends TestCase
{

    public function test_set_data(): void
    {
        $api = ResponseApi::ok([]);
        $api->isRaw(true);
        $api->setData(['a' => 1]);

        $this->assertEquals($api->toArray(), ['a'=>1]);

        $api->setData(['b' => 2], 'append');
        $this->assertEquals($api->toArray(), ['a'=>1, 'b' => 2]);

        $api->setData(['c' => 3], 'prepend');
        $this->assertEquals($api->toArray(), ['c'=>3, 'a'=>1, 'b' => 2]);

        $api->setData(['d'=>4]);
        $this->assertEquals($api->toArray(), ['d'=>4]);

        $api->setData(['e'=>5], 'replace');
        $this->assertEquals($api->toArray(), ['e'=>5]);

        //

        $api = ResponseApi::ok([]);
        $api->setData(['a' => 1]);

        $this->assertEquals($api->getData(), ['a'=>1]);
    }

    public function test_is_debug(): void
    {
        $api = ResponseApi::ok([]);

        ResponseApi::setGlobalDebug(true);

        $this->assertTrue($api
            ->toArray()['is_debug']);

        $this->assertFalse($api
            ->setDebug(false)
            ->toArray()['is_debug'] ?? false);

        //

        $api = ResponseApi::ok([]);
        ResponseApi::setGlobalDebug(false);

        $this->assertFalse($api
            ->toArray()['is_debug'] ?? false);

        $this->assertTrue($api
            ->setDebug(true)
            ->toArray()['is_debug']);
    }

    public function test_result_key(): void
    {
        $api = ResponseApi::ok([]);

        $this->assertArrayHasKey(
            'result',
            $api->toArray()
        );

        ResponseApi::setGlobalResultKey('key1');

        $this->assertArrayHasKey(
            'key1',
            $api->toArray()
        );

        $this->assertArrayHasKey(
            'key2',
            $api->setResultKey('key2')->toArray()
        );

        $this->assertArrayNotHasKey(
            'key1',
            $api->toArray()
        );

        $this->assertArrayNotHasKey(
            'result',
            $api->toArray()
        );

        ResponseApi::setGlobalResultKey('result');
    }

    public function test_global_extra()
    {
        $api = ResponseApi::ok([]);

        $key1 = ResponseApi::setGlobalExtra(function() {
            return [
                'global1' => 'extra',
            ];
        });

        $key2 = ResponseApi::setGlobalExtra(function() {
            return [
                'global2' => 'extra',
            ];
        });

        $this->assertArrayHasKey(
            'global1',
            $api->toArray()
        );

        $this->assertArrayHasKey(
            'global2',
            $api->toArray()
        );

        ResponseApi::removeGlobalExtra($key1);

        $this->assertArrayNotHasKey(
            'global1',
            $api->toArray()
        );

        $key3 = ResponseApi::setGlobalExtra(function() {
            return [
                'global3' => 'extra',
            ];
        });

        $this->assertArrayHasKey(
            'global3',
            $api->toArray()
        );

        ResponseApi::resetGlobalExtra();

        foreach (['global1', 'global2', 'global3'] as $key) {
            $this->assertArrayNotHasKey(
                $key,
                $api->toArray()
            );
        }
    }

    public function test_json_ok()
    {
        $res = ResponseApi::ok([
            'id' => 1,
            'name' => 'John',
            'age' => 30,
        ]);

        $this->assertEquals(
            '{"success":true,"result":{"id":1,"name":"John","age":30}}',
            $res->setPretty(false)->toJson()
        );

        ResponseApi::setGlobalExtra(function() {
            return [
                'global' => 'extra',
            ];
        });

        $this->assertEquals(
            '{"success":true,"result":true,"global":"extra"}',
            ResponseApi::ok(true)->setPretty(false)->toJson()
        );
    }

    public function test_json_error()
    {
        ResponseApi::resetGlobalExtra();

        $res = ResponseApi::error(
            'Error message',
            100,
            [
                'fields' => [
                    'name' => 'Name is required',
                    'age' => 'Age must be greater than 0',
                ],
            ]
        );

        $this->assertEquals(
            '{"success":false,"error":{"message":"Error message","code":100,"fields":{"name":"Name is required","age":"Age must be greater than 0"},"fields_keys":["name","age"]}}',
            (string)$res->setPretty(false)->toJson()
        );

        $res = ResponseApi::error(
            'Error message',
            100,
        );

        $this->assertEquals(
            '{"success":false,"error":{"message":"Error message","code":100,"fields":[],"fields_keys":[]}}',
            $res->setPretty(false)->toJson()
        );

        $this->assertEquals(
            '{"success":false,"error":{"message":"Error message","code":100,"fields":[],"haha":123,"fields_keys":[]}}',
            ResponseApi::error(
                'Error message',
                100,
                [
                    'haha' => 123,
                ]
            )
                ->setPretty(false)->toJson()
        );
    }

    public function test_json_raw()
    {
        ResponseApi::resetGlobalExtra();

        $this->assertEquals(
            '1',
            ResponseApi::raw(1)->setPretty(false)->toArray()['result']
        );

        $this->assertEquals(
            '1',
            ResponseApi::raw(1)->setPretty(false)->toJson()
        );

        $this->assertEquals(
            'true',
            ResponseApi::raw(true)->setPretty(false)->toJson()
        );

        $this->assertEquals(
            'null',
            ResponseApi::raw(null)->setPretty(false)->toJson()
        );

        $this->assertEquals(
            '{"id":1,"name":"John","age":30}',
            ResponseApi::raw([
                'id' => 1,
                'name' => 'John',
                'age' => 30,
            ])->setPretty(false)->toJson()
        );
    }

    public function test_json_exception()
    {
        ResponseApi::resetGlobalExtra();

        $this->assertEquals(
            '{"success":false,"error":{"message":"Error message","code":100,"exception":{"message":"Error message","code":100}}}',
            ResponseApi::exception(
                new Exception('Error message', 100),
            )->setPretty(false)->toJson()
        );

        $this->assertEquals(
            '{"success":false,"error":{"message":"Error message","code":100,"exception":{"message":"Error message","code":100},"exception_previous":{"message":"Previous message","code":200}}}',
            ResponseApi::exception(
                new Exception(
                    'Error message', 100,
                    new Exception('Previous message', 200),
                ),
            )->setPretty(false)->toJson()
        );

        $this->assertEquals(
            '{"success":false,"error":{"message":"Error","code":300,"exception":{"message":"Error message","code":100},"exception_previous":{"message":"Previous message","code":200}}}',
            ResponseApi::exception(
                new Exception(
                    'Error message', 100,
                    new Exception('Previous message', 200),
                ),
                'Error',
                300
            )->setPretty(false)->toJson()
        );
    }
}
