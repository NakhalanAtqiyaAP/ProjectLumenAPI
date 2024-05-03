<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Helpers\ApiFormatter;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'logout']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
	    $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);


        $credatials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credatials)) {
        return ApiFormatter::sendResponse(400, 'User not not found', 'Silakan  Cek kembali email dan password');
        }

    // $ttl = Config::get('jwt.ttl');
        
    // $expires_in = $ttl * 60; 

        $responsWithToken=[
            'access_token' => $token,
            'token_type' =>'bearer',
            'user' => auth()->user(),
            'expires_in' => auth()->factory()->getTTL() * 60 * 24
        ];
        return ApiFormatter::sendResponse(200, 'logged-in', $responsWithToken);
    }

     /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
       return ApiFormatter::sendResponse(200, 'success', auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
       auth()->logout();

       return ApiFormatter::sendResponse(200, 'Success', 'Berhasil Logout');
    }
    
}