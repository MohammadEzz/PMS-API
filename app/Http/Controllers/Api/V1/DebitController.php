<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Models\Debit;
use Illuminate\Http\Request;

class DebitController extends Controller
{
    public function index(Request $request,  ApiSort $sort, ApiField $field, ApiFilter $filter) {

        $fields = [
            'id'=>"debits.id",
            'supplier_id'=>'creditor_id',
            "name" => "name",
            "amount" => "amount",
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
        $query = Debit::query()
        ->select($fieldParams)
        ->join('suppliers', function($join){
            $join->on('suppliers.id', '=', 'creditor_id')
            ->where('creditor_type', '=', 'supplier');
        });

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
        
        $debitItems = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

        return ApiMessagesTemplate::createResponse(true, 200, "Debits items readed successfully", $debitItems);
    }
}
