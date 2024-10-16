<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Helpers\Api\ApiSort;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
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
    public function index(Request $request, ApiSort $sort, ApiField $field, ApiFilter $filter)
    {
        $fields = [
            "id" => "inventory.id",
            'purchaseitem_id'=>"purchaseitem_id",
            "drug_id" => "drugs.id as drug_id",
            "drugname" => "drugs.name",
            "drugbarcode" => "drugs.barcode",
            "quantity" => "inventory.quantity",
            "purchaseprice" => "purchaseprice",
            "tax" => "tax",
            "discount" => "discount",
            "expiredate" => "inventory.expiredate",
        ];

        $fieldParams = $fields;
        $filterParams = '';
        $sortParams = [];
        $rangeParams = 20;

        if($request->has('fields')) {
            $urlFields = $request->query('fields');
            $fieldParams = $field->buildFields($urlFields, $fields);
        }

        if($request->has('filter')) {
            $urlFilter = $request->query('filter');
            [$filterParams, $queryParams] = $filter->buildFilter($urlFilter, $fields);
        }
        
        if($request->has('sort')) {
            $urlSort = $request->query('sort');
            $sortParams = $sort->buidlSort($urlSort, $fields);
        }

        if($request->has('range')) {
            $urlRange = $request->query('range');
            $rangeParams = strtolower($urlRange);
        }

        // Select & Joins
        $query = Inventory::query()->select($fieldParams)
        ->join('drugs', 'inventory.drug_id', '=', 'drugs.id')
        ->join('purchaseitems', 'purchaseitems.id', '=', 'inventory.purchaseitem_id');

        // Where
        $filterParams && $query->WhereRaw($filterParams, $queryParams);

        // Order By | Sort
        if(count($sortParams) > 0) {
            foreach($sortParams as $value){
                [$field, $sortType] = explode('.', $value);
                $query->orderBy($field, $sortType);
            }
        }
        else $query->orderBy('id', 'desc');

        $inventoryItems = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);
        
        return ApiMessagesTemplate::createResponse(true, 200, "Purchase items readed successfully", $inventoryItems);
    }

    public function quantity(Request $request, ApiSort $sort, ApiField $field, ApiFilter $filter) {
        $fields = [
            'drug_id'=>"drug_id",
            "name" => "name",
            "brandname" => "brandname",
            "middleunitnum" => "middleunitnum",
            "smallunitnum" => "smallunitnum",
        ];

        $fieldParams = $fields;
        $filterParams = '';
        $sortParams = [];
        $rangeParams = 20;

        if($request->has('fields')) {
            $urlFields = $request->query('fields');
            $fieldParams = $field->buildFields($urlFields, $fields);
        }

        if($request->has('filter')) {
            $urlFilter = $request->query('filter');
            [$filterParams, $queryParams] = $filter->buildFilter($urlFilter, $fields);
        }
        
        if($request->has('sort')) {
            $urlSort = $request->query('sort');
            $sortParams = $sort->buidlSort($urlSort, $fields);
        }

        if($request->has('range')) {
            $urlRange = $request->query('range');
            $rangeParams = strtolower($urlRange);
        }

        // Select & Join
        $query = Inventory::query()
        ->select([...$fieldParams, DB::raw('sum(quantity) as quantity')])
        ->join('drugs', 'drugs.id', '=', 'drug_id');

        // Where
        $filterParams && $query->WhereRaw($filterParams, $queryParams);

        // Group By
        $query->groupBy('drug_id');

        // Order By | Sort
        if(count($sortParams) > 0) {
            foreach($sortParams as $value){
                [$field, $sortType] = explode('.', $value);
                $query->orderBy($field, $sortType);
            }
        }
        else $query->orderBy('name', 'asc');
        
        $inventoryItems = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

        return ApiMessagesTemplate::createResponse(true, 200, "Purchase items readed successfully", $inventoryItems);
       
    }
}
