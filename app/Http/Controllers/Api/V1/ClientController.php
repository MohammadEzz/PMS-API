<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request, ApiSort $sort, ApiField $field, ApiFilter $filter)
    {
            $mapArray = [
                "id" => "id",
                "name" => "name",
            ];
            $queryString = $request->query("filter");
            list($filterquery, $queryParams) = $filter->buildQuery($queryString, $mapArray);

            $sortParams = $sort->buidlSortByQuery($request);
            $fieldParams = $field->buidlFieldQuery($request, $mapArray);

            if($request->query('range') === 'all') {
                $query = Client::select($fieldParams);
                $filterquery && $query->WhereRaw($filterquery, $queryParams);
                if(count($sortParams) > 0) {
                    foreach($sortParams as $value){
                        [$field, $sortType] = explode('.', $value);
                        $query->orderBy($field, $sortType);
                    }
                }
                else $query->orderBy('name', 'asc');

                $options = $query->get();
            }
            else {
                $query = Client::select($fieldParams);
                $filterquery && $query->WhereRaw($filterquery, $queryParams);

                if(is_array($sortParams) && count($sortParams) > 0) {
                    foreach($sortParams as $value){
                        [$field, $sortType] = explode('.', $value);
                        $query->orderBy($field, $sortType);
                    }
                }
                else $query->orderBy('name', 'asc');

                $options = $query->paginate($request->query('range') ?? 20);
            }
            return ApiMessagesTemplate::createResponse(true, 200, "Supplier Readed Successfully", $options);
    }
}
