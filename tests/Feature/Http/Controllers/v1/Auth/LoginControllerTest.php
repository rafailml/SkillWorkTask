<?php

namespace Tests\Feature\Http\Controllers\v1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_log_in()
    {
        $user = User::factory()->create();

        $response = $this->postJson('v1/api/login', [
            'email' => $user->email,
            'password' => 'MySecre1'
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('name', $user->name)
            ->assertJsonPath('email', $user->email)
            ->assertJsonPath('token', $response->json('token'));

        $this->assertAuthenticated();
        $this->assertEquals($user->name, $response->json('name'));
        $this->assertEquals(64, strlen($response->json('token')));
    }

    /** @test */
    public function user_cant_login_with_wrong_password()
    {

        $user = User::factory()->create();

        $response = $this->postJson('v1/api/login', [
            "email" => $user->email,
            "password" => 'MySecre231',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('message', 'These credentials do not match our records.');
    }

    /** @test */
    public function user_cant_login_with_wrong_email()
    {

        $response = $this->postJson('v1/api/login', [
            "email" => $this->faker->email(),
            "password" => 'MySecre1',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('message', 'These credentials do not match our records.');
    }

    /** @test */
    public function user_cant_login_with_invalid_email()
    {

        $response = $this->postJson('v1/api/login', [
            "email" => "invalid_email",
            "password" => 'MySecre1',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('email')
            ->assertJsonValidationErrorFor('email')
            ->assertJsonValidationErrors(['email' => 'The email must be a valid email address.']);
    }

    /** @test */
    public function user_cant_login_without_email()
    {

        $response = $this->postJson('v1/api/login', [
            "password" => 'MySecre1',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('email')
            ->assertJsonValidationErrorFor('email')
            ->assertJsonPath('message', 'The email field is required.');
    }

    /** @test */
    public function user_cant_login_without_password()
    {

        $response = $this->postJson('v1/api/login', [
            "email" => $this->faker->email(),
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('password')
            ->assertJsonValidationErrorFor('password')
            ->assertJsonPath('message', 'The password field is required.');
    }

}
