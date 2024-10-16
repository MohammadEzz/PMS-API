<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Models\Dealer;
use Illuminate\Http\Request;

class DealerController extends Controller
{
    public function index(Request $request, ApiSort $sort, ApiField $field, ApiFilter $filter)
    {
        $fields = [
            "id" => "id",
            'supplier' => 'supplier_id',
            "name" => "name",
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

        // Select
        $query = Dealer::select($fieldParams);

        // Where
        $filterParams && $query->WhereRaw($filterParams, $queryParams);

        // Order By | Sorting
        if(count($sortParams) > 0) {
            foreach($sortParams as $value){
                [$field, $sortType] = explode('.', $value);
                $query->orderBy($field, $sortType);
            }
        }
        else $query->orderBy('name', 'asc');
            
        $options = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

        return ApiMessagesTemplate::createResponse(true, 200, "Dealers Readed Successfully", $options);
    }
}
