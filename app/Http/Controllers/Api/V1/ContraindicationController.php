<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\ContraindicationRequest;
use App\Models\Contraindication;

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

        return ApiMessagesTemplate::createResponse(true, 200, "Contraindications Readed Successfully", ['contraindications' => $contraindications]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContraindicationRequest $request)
    {
        $data = $request->validated();

        $isCreated = Contraindication::create([
            "category" => $data['category'],
            "description" => $data['description'],
            "level" => $data['level'],
            "order" => $data['order'],
            "drug_id" => $data['drug_id']
        ]);

        if($isCreated)
            return ApiMessagesTemplate::createResponse(true, 201, "Contraindication Added Successfully", ["id" => $isCreated->id]);

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
        $contraindication = Contraindication::findOrFail($id);

        return ApiMessagesTemplate::createResponse(true, 200, "Contraindication Readed Successfully", ["contraindication" => $contraindication]); 
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
        $data = $request->validated();

        $contraindication = Contraindication::findOrFail($id);

        $contraindication->category = $data['category'];
        $contraindication->description = $data['description'];
        $contraindication->level = $data['level'];
        $contraindication->order = $data['order'];
        $contraindication->drug_id = $data['drug_id'];
        $isUpdated = $contraindication->save();

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
        $contraindication = Contraindication::findOrFail($id);

        $isDeleted = $contraindication->delete();

        if($isDeleted) 
            return response()->json([], 204);

        return response()->json(["message" => "Server Error"], 500); 
    }
}
