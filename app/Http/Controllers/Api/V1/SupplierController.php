<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Http\Repository\SupplierRepository;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request, SupplierRepository $repository)
    {
            $fields = [
                "id" => "id",
                "name" => "name",
            ];

            $supplierItems = $repository->fetchListOfItems($request, $fields);

            return ApiMessagesTemplate::createResponse(true, 200, "Supplier Readed Successfully", $supplierItems);
    }
}
