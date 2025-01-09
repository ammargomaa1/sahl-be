<?php

namespace App\Http\Controllers\Users\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Auth\PasswordResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
     /**
     * Handle password reset request (send email).
     */
    public function sendResetLinkEmail(PasswordResetRequest $request)
    {
        // Send reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['error' => __($status)], 400);
    }

    /**
     * Handle password reset submission.
     */
    public function resetPassword(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Reset the password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();

                // Optionally revoke all tokens after password reset
                $user->tokens()->delete();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['error' => __($status)], 400);
    }
}
