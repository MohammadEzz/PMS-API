<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Http\Repository\CityRepository;
use App\Models\City;
use Exception;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, CityRepository $repository)
    {
        $fields = [
            "id" => "id",
            "name" => "name",
            "areacode" => "areacode",
        ];
        
        $cityItems = $repository->fetchListOfItems($request, $fields);

        return ApiMessagesTemplate::createResponse(true, 200, "Cities readed successfully", $cityItems);
    }
}
