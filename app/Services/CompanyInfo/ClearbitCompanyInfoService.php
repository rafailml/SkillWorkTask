<?php

namespace App\Services\CompanyInfo;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClearbitCompanyInfoService implements \App\Interfaces\CompanyInfo\CompanyInfoInterface
{

    public function getResults($domain)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('api.clearbit_api_key')
        ])->get('https://company.clearbit.com/v2/companies/find', [
            'domain' => $domain,
        ]);

        if ($response->failed()) {
            // Job failed
            Log::error("Error executing Clearbit Company query: " . $response->reason() . " status: " . $response->status());
            throw new \Exception("Error executing job: " . $response->reason() . " status: " . $response->status());
        } else {
            // Job is successful
            return json_decode($response->body());
        }
    }
}
