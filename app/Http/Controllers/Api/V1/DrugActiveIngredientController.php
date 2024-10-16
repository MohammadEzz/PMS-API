<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\DrugActiveIngredientRequest;
use App\Models\ActiveIngredient;
use App\Models\Drug;
use App\Models\DrugActiveIngredient;
use Illuminate\Support\Facades\DB;

class DrugActiveIngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($drugId)
    {
        $drug = Drug::find($drugId);

        if($drug) {
            $activeIngredients = Drug::find($drugId)->activeIngredients()
            ->leftJoin('options', 'options.id', '=', 'drug_activeingredient.format')
            ->select('activeingredients.id','activeingredients.name', 'drug_activeingredient.concentration', 'options.name as format')
            ->get();
            return ApiMessagesTemplate::createResponse(true, 200,  "Durg Active Ingredients Readed Successfully", $activeIngredients);
        }
        else
            return ApiMessagesTemplate::createResponse(false, 404,  "Durg Not Exist");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DrugActiveIngredientRequest $request, $drugId){
        $drug = Drug::find($drugId);
        $activeIngredientId = $request->input('activeingredient_id');
        $activeIngredient = ActiveIngredient::find($activeIngredientId);

        if(!$drug)
            return ApiMessagesTemplate::createResponse(false, 404,  "Durg Not Exist");
        elseif(!$activeIngredient)
            return ApiMessagesTemplate::createResponse(false, 404,  "Active Ingredient Not Exist");
        elseif($drug && $activeIngredient) {
            $drug->activeIngredients()
                 ->attach($activeIngredientId, [ "concentration" => $request->input('concentration'),
                                                 "format" => $request->input('format'),
                                                 "order" => $request->input('order') ]);

            $drugActiveIngredient = $drug->activeIngredients()
                                         ->where("activeingredient_id", $activeIngredientId)
                                         ->leftJoin('options', 'options.id', '=', 'drug_activeingredient.format')
                                         ->select('activeingredients.id','activeingredients.name', 'drug_activeingredient.concentration', 'options.name as format')
                                         ->first();

            if($drugActiveIngredient)
                return ApiMessagesTemplate::createResponse(true, 200,  "Durg Active Ingredients Created Successfully", $drugActiveIngredient);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Drug  $drug
     * @return \Illuminate\Http\Response
     */
    public function show($drugId, $activeIngredientId)
    {
       $drug = Drug::find($drugId);
       $activeIngredient = ActiveIngredient::find($activeIngredientId);

       if(!$drug)
            return ApiMessagesTemplate::createResponse(false, 404, "Durg Not Exist");
       elseif(!$activeIngredient)
            return ApiMessagesTemplate::createResponse(false, 404, "Active Ingredient Not Exist");
       elseif($drug && $activeIngredient) {
            $drugActiveIngredient = Drug::find($drugId)
            ->activeIngredients()
            ->where('activeingredients.id', $activeIngredientId)
            ->get();

            return ApiMessagesTemplate::createResponse(true, 200, "Durg Active Ingredients Readed Successfully", $drugActiveIngredient);
       }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Drug  $drug
     * @return \Illuminate\Http\Response
     */
    public function update(DrugActiveIngredientRequest $request, $drugId, $activeIngredientId)
    {
        $data = $request->only(["activeingredient_id", 'drug_id', "concentration", "format", "order"]);
        $drug = Drug::find($drugId);
        $activeIngredient = ActiveIngredient::find($activeIngredientId);

        if(!$drug)
            return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Exist");
        elseif(!$activeIngredient)
            return ApiMessagesTemplate::createResponse(false, 404, "Active Ingredient Not Exist");
        elseif($drug && $activeIngredient) {
            $pivotRecord = DrugActiveIngredient:: where([
                ['activeingredient_id', '=', $activeIngredientId],
                ["drug_id", '=', $drugId]])
                ->get();

            if($pivotRecord->isNotEmpty()) {
                $drug->activeIngredients()->updateExistingPivot($activeIngredientId, $data);

                $drugActiveIngredients = Drug::find($drugId)
                ->activeIngredients()
                ->where('activeingredients.id', $data['activeingredient_id'])
                ->leftJoin('options', 'options.id', '=', 'drug_activeingredient.format')
                ->select('activeingredients.id','activeingredients.name', 'drug_activeingredient.concentration', 'options.name as format')
                ->first();
                return ApiMessagesTemplate::createResponse(true, 200, "Drug Active Ingredient Updated Successfully", $drugActiveIngredients);
            }
            else
                return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Linked With This Active Ingredient");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Drug  $drug
     * @return \Illuminate\Http\Response
     */
    public function destroy($drugId, $activeIngredientId)
    {
        $drug = Drug::find($drugId);
        $activeIngredient = ActiveIngredient::find($activeIngredientId);

        if(!$drug)
            return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Exist");
        elseif(!$activeIngredient)
            return ApiMessagesTemplate::createResponse(false, 404, "Active Ingredient Not Exist");
        elseif($drug && $activeIngredient){
            $pivotRecord = DrugActiveIngredient:: where([
                            ['activeingredient_id', '=', $activeIngredientId],
                            ["drug_id", '=', $drugId]])
                            ->get();

            if($pivotRecord->isNotEmpty()) {
                $drug->activeIngredients()->detach($activeIngredientId);
                $isDeleted = $drug->activeIngredients()->where('activeingredients.id', $activeIngredientId)->get();

                if($isDeleted->isEmpty())
                    return ApiMessagesTemplate::createResponse(true, 201, "Drug Active Ingredient Deleted Successfully");
            }
            else
                return ApiMessagesTemplate::createResponse(false, 404, "Drug Not Linked With This Active Ingredient");
        }
    }
}
