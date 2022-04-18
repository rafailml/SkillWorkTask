<?php

namespace App\Http\Controllers\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ChangePasswordController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(ChangePasswordRequest $request)
    {
        $user = User::where('email', $request->validated()['email'])->first();

        // Check if token exists
        if (Password::tokenExists($user, $request->validated()['token'])) {
            Password::reset($request->validated(), function ($user) use ($request) {
                $user->password = Hash::make($request->validated()['password']);
                $user->save();
            });

            Password::deleteToken($user);

            return response()->json(['message' => 'The password has been changed']);
        } else {
            return response()->json(['message' => 'Invalid token.'], 422);
        }
    }
}
