<?php
namespace App\Http\Repository;

use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiSort;
use App\Models\User;
use Illuminate\Http\Request;

class UserRepository implements GeneralRepository {

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

        //Select & Joins
        $query = User::select($fieldParams)
        ->join('countries', 'countries.id', '=', 'users.country')
        ->join('options', 'options.id', '=', 'users.status')
        ->leftJoin('cities', 'cities.id', '=', 'users.city')
        ->leftJoin('users as admin', 'admin.id', '=', 'users.created_by');

        // Where
        $filterParams && $query->WhereRaw($filterParams, $queryParams);

        // Order By | Sorting
        if(is_array($sortParams) && count($sortParams) > 0) {
            foreach($sortParams as $value){
                [$field, $sortType] = explode('.', $value);
                $field = $fields[$field];
                $query->orderBy($field, $sortType);
            }
        }
        else $query->orderBy('users.id', 'desc');

        $users = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

        return $users->toArray();
    }
}

?>