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
        $activeIngredient = ActiveIngredient::findOrFail($id);
        
        $activeIngredientInteractions = $activeIngredient->drugInteractions();

        return ApiMessagesTemplate::createResponse(true, 200, "Druginteractions of Active Ingredient Readed Successfully", $activeIngredientInteractions);
    }
}
