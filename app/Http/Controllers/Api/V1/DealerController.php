<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Http\Repository\DealerRepository;
use App\Models\Dealer;
use Illuminate\Http\Request;

class DealerController extends Controller
{
    public function index(Request $request, DealerRepository $repository)
    {
        $fields = [
            "id" => "id",
            'supplier' => 'supplier_id',
            "name" => "name",
        ];

        $dealerItems = $repository->fetchListOfItems($request, $fields);

        return ApiMessagesTemplate::createResponse(true, 200, "Dealers Readed Successfully", $dealerItems);
    }
}
