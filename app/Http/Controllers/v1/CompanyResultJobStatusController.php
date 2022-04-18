<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyResultJobRequest;
use App\Http\Resources\CompanyResultResource;
use App\Models\CompanyResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CompanyResultJobStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param CompanyResultJobRequest $request
     * @return JsonResponse
     */
    public function __invoke(CompanyResultJobRequest $request): JsonResponse
    {
        $result = CompanyResult::where('domain', $request->validated()['company_domain'])
            ->where('user_id', Auth::user()->id)
            ->first();

        if ($result) {
            return response()->json(new CompanyResultResource($result));
        } else {
            return response()->json(["message" => "No jobs found for this domain: " . $request->validated()['company_domain']], 422);
        }
    }
}
