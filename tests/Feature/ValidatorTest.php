<?php

namespace Tests\Feature;

use App\Rules\RegistrationRule;
use App\Rules\Uppercase;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\Password;
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

    public function testLocalization()
    {
        App::setLocale("id");
        $data = [
            "username" => "admin",
            "password" => "test"
        ];

        $rules = [
            "username" => "required|email|max:100",
            "password" => "required|min:6|max:20"
        ];

        $validator = Validator::make($data, $rules);
        self::assertTrue($validator->fails());
        Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidatorInlineMessage()
    {
        $data = [
            "username" => "eko",
            "password" => "eko"
        ];

        $rules = [
            "username" => "required|email|max:100",
            "password" => ["required", "min:6", "max:20"]
        ];

        $messages = [
            "required" => ":attribute harus diisi",
            "email" => ":attribute harus berupa email",
            "min" => ":attribute minimal :min karakter",
            "max" => ":attribute maksimal :max karakter",
        ];

        $validator = Validator::make($data, $rules, $messages);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();

        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidatorAdditionalValidation()
    {
        $data = [
            "username" => "eko",
            "password" => "eko"
        ];

        $rules = [
            "username" => "required|email|max:100",
            "password" => ["required", "min:6", "max:20"]
        ];

        $validator = Validator::make($data, $rules);
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $data = $validator->getData();
            if ($data['username'] == $data['password']) {
                $validator->errors()->add("password", "Password tidak boleh sama dengan username");
            }
        });

        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidatorCustomRule()
    {
        $data = [
            "username" => "eko@pzn.com",
            "password" => "eko@pzn.com"
        ];

        $rules = [
            "username" => ["required", "email", "max:100", new Uppercase()],
            "password" => ["required", "min:6", "max:20", new RegistrationRule()]
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();

        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidatorCustomFunctionRule()
    {
        $data = [
            "username" => "eko@pzn.com",
            "password" => "eko@pzn.com"
        ];

        $rules = [
            "username" => ["required", "email", "max:100", function (string $attributes, string $value, Closure $fail) {
                if (strtoupper($value) != $value) {
                    $fail("The $attributes must be uppercase");
                }
            }],
            "password" => ["required", "min:6", "max:20", new RegistrationRule()]
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->fails());
        Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidatorRuleClass()
    {
        $data = [
            "username" => "miftahfadilah71@gmail.com",
            "password" => "miftahfadilah71@gmail.com"
        ];

        $rules = [
            "username" => ["required", new In(["Miftah", "Fadilah", "71"])],
            "password" => ["required", Password::min(6)->letters()->numbers()->symbols()]
        ];

        $validator = Validator::make($data, $rules);

        self::assertTrue($validator->fails());
        Log::info($validator->errors()->toJson(JSON_PRETTY_PRINT));
    }

    public function testValidatorNestedArrayValidation()
    {
        $data = [
            "name" => [
                "first" => "Miftah",
                "last" => "Fadilah"
            ],
            "address" => [
                "street" => "Jl. Dramaga",
                "city" => "Bogor",
                "country" => "Indonesia"
            ]
        ];

        $rules = [
            "name.first" => ["required", "max:100"],
            "name.last" => ["max:100"],
            "address.street" => ["max:200"],
            "address.city" => ["required", "max:100"],
            "address.country" => ["required", "max:100"]
        ];

        $validator = Validator::make($data, $rules);
        self::assertTrue($validator->passes());
    }

    public function testValidatorIndexedArrayValidation()
    {
        $data = [
            "name" => [
                "first" => "Miftah",
                "last" => "Fadilah"
            ],
            "address" => [
                [
                    "street" => "Jl. Dramaga",
                    "city" => "Bogor",
                    "country" => "Indonesia"
                ],
                [
                    "street" => "Jl. Dramaga",
                    "city" => "Bogor",
                    "country" => "Indonesia"
                ]
            ]
        ];

        $rules = [
            "name.first" => ["required", "max:100"],
            "name.last" => ["max:100"],
            "address.*.street" => ["max:200"],
            "address.*.city" => ["required", "max:100"],
            "address.*.country" => ["required", "max:100"]
        ];

        $validator = Validator::make($data, $rules);
        self::assertTrue($validator->passes());
    }
}
