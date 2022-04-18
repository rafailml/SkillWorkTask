<?php

namespace App\Http\Controllers\v1;

use App\Events\CompanyRequestRecieved;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyResultRequest;
use App\Jobs\CompanyResultJob;
use App\Models\CompanyResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CompanyResultController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param CompanyResultRequest $request
     * @return JsonResponse
     */
    public function __invoke(CompanyResultRequest $request): JsonResponse
    {
        $companyResult = CompanyResult::firstOrCreate([
            'user_id' => Auth::user()->id,
            'domain' => $request->validated()['company_domain'],
        ]);
        $companyResult->job_finished = false;

        // Dispatch the event
        CompanyRequestRecieved::dispatch();

        // Dispatch the job
        $this->dispatch(new CompanyResultJob($request->validated()['company_domain'], Auth::user()->id));

        return response()->json(["message" => "Your job is queued, you can check the status here: " . route('v1.company-result-status')], 200);
    }
}
