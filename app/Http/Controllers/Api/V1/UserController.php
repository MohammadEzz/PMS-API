<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function index(Request $request, ApiFilter $filter, ApiSort $sort, ApiField $field)
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
            "status" => "options.name as status",
            "note" => "users.note",
            "visible" => "users.visible",
            "created_by_id" => "admin.id as created_by_id",
            "created_by_fn" => "admin.firstname as created_by_fn",
            "created_by_ln" => "admin.lastname as created_by_ln",
            "created_at" => "users.created_at",
            "updated_at" => "users.updated_at",
            "lastlogin" => "users.lastlogin",
        ];

        $fieldParams = $fields;
        $filterParams = '';
        $sortParams = [];
        $rangeParams = 20;

        if($request->has('fields')) {
            $urlFields = $request->query('fields');
            $fieldParams = $field->buildFields($urlFields, $fields);
        }

        if($request->has('filter')) {
            $urlFilter = $request->query('filter');
            [$filterParams, $queryParams] = $filter->buildFilter($urlFilter, $fields);
        }
        
        if($request->has('sort')) {
            $urlSort = $request->query('sort');
            $sortParams = $sort->buidlSort($urlSort, $fields);
        }

        if($request->has('range')) {
            $urlRange = $request->query('range');
            $rangeParams = strtolower($urlRange);
        }

        //Select & Joins
        $query = User::select($fieldParams)
        ->join('countries', 'countries.id', '=', 'users.country')
        ->join('options', 'options.id', '=', 'users.status')
        ->leftJoin('cities', 'cities.id', '=', 'users.city')
        ->leftJoin('users as admin', 'admin.id', '=', 'users.created_by');

        // Where
        $filterParams && $query->WhereRaw($filterParams, $queryParams);

        // Order By | Sorting
        if(is_array($sortParams) && count($sortParams) > 0) {
            foreach($sortParams as $value){
                [$field, $sortType] = explode('.', $value);
                $field = $this->mapSortParamWithTableField($field);
                $query->orderBy($field, $sortType);
            }
        }
        else $query->orderBy('users.id', 'desc');

        $users = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

        return ApiMessagesTemplate::createResponse(true, 200, "Users readed successfully", $users);
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

        $user = User::query()->create([
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
            return ApiMessagesTemplate::createResponse(true, 201, "User created successfully", $user);
        else
            return ApiMessagesTemplate::createResponse(true, 400, "User created failed");

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if($user)
            return ApiMessagesTemplate::createResponse(true, 200, "User readed successfully", $user);
        else
            return ApiMessagesTemplate::createResponse(false, 404, "User not exist");
    }

    public function updateUserName(Request $request, $id) {

        $user = User::find($id);

        if($user) {
            $request->validate(['username' => ['required', 'alpha_num', 'min:4', 'max:100', 'unique:users']]);
            $user->username = $request->username;
            $isUpdated = $user->save();
            if($isUpdated)
                return ApiMessagesTemplate::createResponse(true, 204, "UserName updated successfully");
            else
                return ApiMessagesTemplate::createResponse(true, 400, "UserName updated failed");
        }
        else 
            return ApiMessagesTemplate::createResponse(false, 404, "User not exist");
    }

    public function updateEmail(Request $request, $id) {
        $user = User::find($id);

        if($user) {
            $request->validate(['email' => ['required', 'string', 'email', 'max:255', 'unique:users']]);
            $user->email = $request->email;
            $isUpdated = $user->save();
            if($isUpdated)
                return ApiMessagesTemplate::createResponse(true, 204, "Email updated successfully");
            else
                return ApiMessagesTemplate::createResponse(true, 400, "Email updated failed");
        }
        else 
            return ApiMessagesTemplate::createResponse(false, 404, "User not exist");
    }

    public function updatePassword(Request $request, $id) {
        $user = User::find($id);

        if($user) {
            $request->validate(['password' => ['required', 'confirmed', Password::min(6)->letters()->numbers()->symbols()]]);
            $user->password = Hash::make($request->password);
            $isUpdated = $user->save();
            if($isUpdated)
                return ApiMessagesTemplate::createResponse(true, 204, "Password  updated successfully");
            else
                return ApiMessagesTemplate::createResponse(true, 400, "Password updated failed");
        }
        else 
            return ApiMessagesTemplate::createResponse(false, 404, "User not exist");
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
        $user = User::find($id);

        if($user) {
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
                return ApiMessagesTemplate::createResponse(true, 201, "User updated successfully", $user);
            else
                return ApiMessagesTemplate::createResponse(false, 400, "User updated failed");
        }
        else 
            return ApiMessagesTemplate::createResponse(false, 404, "User not exist");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if($user) {
            $isDeleted = User::find($id)->delete();
            if($isDeleted) 
                return ApiMessagesTemplate::createResponse(true, 204, "User deleted successfully");
            else
                return ApiMessagesTemplate::createResponse(false, 400, "User deleted failed");
        }
        else 
            return ApiMessagesTemplate::createResponse(false, 404, "User not exist");
    }
}
