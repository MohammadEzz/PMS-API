<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\DiseaseActiveIngredientRequest;
use App\Models\ActiveIngredient;
use App\Models\Disease;
use Illuminate\Http\Request;

class DiseaseActiveIngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($diseaseId)
    {
        $disease = Disease::find($diseaseId);

        if($disease) {
            $diseaseActiveIngredients = $disease->activeIngredients()->get();
            return ApiMessagesTemplate::createResponse(true, 200, "Disease Active Ingredients Readed Successfully", $diseaseActiveIngredients);
        }
        else
            return ApiMessagesTemplate::createResponse(false, 404, "Disease Not Exist");
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
        $disease = Disease::find($diseaseId);
        $activeIngredient = ActiveIngredient::find($activeingredientId);

        if(!$disease)
            return ApiMessagesTemplate::createResponse(false, 404, "Disease Not Exist");
        elseif(!$activeIngredient)
            return ApiMessagesTemplate::createResponse(false, 404, "Active Ingredient Not Exist");
        elseif($disease && $activeIngredient) {
            $disease->activeIngredients()->attach($activeingredientId, ["order" => $request->input("order")]);
            $diseaseActiveIngredient = $disease
            ->activeIngredients()
            ->where("activeingredients.id", $activeingredientId)
            ->get();

            if($diseaseActiveIngredient->isNotEmpty()){
                return ApiMessagesTemplate::createResponse(true, 200, "Disease Active Ingredients Created Successfully", $diseaseActiveIngredient);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($diseaseId, $activeIngredientId)
    {
        $disease = Disease::find($diseaseId);
        $activeIngredient = ActiveIngredient::find($activeIngredientId);

        if(!$disease) {
            return ApiMessagesTemplate::createResponse(false, 404, "Disease Not Exist");
        }
        elseif(!$activeIngredient) {
            return ApiMessagesTemplate::createResponse(false, 404, "Active Ingredient Not Exist");
        }
        elseif($disease && $activeIngredient) {
            $diseaseActiveIngredient = $disease->activeIngredients()->where('activeingredients.id', $activeIngredientId)->get();
            return ApiMessagesTemplate::createResponse(true, 200, "Disease Active Ingredient Readed Successfully", $diseaseActiveIngredient);
        }
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
        $disease = Disease::find($diseaseId);
        $activeIngredient = ActiveIngredient::find($activeIngredientId);

        if(!$disease)
            return ApiMessagesTemplate::createResponse(false, 404, "Disease Not Exist");
        elseif(!$activeIngredient)
            return ApiMessagesTemplate::createResponse(false, 404, "Active Ingredient Not Exist");
        elseif($disease && $activeIngredient) {
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
                return ApiMessagesTemplate::createResponse(false, 404, "Disease Not Linked With This Active Ingredient");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($diseaseId, $activeIngredientId)
    {
        $disease = Disease::find($diseaseId);
        $activeIngredient = ActiveIngredient::find($activeIngredientId);

        if(!$disease)
            return ApiMessagesTemplate::createResponse(false, 404, "Disease Not Exist");
        elseif(!$activeIngredient)
            return ApiMessagesTemplate::createResponse(false, 404, "Active Ingredient Not Exist");
        elseif($disease && $activeIngredient) {
            $diseaseActiveIngredient = $disease->activeIngredients()->where('activeingredients.id', $activeIngredientId)->get();

            if($diseaseActiveIngredient->isNotEmpty()) {
                $disease->activeIngredients()->detach($activeIngredientId);
                $isDeleted = $disease->activeIngredients()->where('activeingredients.id', $activeIngredientId)->get();

                if($isDeleted->isEmpty())
                    return ApiMessagesTemplate::createResponse(true, 201, "Disease Active Ingredient Deleted Successfully");
            }
            else
                return ApiMessagesTemplate::createResponse(false, 404, "Disease Not Linked With This Active Ingredient");
        }
    }
}
