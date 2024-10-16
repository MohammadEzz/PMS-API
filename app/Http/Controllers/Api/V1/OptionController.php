<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
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
    public function index(Request $request, ApiFilter $filter, ApiSort $sort, ApiField $field)
    {

        $fields = [
            'id' => 'id',
            "name" => "name",
            "type" => "type",
            "order" => "order",
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
            [$filterParams, $queryParams] = $filter->buildFilter($request->query('filter'), $fields);
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
        $query = Option::select($fieldParams);

        // Where
        $filterParams && $query->WhereRaw($filterParams, $queryParams);

        // Order By | Sort
        if(count($sortParams) > 0) {
            foreach($sortParams as $value){
                [$field, $sortType] = explode('.', $value);
                $query->orderBy($field, $sortType);
            }
        }
        else {
            $query->orderBy('order', 'asc');
        }
        
        // Limit
        $options = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

        return ApiMessagesTemplate::apiResponseDefaultMessage(true, 200, "Options Readed Successfully", $options);
    }
}
