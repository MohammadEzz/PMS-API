<?php
namespace App\Http\Repository;

use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiSort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceRepository implements GeneralRepository {

    private ApiField $field;
    private ApiFilter $filter;
    private ApiSort $sort;

    public function __construct(ApiField $field, ApiFilter $filter, ApiSort $sort)
    {
        $this->field = $field;
        $this->filter = $filter;
        $this->sort = $sort;
    }

    public function fetchListOfItems(Request $request, array $fields): array
    {
        
        $fieldParams = $fields;
        $filterParams = '';
        $sortParams = [];
        $rangeParams = 'all';

        if($request->has('fields')) {
            $urlFields = $request->query('fields');
            $fieldParams = $this->field->buildFields($urlFields, $fields);
        }
        else
            $fieldParams = $this->field->aliasFieldsName($fields);

        if($request->has('filter')) {
            $urlFilter = $request->query('filter');
            [$filterParams, $queryParams] = $this->filter->buildFilter($urlFilter, $fields);
        }
        
        if($request->has('sort')) {
            $urlSort = $request->query('sort');
            $sortParams = $this->sort->buidlSort($urlSort, $fields);
        }

        if($request->has('range')) {
            $urlRange = $request->query('range');
            $rangeParams = strtolower($urlRange);
        }

        // Select & Join
        $query = DB::table(function($q){
            $q->selectRaw('drug_id, name, brandname, max(prices.id) as id')
            ->join('drugs', 'drug_id', '=', 'drugs.id')
            ->from('prices')
            ->groupBy('drug_id');
        }, 'sub')
        ->join('prices as p2', 'p2.id', '=', 'sub.id')
        ->select($fieldParams);

        // Where
        $filterParams && $query->WhereRaw($filterParams, $queryParams);

        // Order By | Sort
        foreach($sortParams as $value){
            [$field, $sortType] = explode('.', $value);
            $field = $fields[$field];
            $query->orderBy($field, $sortType);
        }
        
        $prices = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

        return $prices->toArray();
    }
}

?>