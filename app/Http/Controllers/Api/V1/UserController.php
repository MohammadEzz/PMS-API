<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Repository\UserRepository;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, UserRepository $repository)
    {
        $fields = [
            'id' => 'users.id',
            "firstname" => "users.firstname",
            "middlename" => "users.middlename",
            "lastname" => "users.lastname",
            "gender" => "users.gender",
            "country" => "countries.nicename",
            "city" => "cities.name",
            "address" => "users.address",
            "nationalid" => "users.nationalid",
            "passportnum" => "users.passportnum",
            "username" => "users.username",
            "email" => "users.email",
            "status" => "options.name",
            "note" => "users.note",
            "visible" => "users.visible",
            "created_by_id" => "admin.id",
            "created_by_fn" => "admin.firstname",
            "created_by_ln" => "admin.lastname",
            "created_at" => "users.created_at",
            "updated_at" => "users.updated_at",
            "lastlogin" => "users.lastlogin",
        ];

        $userItems = $repository->fetchListOfItems($request, $fields);

        return ApiMessagesTemplate::createResponse(true, 200, "Users readed successfully", $userItems);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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

        if($user)
            return ApiMessagesTemplate::createResponse(true, 201, "User created successfully", ["id" => $user->id]);
        
        return response()->json(["message" => "Server Error"], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return ApiMessagesTemplate::createResponse(true, 200, "User readed successfully", $user); 
    }

    public function updateUserName(Request $request, $id) {

        $user = User::findOrFail($id);

        $request->validate(['username' => ['required', 'alpha_num', 'min:4', 'max:100', 'unique:users']]);
        $user->username = $request->username;
        $isUpdated = $user->save();
        if($isUpdated)
            return response()->json([], 204);

        return response()->json(["message" => "Server Error"], 500);
    }

    public function updateEmail(Request $request, $id) {

        $user = User::findOrFail($id);

        $request->validate(['email' => ['required', 'string', 'email', 'max:255', 'unique:users']]);
        $user->email = $request->email;
        $isUpdated = $user->save();
        if($isUpdated)
            return response()->json([], 204);

        return response()->json(["message" => "Server Error"], 500);
        
    }

    public function updatePassword(Request $request, $id) {

        $user = User::findOrFail($id);

        $request->validate(['password' => ['required', 'confirmed', Password::min(6)->letters()->numbers()->symbols()]]);
        $user->password = Hash::make($request->password);
        $isUpdated = $user->save();
        if($isUpdated)
            return response()->json([], 204);

        return response()->json(["message" => "Server Error"], 500);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'firstname' => ['required', 'string', 'min:2', 'max:100'],
            'middlename' => ['nullable', 'string', 'min:2', 'max:100'],
            'lastname' => ['required', 'string', 'min:2', 'max:100'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'birthdate' => ['required', 'date'],
            'country' => ['required', 'integer', 'min:1'],
            'city' => ['sometimes', 'integer', 'min:1'],
            'address' => ['required', 'string', 'min:4', 'max:1000'],
            'nationalid' => ['required', 'string', 'min:6', 'max:100', 'unique:users,nationalid,'.$user->id],
            'passportnum' => ['sometimes', 'string', 'min:6', 'max:100'],
            'status' => ['required', 'integer', 'min:1'],
            'visible' => ['required', Rule::in(['visible', 'hidden'])],
            'note' => ['nullable', 'string', 'min:4', 'max:1000'],
            'created_by' => ['required', 'int', 'min:1'],
        ]);

        $user->firstname = $request->firstname;
        $user->middlename = $request->middlename;
        $user->lastname = $request->lastname;
        $user->gender = $request->gender;
        $user->birthdate = $request->birthdate;
        $user->country = $request->country;
        $user->city = $request->city;
        $user->address = $request->address;
        $user->nationalid = $request->nationalid;
        $user->passportnum = $request->passportnum;
        $user->status = $request->status;
        $user->visible = $request->visible;
        $user->created_by = $request->created_by;
        $user->note = $request->note;
        $isUpdated = $user->save();

        if($isUpdated)
            return response()->json([], 204);
        
        return response()->json(["message" => "Server Error"], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $isDeleted = $user->delete();
        if($isDeleted) 
            return response()->json([], 204);
        
        return response()->json(["message" => "Server Error"], 500);
       
    }
}
