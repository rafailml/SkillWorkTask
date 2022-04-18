<?php

namespace Tests\Feature\Http\Controllers\v1\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_be_registered()
    {
        $name = $this->faker->name;
        $email = $this->faker->email;

        $response = $this->postJson('v1/api/sign-in', [
            'name' => $name,
            'email' => $email,
            'password' => 'MySecre1',
            'password_confirmation' => 'MySecre1'
        ]);

        $response
            ->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('users', ['email' => $email]);
        $this->assertEquals($name, $response->json('name'));
        $this->assertEquals(64, strlen($response->json('token')));

        $user = User::where('email', $email)->firstOrFail();
        $this->assertGreaterThan(Carbon::now()->addHours(11)->timestamp, $user->token_expiration);
    }

    /** @test */
    public function user_cant_register_without_name()
    {
        $response = $this->postJson('v1/api/sign-in', [
            "email" => $this->faker->email,
            "password" => 'Pa$$w0rd!',
            "password_confirmation" => 'Pa$$w0rd!',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('name')
            ->assertJsonValidationErrorFor('name')
            ->assertJsonValidationErrors(['name' => 'The name field is required.']);
    }

    /** @test */
    public function user_cant_register_without_email()
    {
        $response = $this->postJson('v1/api/sign-in', [
            "name" => $this->faker->name,
            "password" => 'Pa$$w0rd!',
            "password_confirmation" => 'Pa$$w0rd!',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('email')
            ->assertJsonValidationErrorFor('email')
            ->assertJsonValidationErrors(['email' => 'The email field is required.']);
    }

    /** @test */
    public function user_cant_register_without_password()
    {
        $response = $this->postJson('v1/api/sign-in', [
            "name" => $this->faker->name,
            "email" => $this->faker->email,
            "password_confirmation" => 'Pa$$w0rd!',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('password')
            ->assertJsonValidationErrorFor('password')
            ->assertJsonValidationErrors(['password' => 'The password field is required.']);
    }

    /** @test */
    public function user_cant_register_without_password_confirmation()
    {
        $response = $this->postJson('v1/api/sign-in', [
            "name" => $this->faker->name,
            "email" => $this->faker->email,
            "password" => 'Pa$$w0rd!',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('password')
            ->assertJsonValidationErrorFor('password')
            ->assertJsonValidationErrors(['password' => 'The password confirmation does not match.'])
            ->assertJsonValidationErrors(['password_confirmation' => 'The password confirmation field is required.']);
    }

    /** @test */
    public function user_cant_register_when_confirm_password_does_not_match_password()
    {
        $response = $this->postJson('v1/api/sign-in', [
            "name" => $this->faker->name,
            "email" => $this->faker->email,
            "password" => 'Pa$$w0rd!',
            "password_confirmation" => 'Pa$$w0rd!123',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('password')
            ->assertJsonValidationErrorFor('password')
            ->assertJsonValidationErrors(['password' => 'The password confirmation does not match.']);
    }

    /** @test */
    public function test_user_cant_register_if_email_exists()
    {
        $email = $this->faker->email;

        User::factory()->create(['email' => $email]);
        $this->assertDatabaseHas('users', ['email' => $email]);

        $response = $this->postJson('v1/api/sign-in', [
            "name" => $this->faker->name,
            "email" => $email,
            "password" => 'Pa$$w0rd!',
            "password_confirmation" => 'Pa$$w0rd!',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('email')
            ->assertJsonValidationErrorFor('email')
            ->assertJsonValidationErrors(['email' => 'The email has already been taken.']);
    }

}
