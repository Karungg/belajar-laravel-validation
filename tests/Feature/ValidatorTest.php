<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ValidatorTest extends TestCase
{
    public function testValidator()
    {
        $data = [
            'username' => 'miftahfadilah71',
            'password' => 'miftah123'
        ];

        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->passes());
        self::assertFalse($validator->fails());
    }

    public function testErrorMessage()
    {
        $data = [
            'username' => 'miftahfadilah71',
            'password' => 'miftah123'
        ];

        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->passes());
        self::assertFalse($validator->fails());

        $message = $validator->getMessageBag();

        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidationException()
    {
        $data = [
            'username' => 'miftahfadilah71',
            'password' => ''
        ];

        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        $validator = Validator::make($data, $rules);

        try {
            $validator->validate();
            self::fail("ValidationException not thrown");
        } catch (ValidationException $exception) {
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::info($message->toJson(JSON_PRETTY_PRINT));
        }
    }

    public function testValidationRules()
    {
        $data = [
            "username" => "admin",
            "password" => "rahasia"
        ];

        $rules = [
            "username" => "required|email|max:100",
            "password" => ["required", "min:6", "max:20"]
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->fails());
        Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidData()
    {
        $data = [
            "username" => "admin@gmail.com",
            "password" => "rahasia"
        ];

        $rules = [
            "username" => "required|email|max:100",
            "password" => "required|min:6|max:20"
        ];

        $validator = Validator::make($data, $rules);

        try {
            $result = $validator->validate();
            self::assertNotNull($result);
            Log::info(json_encode($result, JSON_PRETTY_PRINT));
        } catch (ValidationException $exception) {
            self::fail($exception->getMessage());
        }
    }
}
