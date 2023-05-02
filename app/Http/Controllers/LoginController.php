<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginFormRequest;
use App\Http\Resources\LoginResource;

class LoginController extends Controller
{
	public function login(LoginFormRequest $request)
	{
		$requestData = $request->only(['email', 'password']);

		$isLogged = Auth::attempt( $requestData );

		if ( !$isLogged ) {
			return response()->json(["message" => 'E-mail e/ou senha incorretos'], 401);
		}
		
		/** @var User */
		$user  = Auth::user();
		$token = $user->createToken('AccessToken');

		return LoginResource::make([
			'user'           => $user,
			'accessToken'    => $token->accessToken,
			'plainTextToken' => $token->plainTextToken,
		]);
	}
}
