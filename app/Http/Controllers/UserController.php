<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'driver_license' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validation->fails()) {
            return ResponseFormatter::error(null, $validation->errors()->all(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'driver_license' => $request->driver_license,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = auth()->guard('api')->attempt($request->only('email', 'password'));

        return ResponseFormatter::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 'User Registered');
    }

    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validation->fails()) {
            return ResponseFormatter::error(null, $validation->errors()->all(), 400);
        }

        if (!auth()->guard('api')->attempt($request->only('email', 'password'))) {
            return ResponseFormatter::error(null, 'Unauthorized, Email or Password Invalid', 401);
        }

        $token = auth()->guard('api')->attempt($request->only('email', 'password'));

        return ResponseFormatter::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'User Logged In');
    }

    public function whoami()
    {
        if (!auth()->guard('api')->check()) {
            return ResponseFormatter::error(null, 'Unauthorized, You are not logged in', 401);
        }
        return ResponseFormatter::success(auth()->guard('api')->user(), 'User Data');
    }

    public function logout()
    {
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        if ($removeToken) {
            return ResponseFormatter::success(null, 'User Logged Out');
        }
    }
}
