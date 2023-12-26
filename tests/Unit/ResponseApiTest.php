<?php

use One23\Helpers\ResponseApi;
use PHPUnit\Framework\TestCase;

class ResponseApiTest extends TestCase
{
    public function test_json_ok()
    {
        $res = ResponseApi::ok([
            'id' => 1,
            'name' => 'John',
            'age' => 30,
        ]);

        $this->assertEquals(
            '{"success":true,"result":{"id":1,"name":"John","age":30}}',
            $res->pretty(false)->toJson()
        );

        ResponseApi::setGlobalExtra(function() {
            return [
                'global' => 'extra',
            ];
        });

        $this->assertEquals(
            '{"success":true,"result":true,"global":"extra"}',
            ResponseApi::ok(true)->pretty(false)->toJson()
        );
    }

    public function test_json_error()
    {
        ResponseApi::setGlobalExtra(null);

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
            '{"success":false,"error":{"message":"Error message","code":100,"fields":{"name":"Name is required","age":"Age must be greater than 0"}}}',
            (string)$res->pretty(false)->toJson()
        );

        $res = ResponseApi::error(
            'Error message',
            100,
        );

        $this->assertEquals(
            '{"success":false,"error":{"message":"Error message","code":100,"fields":[]}}',
            $res->pretty(false)->toJson()
        );

        $this->assertEquals(
            '{"success":false,"error":{"message":"Error message","code":100,"fields":[],"haha":123}}',
            ResponseApi::error(
                'Error message',
                100,
                [
                    'haha' => 123,
                ]
            )
                ->pretty(false)->toJson()
        );
    }

    public function test_json_raw()
    {
        ResponseApi::setGlobalExtra(null);

        $this->assertEquals(
            '1',
            ResponseApi::raw(1)->pretty(false)->toArray()['result']
        );

        $this->assertEquals(
            '1',
            ResponseApi::raw(1)->pretty(false)->toJson()
        );

        $this->assertEquals(
            'true',
            ResponseApi::raw(true)->pretty(false)->toJson()
        );

        $this->assertEquals(
            'null',
            ResponseApi::raw(null)->pretty(false)->toJson()
        );

        $this->assertEquals(
            '{"id":1,"name":"John","age":30}',
            ResponseApi::raw([
                'id' => 1,
                'name' => 'John',
                'age' => 30,
            ])->pretty(false)->toJson()
        );
    }

    public function test_json_exception()
    {
        ResponseApi::setGlobalExtra(null);

        $this->assertEquals(
            '{"success":false,"error":{"message":"Error message","code":100,"exception":{"message":"Error message","code":100}}}',
            ResponseApi::exception(
                new Exception('Error message', 100),
            )->pretty(false)->toJson()
        );

        $this->assertEquals(
            '{"success":false,"error":{"message":"Error message","code":100,"exception":{"message":"Error message","code":100},"exception_previous":{"message":"Previous message","code":200}}}',
            ResponseApi::exception(
                new Exception(
                    'Error message', 100,
                    new Exception('Previous message', 200),
                ),
            )->pretty(false)->toJson()
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
            )->pretty(false)->toJson()
        );
    }
}
