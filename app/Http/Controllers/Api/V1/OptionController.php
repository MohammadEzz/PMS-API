<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Http\Repository\OptionRepository;
use App\Models\Option;
use Exception;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, OptionRepository $repository)
    {

        $fields = [
            'id' => 'id',
            "name" => "name",
            "type" => "type",
            "order" => "order",
        ];

        $optionItems = $repository->fetchListOfItems($request, $fields);

        return ApiMessagesTemplate::apiResponseDefaultMessage(true, 200, "Options Readed Successfully", $optionItems);
    }
}
