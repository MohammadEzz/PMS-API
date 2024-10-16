<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Models\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceController extends Controller
{
    public function index(Request $request, ApiSort $sort, ApiField $field, ApiFilter $filter)
    {
        $fields = [
            'drug_id'=>"sub.drug_id",
            "name" => "sub.name",
            "brandname" => "sub.brandname",
            "price" => "p2.price"
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

        $query = DB::table(function($q){
            $q->selectRaw('drug_id, name, brandname, max(prices.id) as id')
            ->join('drugs', 'drug_id', '=', 'drugs.id')
            ->from('prices')
            ->groupBy('drug_id');
        }, 'sub')
        ->join('prices as p2', 'p2.id', '=', 'sub.id')
        ->select($fieldParams);

        $filterParams && $query->WhereRaw($filterParams, $queryParams);
        if(count($sortParams) > 0) {
            foreach($sortParams as $value){
                [$field, $sortType] = explode('.', $value);
                $query->orderBy($field, $sortType);
            }
        }
        else $query
        ->orderBy('name', 'asc');

            $inventoryItems = $query->get();
        
        $inventoryItems = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

        return ApiMessagesTemplate::createResponse(true, 200, "Prices items readed successfully", $inventoryItems);
    }
}