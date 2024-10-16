<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function authenicate(Request $request) {
        if(Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])){
            return response()->json(["login" => "success", "user" => Auth::user()], 200);
        }
        else {
            return response()->json(["login" => "failed"], 419);
        }
    }

    public function register(Request $request) {

        User::create([
            'email_verified_at' => now(),
            "email" => $request->input('email'),
            "password" => Hash::make($request->input('password')),
            "name" => $request->input('name')
        ]);
    }
}
