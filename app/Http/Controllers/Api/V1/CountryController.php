<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Http\Helpers\Api\ListOfOperator;
use App\Http\Helpers\Api\Operator;
use App\Http\Repository\CountryRepository;
use App\Models\Country;
use ErrorException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, CountryRepository $repository)
    {
        $fields = [
            "id" => "id",
            "iso" => "iso",
            "name" => "name",
            "nicename" => "nicename",
            "iso3" => "iso3",
            "numcode" => "numcode",
            "phonecode" => "phonecode",
        ];

        $countryItems = $repository->fetchListOfItems($request, $fields);

        return ApiMessagesTemplate::createResponse(true, 200, "Countries readed successfully", $countryItems);
    }
}
