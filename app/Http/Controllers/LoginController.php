<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class LoginController extends Controller
{
    //
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) {
            $user_id = Auth::user()->id;
            $user = User::where('id', $user_id)->first();
            $success['token'] =  $user->createToken('api-cab-driver')->plainTextToken;
            $success['name'] =  $user->name;

            return response()->json([
                'status' => 'success',
                'message' => 'Login success',
                'content' => $success
            ], 200);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Email or password wrong'
            ], 400);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Logged out'
            ], 200);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to logout'
            ], 200);
        }
    }
}
