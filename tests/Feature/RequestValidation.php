<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RequestValidation extends TestCase
{
    public function testLoginSuccess()
    {
        $response = $this->post('/form/login', [
            "username" => "miftah",
            "password" => "miftah"
        ]);
        $response->assertStatus(200);
    }

    public function testLoginFailed()
    {
        $response = $this->post('/form/login', [
            "username" => 'Miftah',
            "password" => ''
        ]);
        $response->assertStatus(400);
    }
}
