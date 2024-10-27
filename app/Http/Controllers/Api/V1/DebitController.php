<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Repository\DebitRepository;
use Illuminate\Http\Request;

class DebitController extends Controller
{
    public function index(Request $request,  DebitRepository $repository) {

        $fields = [
            'id'=>"debits.id",
            'supplier_id'=>'creditor_id',
            "name" => "name",
            "amount" => "amount",
        ];

        $debitItems = $repository->fetchListOfItems($request, $fields);

        return ApiMessagesTemplate::createResponse(true, 200, "Debits items readed successfully", $debitItems);
    }
}
