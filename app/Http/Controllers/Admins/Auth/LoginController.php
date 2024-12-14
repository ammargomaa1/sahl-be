<?php

namespace App\Http\Controllers\Admins\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Auth\LoginRequest;
use App\Http\Resources\Admins\Auth\PrivateAdminResource;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class LoginController extends Controller
{
    public function action(LoginRequest $request){
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'error' => 'The provided credentials are incorrect.',
            ]);
        }
        $admin->tokens()->delete();

        return (new PrivateAdminResource($admin))
            ->additional([
                'meta'=>[
                    'token' => $admin->createToken($request->email)->plainTextToken
                ]
        ]);
    }
}
