<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => ['required', 'string', 'min:2', 'max:100'],
            'middlename' => ['nullable', 'string', 'min:2', 'max:100'],
            'lastname' => ['required', 'string', 'min:2', 'max:100'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'birthdate' => ['required', 'date'],
            'country' => ['required', 'integer', 'min:1'],
            'city' => ['sometimes', 'integer', 'min:1'],
            'address' => ['required', 'string', 'min:4', 'max:1000'],
            'nationalid' => ['required', 'string', 'min:6', 'max:100', 'unique:users'],
            'passportnum' => ['sometimes', 'string', 'min:6', 'max:100'],
            'username' => ['required', 'alpha_num', 'min:4', 'max:100', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(6)->letters()->numbers()->symbols()],
            'status' => ['required', 'integer', 'min:1'],
            'visible' => ['required', Rule::in(['visible', 'hidden'])],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'note' => ['nullable', 'string', 'min:4', 'max:1000'],
            'created_by' => ['required', 'int', 'min:1'],
        ]);

        $user = User::create([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'country' => $request->country,
            'city' => $request->city,
            'address' => $request->address,
            'nationalid' => $request->nationalid,
            'passportnum' => $request->passportnum,
            'username' => $request->username,
            'status' => $request->status,
            'visible' => $request->visible,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'created_by' => $request->created_by,
            'note' => $request->note,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return ApiMessagesTemplate::createResponse(true, 200, "User Created Successfully", $user);
    }
}
