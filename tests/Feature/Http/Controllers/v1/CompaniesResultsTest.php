<?php

namespace Tests\Feature\Http\Controllers\v1;

use App\Models\CompanyResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompaniesResultsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_company_results()
    {
        $user = User::factory()->create();
        $response = $this->postJson('v1/api/login', [
            'email' => $user->email,
            'password' => 'MySecre1'
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('token', $response->json('token'));

        CompanyResult::create([
            'user_id' => $user->id,
            'domain' => 'facebook.com',
            'job_finished' => false,
        ]);

        CompanyResult::create([
            'user_id' => $user->id,
            'domain' => 'google.com',
            'job_finished' => true,
        ]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $response->json('token'),
        ])->getJson('v1/api/companies-results');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->assertEquals('facebook.com', $response->json('data')[0]['domain']);
        $this->assertEquals('google.com', $response->json('data')[1]['domain']);
    }

    /** @test */
    public function user_cant_get_results_when_not_authorized()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->getJson('v1/api/companies-results');

        $response
            ->assertUnauthorized();
    }
}
