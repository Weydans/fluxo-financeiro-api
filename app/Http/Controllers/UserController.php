<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Resources\UserResources;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(UserCreateRequest $request)
    {
        $userData = [
            "name"     => $request->name,
            "email"    => $request->email,
            "password" => bcrypt($request->password),
        ];

        $user = User::create($userData);

        return UserResources::make($user)->response()->setStatusCode(201);
    }
}
