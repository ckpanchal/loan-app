<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_registered_successfully()
    {
        $this->withoutExceptionHandling();
        $formData = [
            'name' => 'Chintan Panchal',
            'email' => 'ckpanchal222@gmail.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ];
        $response = $this->json('POST', route('register'), $formData);
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'status', 'message', 'user'
                ]);
    }

    public function test_user_loggedin_successfully()
    {
        $this->withoutExceptionHandling();
        $formData = [
            'email' => 'user@example.com',
            'password' => 'secret',
        ];
        $response = $this->json('POST', route('login'), $formData);
        $response->assertStatus(200)->assertJsonStructure([
            'status','access_token', 'token_type', 'expires_in'
        ]);
    }

    public function test_user_not_loggedin_with_invalid_credentials()
    {
        $this->withoutExceptionHandling();
        $formData = [
            'email' => 'another-user@example.com',
            'password' => 'secret',
        ];
        $response = $this->json('POST', route('login'), $formData);
        $response->assertStatus(401);
    }  

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
