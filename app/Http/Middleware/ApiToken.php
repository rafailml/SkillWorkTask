<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        if ($request->bearerToken()) {
            try {
                $user = User::where('token', $request->bearerToken())
                    ->where('token_expiration', '>', Carbon::now()->timestamp)
                    ->firstOrFail();
            } catch (Exception $ex) {
                Log::error('Error authorizing user: ' . $ex->getMessage());
                return response()->json('Unauthorized', 401);
            }

            Auth::login($user);

            return $next($request);
        } else {
            return response()->json('Unauthorized', 401);
        }

    }
}
