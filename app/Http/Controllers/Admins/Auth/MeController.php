<?php

namespace App\Http\Controllers\Users\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\Auth\PrivateUserResource;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function action(Request $request){
        return new PrivateUserResource  ($request->user());
    }
}
