<?php

namespace Tests\Feature\Http\Controllers\v1;

use App\Events\CompanyRequestRecieved;
use App\Models\User;
use App\Notifications\CompanyResultsReady;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CompanyResultTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_get_company_result()
    {
        Notification::fake();
        Event::fake();

        $user = User::factory()->create();
        $response = $this->postJson('v1/api/login', [
            'email' => $user->email,
            'password' => 'MySecre1'
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('token', $response->json('token'));

        $domain = 'facebook.com';
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $response->json('token'),
        ])->postJson('v1/api/company', [
            'company_domain' => $domain
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('company_results', [
            'domain' => $domain,
            'user_id' => $user->id,
            'job_finished' => true,
        ]);

        Notification::assertSentTo([$user], CompanyResultsReady::class);
        Event::assertDispatched(CompanyRequestRecieved::class);
    }

    /** @test */
    public function user_cant_get_company_result_without_domain()
    {
        $user = User::factory()->create();
        $response = $this->postJson('v1/api/login', [
            'email' => $user->email,
            'password' => 'MySecre1'
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('token', $response->json('token'));

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $response->json('token'),
        ])->postJson('v1/api/company');

        $response
            ->assertUnprocessable()
            ->assertInvalid('company_domain')
            ->assertJsonValidationErrorFor('company_domain')
            ->assertJsonValidationErrors(['company_domain' => 'The company domain field is required.']);
    }

    /** @test */
    public function user_cant_get_company_result_without_api_key()
    {
        $response = $this->postJson('v1/api/company');

        $response
            ->assertUnauthorized();
    }
}
