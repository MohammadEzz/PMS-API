<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Models\ActiveIngredient;
use Illuminate\Http\Request;

class ActiveIngredientInteractionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $activeIngredient = ActiveIngredient::find($id);
        if($activeIngredient) {
            $activeIngredientInteractions = $activeIngredient->drugInteractions();

            return ApiMessagesTemplate::createResponse(true, 200, "Druginteractions of Active Ingredient Readed Successfully", $activeIngredientInteractions);
        }
        else
            return ApiMessagesTemplate::createResponse(false, 404, "Active Ingredient Not Exist");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
