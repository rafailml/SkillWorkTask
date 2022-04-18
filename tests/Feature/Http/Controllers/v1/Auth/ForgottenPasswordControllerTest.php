<?php

namespace Tests\Feature\Http\Controllers\v1\Auth;

use App\Models\User;
use App\Notifications\ForgottenPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgottenPasswordControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_reset_own_password()
    {
        Notification::fake();

        $email = $this->faker()->email();
        $user = User::factory()->create(['email' => $email]);

        $response = $this->postJson('v1/api/forgotten-password', [
            'email' => $email
        ]);

        Notification::assertSentTo([$user], ForgottenPassword::class);

        $response
            ->assertCreated();

        $this->assertDatabaseHas('password_resets', ['email' => $email]);
    }

    /** @test */
    public function user_cant_reset_password_when_email_not_exists()
    {
        $response = $this->postJson('v1/api/forgotten-password', [
            'email' => $this->faker()->email()
        ]);

        $response
            ->assertUnprocessable()
            ->assertInvalid('email')
            ->assertJsonValidationErrorFor('email')
            ->assertJsonValidationErrors(['email' => 'The selected email is invalid.']);
    }
}
