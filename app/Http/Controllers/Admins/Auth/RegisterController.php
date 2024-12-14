<?php

namespace App\Http\Controllers\Users\Auth;

use App\Core\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Auth\RegisterRequest;
use App\Http\Resources\Users\Auth\PrivateUserResource;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function action(RegisterRequest $request)
    {
        try {
            $user = User::create($request->safe()->only(['email','name','password','phone_number']));

            return new PrivateUserResource($user);
        }  catch (\Exception $ex) {
            return ResponseHelper::render500Response($ex);
        }
        
    }
}
