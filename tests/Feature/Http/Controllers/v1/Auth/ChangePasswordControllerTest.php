<?php

namespace Tests\Feature\Http\Controllers\v1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ChangePasswordControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_change_password()
    {
        $email = $this->faker()->email();
        $user = User::factory()->create(['email' => $email]);
        $passwordResetToken = Password::createToken($user);
        $newPassword = 'MySecre2';

        $response = $this->postJson('v1/api/change-password', [
            'email' => $email,
            'token' => $passwordResetToken,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response
            ->assertOk();
        $this->assertDatabaseMissing('password_resets', ['email' => $email]);

        Auth::attempt(['email' => $email, 'password' => $newPassword]);
        $this->assertEquals(Auth::user()->email, $email);
    }

    /** @test */
    public function user_cant_change_password_with_wrong_email()
    {
        $response = $this->postJson('v1/api/change-password', [
            'email' => 'dummy_email@emailzzz.com',
            'token' => 'dummy_token',
            'password' => 'MySecre1',
            'password_confirmation' => 'MySecre1',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('email')
            ->assertJsonValidationErrorFor('email')
            ->assertJsonValidationErrors(['email' => 'The selected email is invalid.']);
    }

    /** @test */
    public function user_cant_change_password_with_wrong_token()
    {
        $email = $this->faker()->email();
        $user = User::factory()->create(['email' => $email]);
        Password::createToken($user);

        $response = $this->postJson('v1/api/change-password', [
            'email' => $email,
            'token' => '0000000000000000000000000000000000000000000000000000000000000000',
            'password' => 'MySecre1',
            'password_confirmation' => 'MySecre1',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Invalid token.');
    }

    /** @test */
    public function user_cant_change_password_with_invalid_token()
    {
        $response = $this->postJson('v1/api/change-password', [
            'email' => '',
            'token' => 'dummy_token',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('token')
            ->assertJsonValidationErrorFor('token')
            ->assertJsonValidationErrors(['token' => 'The token must be 64 characters.']);
    }

    /** @test */
    public function user_cant_change_password_without_email()
    {
        $response = $this->postJson('v1/api/change-password', [
            'token' => 'dummy_token',
            'password' => 'MySecre1',
            'password_confirmation' => 'MySecre1',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('email')
            ->assertJsonValidationErrorFor('email')
            ->assertJsonValidationErrors(['email' => 'The email field is required.']);
    }

    /** @test */
    public function user_cant_change_password_without_token()
    {
        $response = $this->postJson('v1/api/change-password', [
            'email' => $this->faker()->email(),
            'password' => 'MySecre1',
            'password_confirmation' => 'MySecre1',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('token')
            ->assertJsonValidationErrorFor('token')
            ->assertJsonValidationErrors(['token' => 'The token field is required.']);
    }

    /** @test */
    public function user_cant_change_password_without_password()
    {
        $response = $this->postJson('v1/api/change-password', [
            'email' => $this->faker()->email(),
            'token' => 'dummy_token',
            'password_confirmation' => 'MySecre2',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('password')
            ->assertJsonValidationErrorFor('password')
            ->assertJsonValidationErrors(['password' => 'The password field is required.']);
    }

    /** @test */
    public function user_cant_change_password_without_password_confirmation()
    {
        $response = $this->postJson('v1/api/change-password', [
            'email' => $this->faker()->email(),
            'token' => 'dummy_token',
            'password' => 'MySecre1',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('password_confirmation')
            ->assertJsonValidationErrorFor('password_confirmation')
            ->assertJsonValidationErrors(['password_confirmation' => 'The password confirmation field is required.']);
    }

    /** @test */
    public function user_cant_change_password_when_new_password_does_not_match()
    {
        $response = $this->postJson('v1/api/change-password', [
            'email' => $this->faker()->email(),
            'token' => 'dummy_token',
            'password' => 'MySecre1',
            'password_confirmation' => 'MySecre2',
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('password')
            ->assertJsonValidationErrorFor('password')
            ->assertJsonValidationErrors(['email' => 'The selected email is invalid.']);
    }
}
