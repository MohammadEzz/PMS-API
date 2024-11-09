<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Repository\PriceRepository;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index(Request $request, PriceRepository $repository)
    {
        $fields = [
            'drug_id'=>"sub.drug_id",
            "name" => "sub.name",
            "brandname" => "sub.brandname",
            "price" => "p2.price"
        ];

        $priceItems = $repository->fetchListOfItems($request, $fields);

        return ApiMessagesTemplate::createResponse(true, 200, "Prices items readed successfully", $priceItems);
    }
}
