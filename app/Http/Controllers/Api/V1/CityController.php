<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Models\City;
use Exception;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ApiSort $sort, ApiField $field, ApiFilter $filter)
    {
            $fields = [
                "id" => "id",
                "name" => "name",
                "areacode" => "areacode",
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
            $query = City::select($fieldParams);

            // Where
            $filterParams && $query->WhereRaw($filterParams, $queryParams);

            // Order By | Sorting
            if(count($sortParams) > 0) {
                foreach($sortParams as $value){
                    [$field, $sortType] = explode('.', $value);
                    $query->orderBy($field, $sortType);
                }
            }
            else
                $query->orderBy('name', 'asc');            
           
            $options = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

            return ApiMessagesTemplate::createResponse(true, 200, "Cities readed successfully", $options);
    }
}