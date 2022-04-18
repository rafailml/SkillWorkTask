<?php

namespace App\Http\Controllers\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgottenPasswordRequest;
use App\Models\User;
use App\Notifications\ForgottenPassword;
use Illuminate\Support\Facades\Password;

class ForgottenPasswordController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(ForgottenPasswordRequest $request)
    {
        $user = User::where('email', $request->validated()['email'])->first();

        // Creates a token and save it to password_resets
        $passwordResetToken = Password::createToken($user);

        $user->notify(new ForgottenPassword($passwordResetToken));

        return response()->json(['message' => 'Password reset email was sent',], 201);
    }
}
