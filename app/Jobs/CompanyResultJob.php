<?php

namespace App\Jobs;

use App\Interfaces\CompanyInfo\CompanyInfoInterface;
use App\Models\CompanyResult;
use App\Models\User;
use App\Notifications\CompanyResultsReady;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CompanyResultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $domain;
    private $ownerId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($domain, $ownerId)
    {
        $this->domain = $domain;
        $this->ownerId = $ownerId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CompanyInfoInterface $provider)
    {
        try {
            $jsonResult = $provider->getResults($this->domain);

            // Populate database
            $this->saveResult($jsonResult);

            // Send email to job owner
            $user = User::findOrFail($this->ownerId);
            $user->notify(new CompanyResultsReady());
        } catch (\Exception $ex) {
            $this->fail($ex->getMessage());
        }
    }

    /**
     * @param $jsonResult
     * @param $companyResult
     * @return void
     */
    private function saveResult($jsonResult): void
    {
        $companyResult = CompanyResult::where('user_id', $this->ownerId)
            ->where('domain', $this->domain)
            ->firstOrFail();

        $companyResult->name = $jsonResult->name;
        $companyResult->legal_name = $jsonResult->legalName;
        $companyResult->description = $jsonResult->description;
        $companyResult->location = $jsonResult->location;
        $companyResult->logo = $jsonResult->logo;
        $companyResult->job_finished = true;
        $companyResult->save();
    }
}
