<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
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
}
