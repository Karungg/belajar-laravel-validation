<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FormControllerTest extends TestCase
{
    public function testFormFailed()
    {
        $response = $this->post('/form', [
            'username' => '',
            'password' => ''
        ]);
        $response->assertStatus(302);
    }

    public function testFormSuccess()
    {
        $response = $this->post('/form', [
            'username' => 'admin@gmail.com',
            'password' => '#rahasia123'
        ]);
        $response->assertStatus(200);
    }
}
