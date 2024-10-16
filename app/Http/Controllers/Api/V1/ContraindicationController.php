<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\ContraindicationRequest;
use App\Models\Contraindication;
use Illuminate\Http\Request;

class ContraindicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contraindications = Contraindication::all();

        $response = ApiMessagesTemplate::apiResponseDefaultMessage(true, 200, "Contraindications Readed Successfully", ['contraindications' => $contraindications]);
        return response()->json($response,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContraindicationRequest $request)
    {
        $data = $request->input();

        $contraindication = new Contraindication();
        $contraindication->category = $data['category'];
        $contraindication->description = $data['description'];
        $contraindication->level = $data['level'];
        $contraindication->order = $data['order'];
        $contraindication->drug_id = $data['drug_id'];
        $isCreated = $contraindication->save();

        if($isCreated) {
            $response = ApiMessagesTemplate::apiResponseDefaultMessage(true, 201, "Contraindication Added Successfully");
            return response()->json($response, 201);
        }
        return response()->json([], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contraindication = Contraindication::find($id);

        if($contraindication){
            $response = ApiMessagesTemplate::apiResponseDefaultMessage(true, 200, "Contraindication Readed Successfully", ["contraindication" => $contraindication]);
            return response()->json($response, 200);
        }
        else {
            $response = ApiMessagesTemplate::apiResponseDefaultMessage(false, 404, "Contraindication Not Exist");
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
    public function update(ContraindicationRequest $request, $id)
    {
        $data = $request->input();

        $contraindication = Contraindication::find($id);

        if($contraindication) {
            $contraindication->category = $data['category'];
            $contraindication->description = $data['description'];
            $contraindication->level = $data['level'];
            $contraindication->order = $data['order'];
            $contraindication->drug_id = $data['drug_id'];
            $isUpdated = $contraindication->save();

            if($isUpdated) {
                $response = ApiMessagesTemplate::apiResponseDefaultMessage(true, 204, "Contraindication Updated Succefully");
                return response()->json($response, 201);
            }
            return response()->json([], 400);
        }
        else {
            $response = ApiMessagesTemplate::apiResponseDefaultMessage(false, 404, "Contraindication Not Exist");
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
        $contraindication = Contraindication::find($id);

        if($contraindication) {
            $isDeleted = $contraindication->delete();

            if($isDeleted) {
                $response = ApiMessagesTemplate::apiResponseDefaultMessage(true, 204, "Contraindication Deleted Succefully");
                return response()->json($response, 204);
            }
        }
        else {
            $response = ApiMessagesTemplate::apiResponseDefaultMessage(false, 404, "Contraindication Not Exist");
            return response()->json($response, 404);
        }
    }
}
