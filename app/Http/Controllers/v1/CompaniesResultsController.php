<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyResultCollection;
use App\Models\CompanyResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompaniesResultsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        $result = CompanyResult::where('user_id', Auth::user()->id)
            ->paginate();

        return response()->json(new CompanyResultCollection($result));
    }
}
