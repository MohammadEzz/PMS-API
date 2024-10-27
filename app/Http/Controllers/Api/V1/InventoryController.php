<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Helpers\Api\ApiSort;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Repository\InventoryRepository;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, InventoryRepository $repository)
    {
        $fields = [
            "id" => "inventory.id",
            'purchaseitem_id'=>"purchaseitem_id",
            "drug_id" => "drugs.id",
            "drugname" => "drugs.name",
            "drugbarcode" => "drugs.barcode",
            "quantity" => "inventory.quantity",
            "purchaseprice" => "purchaseprice",
            "tax" => "tax",
            "discount" => "discount",
            "expiredate" => "inventory.expiredate",
        ];

        $inventoryItems = $repository->fetchListOfItems($request, $fields);
        
        return ApiMessagesTemplate::createResponse(true, 200, "Purchase items readed successfully", $inventoryItems);
    }

    public function quantity(Request $request,  InventoryRepository $repository) {
        $fields = [
            'drug_id'=>"drug_id",
            "name" => "name",
            "brandname" => "brandname",
            "middleunitnum" => "middleunitnum",
            "smallunitnum" => "smallunitnum",
        ];
        
        $quantityItems = $repository->fetchQuantity($request, $fields);

        return ApiMessagesTemplate::createResponse(true, 200, "Purchase items readed successfully", $quantityItems);
       
    }
}
