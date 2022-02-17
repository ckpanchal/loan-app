<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use JWTAuth;
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

    public function test_user_profile_api_works_successfully()
    {
        $this->withoutExceptionHandling();
        $user = User::where(['email' => 'user@example.com'])->first(); // User
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', route('profile'));
        $response->assertStatus(200);
        $decodeResponse = $response->decodeResponseJson();
        $this->assertEquals(true, $decodeResponse['status']);
    }

    public function test_refresh_token_api_works_successfully()
    {
        $this->withoutExceptionHandling();
        $user = User::where(['email' => 'user@example.com'])->first(); // User
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', route('refresh'));
        $response->assertStatus(200);
        $decodeResponse = $response->decodeResponseJson();
        $this->assertEquals(true, $decodeResponse['status']);
    }

    public function test_logout_api_works_successfully()
    {
        $this->withoutExceptionHandling();
        $user = User::where(['email' => 'user@example.com'])->first(); // User
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', route('logout'));
        $response->assertStatus(200);
        $decodeResponse = $response->decodeResponseJson();
        $this->assertEquals(true, $decodeResponse['status']);
    }

    public function test_access_token_not_working_after_logout()
    {
        $this->withoutExceptionHandling();
        $user = User::where(['email' => 'user@example.com'])->first(); // User
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('POST', route('logout'));
        $response->assertStatus(200);

        // Access profile api
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('GET', route('profile'));
        $response->assertStatus(401);
        $decodeResponse = $response->decodeResponseJson();
        $this->assertEquals(false, $decodeResponse['status']);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
