<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Repository\ActiveIngredientRepository;
use App\Http\Requests\ActiveIngredientRequest;
use App\Models\ActiveIngredient;
use Illuminate\Http\Request;

class ActiveIngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ActiveIngredientRepository $repository)
    {
        $fields = [
            'id' => 'id',
            "name" => "name",
            "globalname" => "globalname",
        ];

        $activeIngredients = $repository->fetchListOfItems($request, $fields);
          
        return ApiMessagesTemplate::createResponse(true, 200, "Active Ingredients Readed Successfully", $activeIngredients);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ActiveIngredientRequest $request)
    {
        $data = $request->validated();

        $isCreated = ActiveIngredient::create([
            "name" => $data['name'],
            "globalname" => $data['globalname'],
        ]);

        if($isCreated)
            return ApiMessagesTemplate::createResponse(true, 201, "Active ingredient added successfully", ["id" => $isCreated->id]);
        
        return response()->json(['message' => 'Server Error'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $activeIngredient = ActiveIngredient::findOrFail($id);

        return ApiMessagesTemplate::createResponse(true, 200, "Active ingredient readed successfully", $activeIngredient); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ActiveIngredientRequest $request, $id)
    {
        $data = $request->validated();

        $activeIngredient = ActiveIngredient::findOrFail($id);
 
        $activeIngredient->name = $data['name'];
        $activeIngredient->globalname = $data['globalname'];
        $isUpdated = $activeIngredient->save();

        if($isUpdated)
            return response()->json([], 204);
        
        return response()->json(['message' => 'Server Error'], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $activeIngredient = ActiveIngredient::findOrFail($id);

        $isDeleted = $activeIngredient->delete();
        
        if($isDeleted)
            return response()->json([], 204);
        
        return response()->json(['message' => 'Server Error'], 500);
    }
}
