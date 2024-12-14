<?php

namespace App\Http\Controllers\Admins\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\Auth\PrivateUserResource;
use App\Models\User;
use App\Scoping\Scopes\IsBusinessScope;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::withScopes($this->scopes())->paginate($request->per_page ?? 10);
        return PrivateUserResource::collection($users);
    }

    protected function scopes()
    {
        return [
            'is_business' => new IsBusinessScope()
        ];
    }

    public function toggleIsBusiness(User $user)
    {
        $user->is_business = !$user->is_business;
        $user->save();
        return (new PrivateUserResource($user));
    }
}
