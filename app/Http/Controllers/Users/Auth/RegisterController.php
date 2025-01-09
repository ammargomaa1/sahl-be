<?php

namespace App\Http\Controllers\Users\Auth;

use App\Core\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Auth\RegisterRequest;
use App\Http\Resources\Users\Auth\PrivateUserResource;
use App\Mail\VerifyMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class RegisterController extends Controller
{
    public function action(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = User::create($request->safe()->only(['email', 'name', 'password', 'phone_number']) + ['is_business' => false]);
            // Generate the signed verification URL for the API route
            $verificationUrl = URL::temporarySignedRoute(
                'api.verification.verify', // The API verification route name
                now()->addMinutes(60), // Expiry time
                ['id' => $user->id, 'hash' => sha1($user->email)] // Parameters required for verification
            );

            // Send the email
            Mail::to($user->email)->send(new VerifyMail($verificationUrl, $user->name));
            DB::commit();

            return (new PrivateUserResource($user))->additional([
                'meta' => [
                    'token' => $user->createToken($request->email)->plainTextToken
                ]
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ResponseHelper::render500Response($ex);
        }
    }

    public function verifyMail($id, $hash)
    {
        try {
            // Find the user
            $user = User::findOrFail($id);

            // Verify that the hash matches
            if (! hash_equals((string) $hash, sha1($user->email))) {
                return redirect(env('APP_URL') . '/account/email?email_is_verified=0');
            }

            // Check if the user has already verified their email
            if ($user->hasVerifiedEmail()) {
                return redirect(env('APP_URL') . '/account/email?email_is_verified=1');
            }

            // Mark the email as verified and update the timestamp
            $user->markEmailAsVerified();

            return redirect(env('APP_URL') . '/account/email?email_is_verified=1');
        } catch (\Exception $ex) {
            return ResponseHelper::render500Response($ex);
        }
    }

    public function sendVerificationEmail(Request $request)
    {
        try {

            if ($request->user()->hasVerifiedEmail()) {
                return ResponseHelper::renderCustomSuccessResponse([
                    'message' => __('ALL.USER_ALREADY_VERIFIED')
                ]);
            }

            // Generate the signed verification URL for the API route
            $verificationUrl = URL::temporarySignedRoute(
                'api.verification.verify', // The API verification route name
                now()->addMinutes(60), // Expiry time
                ['id' => $request->user()->id, 'hash' => sha1($request->user()->email)] // Parameters required for verification
            );

            // Send the email
            Mail::to($request->user()->email)->send(new VerifyMail($verificationUrl, $request->user()->name));
            return ResponseHelper::renderCustomSuccessResponse();
        } catch (\Exception $ex) {
            return ResponseHelper::render500Response($ex);
        }
    }
}
