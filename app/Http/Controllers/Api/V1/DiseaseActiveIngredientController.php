<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\DiseaseActiveIngredientRequest;
use App\Models\ActiveIngredient;
use App\Models\Disease;

class DiseaseActiveIngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($diseaseId)
    {
        $disease = Disease::findOrFail($diseaseId);

        $diseaseActiveIngredients = $disease->activeIngredients()->get();

        return ApiMessagesTemplate::createResponse(true, 200, "Disease Active Ingredients Readed Successfully", $diseaseActiveIngredients);
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DiseaseActiveIngredientRequest $request, $diseaseId)
    {
        $activeingredientId = $request->input("activeingredient_id");
        ActiveIngredient::findOrFail($activeingredientId);
        $disease = Disease::findOrFail($diseaseId);

        $disease->activeIngredients()->attach($activeingredientId, ["order" => $request->input("order")]);
        $diseaseActiveIngredient = $disease
        ->activeIngredients()
        ->where("activeingredients.id", $activeingredientId)
        ->get();

        if($diseaseActiveIngredient->isNotEmpty())
            return ApiMessagesTemplate::createResponse(true, 200, "Disease Active Ingredients Created Successfully", $diseaseActiveIngredient);
    
        return response()->json(["message" => "Server Error"], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($diseaseId, $activeIngredientId)
    {
        $disease = Disease::findOrFail($diseaseId);
        ActiveIngredient::findOrFail($activeIngredientId);

        $diseaseActiveIngredient = $disease->activeIngredients()->where('activeingredients.id', $activeIngredientId)->get();

        return ApiMessagesTemplate::createResponse(true, 200, "Disease Active Ingredient Readed Successfully", $diseaseActiveIngredient);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DiseaseActiveIngredientRequest $request, $diseaseId, $activeIngredientId)
    {
        $disease = Disease::findOrFail($diseaseId);
        ActiveIngredient::findOrFail($activeIngredientId);

        $diseaseActiveIngredient = $disease->activeIngredients()->where('activeingredients.id', $activeIngredientId)->get();

        if($diseaseActiveIngredient->isNotEmpty()) {
            $isUpdated = $disease->activeIngredients()
            ->updateExistingPivot($activeIngredientId,
            ["activeingredient_id" => $request->input("activeingredient_id"), "order" => $request->input("order")]);

            if($isUpdated) {
                $diseaseActiveIngredient = $disease->activeIngredients()
                ->where('disease_activeingredient.activeingredient_id', $request->input("activeingredient_id"))
                ->first();

                return ApiMessagesTemplate::createResponse(true, 200, "Disease Active Ingredient Updated Successfully", $diseaseActiveIngredient);
            }
        }
        else
            return ApiMessagesTemplate::createResponse(false, 422, "Disease Not Linked With This Active Ingredient");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($diseaseId, $activeIngredientId)
    {
        $disease = Disease::findOrFail($diseaseId);
        ActiveIngredient::findOrFail($activeIngredientId);

        $diseaseActiveIngredient = $disease->activeIngredients()->where('activeingredients.id', $activeIngredientId)->get();

        if($diseaseActiveIngredient->isNotEmpty()) {
            $isDeleted = $disease->activeIngredients()->detach($activeIngredientId);

            if($isDeleted)
                return response()->json([], 204);
        }
        else
            return ApiMessagesTemplate::createResponse(false, 422, "Disease Not Linked With This Active Ingredient");
        
    }
}
