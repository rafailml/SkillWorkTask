<?php

namespace Tests\Feature\Http\Controllers\v1;

use App\Models\CompanyResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyResultJobStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_check_job_status()
    {
        $user = User::factory()->create();
        $response = $this->postJson('v1/api/login', [
            'email' => $user->email,
            'password' => 'MySecre1'
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('token', $response->json('token'));

        // Create record in company_results to check against
        $domain = 'facebook.com';
        CompanyResult::create([
            'user_id' => $user->id,
            'domain' => $domain,
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $response->json('token'),
        ])->getJson('v1/api/company-result-status?company_domain='.$domain );

        $response
            ->assertJsonPath('domain', $domain)
            ->assertJsonPath('user_id', (string)$user->id)
            ->assertJsonPath('job_finished', '0')
            ->assertOk();
    }

    /** @test */
    public function user_cant_check_job_status_without_domain()
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
        ])->getJson('v1/api/company-result-status');

        $response
            ->assertUnprocessable()
            ->assertInvalid('company_domain')
            ->assertJsonValidationErrorFor('company_domain')
            ->assertJsonValidationErrors(['company_domain' => 'The company domain field is required.']);
    }

    /** @test */
    public function correct_message_is_shown_when_no_results()
    {
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
        ])->getJson('v1/api/company-result-status?company_domain='.$domain);

        $response->dump()
            ->assertUnprocessable();
        $this->assertEquals('No jobs found for this domain: facebook.com', $response->json('message'));
    }


    /** @test */
    public function user_cant_check_job_status_without_api_key()
    {
        $response = $this->getJson('v1/api/company-result-status');

        $response
            ->assertUnauthorized();
    }
}
