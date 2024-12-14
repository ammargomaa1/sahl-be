<?php

namespace App\Http\Controllers\Users\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Auth\LoginRequest;
use App\Http\Resources\Users\Auth\PrivateUserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function action(LoginRequest $request){
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'error' => 'The provided credentials are incorrect.',
            ]);
        }

        return (new PrivateUserResource($user))
            ->additional([
                'meta'=>[
                    'token' => $user->createToken($request->email)->plainTextToken
                ]
        ]);
    }
}
