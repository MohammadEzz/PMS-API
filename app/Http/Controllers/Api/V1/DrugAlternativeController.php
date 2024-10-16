<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\DrugAlternativeRequest;
use App\Models\DrugAlternative;

class DrugAlternativeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $drugAlternatives = DrugAlternative::all();
        $response = ApiMessagesTemplate::apiResponseDefaultMessage(true, 200, "Drug Alternatives Readed Successfully", ["drug_alternatives" => $drugAlternatives]);
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DrugAlternativeRequest $request)
    {
        $data = $request->input();

        $drugAlternative = new DrugAlternative();
        $drugAlternative->drug_id = $data['drug_id'];
        $drugAlternative->alternative_id = $data['alternative_id'];
        $drugAlternative->order = $data['order'];
        $isCreated = $drugAlternative->save();

        if($isCreated) {
            $response = ApiMessagesTemplate::apiResponseDefaultMessage(true, 201, "Drug Alternative Added Successfully");
            return response()->json($response, 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $drugAlternative = DrugAlternative::find($id);

        if($drugAlternative) {
            $response = ApiMessagesTemplate::apiResponseDefaultMessage(true, 200, "Drug Alternative Readed Successfully", ["drug_alternative" => $drugAlternative]);
            return response()->json($response, 200);
        }
        else {
            $response = ApiMessagesTemplate::apiResponseDefaultMessage(true, 404, "Drug Alternative Not Exist");
            return response()->json($response, 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DrugAlternativeRequest $request, $id)
    {
        $data = $request->input();

        $drugAlternative = DrugAlternative::find($id);

        if($drugAlternative) {
            $drugAlternative->drug_id = $data['drug_id'];
            $drugAlternative->alternative_id = $data['alternative_id'];
            $drugAlternative->order = $data['order'];
            $isUpdated = $drugAlternative->save();

            if($isUpdated) {
                $response = ApiMessagesTemplate::apiResponseDefaultMessage(true, 204, "Drug Alternative Updated Successfully");
                return response()->json($response, 204);
            }
        }
        else {
            $response = ApiMessagesTemplate::apiResponseDefaultMessage(true, 404, "Drug Alternative Not Exist");
            return response()->json($response, 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $drugAlternative = DrugAlternative::find($id);

        if($drugAlternative) {
            $isDeleted = $drugAlternative->delete();

            if($isDeleted) {
                $response = ApiMessagesTemplate::apiResponseDefaultMessage(true, 204, "Drug Alternative Deleted Successfully");
                return response()->json($response, 204);
            }
        }
        else {
            $response = ApiMessagesTemplate::apiResponseDefaultMessage(true, 200, "Drug Alternative Not Exist");
            return response()->json($response, 404);
        }
    }
}
