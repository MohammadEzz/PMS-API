<?php
namespace App\Http\Repository;

use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiSort;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryRepository implements GeneralRepository {

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

        return $inventoryItems->toArray();
    }

    public function fetchQuantity(Request $request, array $fields) {
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

        $quantityItems = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

        return $quantityItems->toArray();
    }
}

?>