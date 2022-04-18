<?php

namespace App\Http\Controllers\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserLoginRequest;
use App\Http\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(UserLoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->validated()['email'], 'password' => $request->validated()['password']])) {

            $user = Auth::user();
            $user->token = Str::random(64);
            $user->token_expiration = Carbon::now()->addHours(12)->timestamp;
            $user->save();

            Auth::login($user);

            return response()->json(new UserResource($user));
        } else {

            return response()->json([
                'message' => 'These credentials do not match our records.',
            ], 422);
        }
    }
}
