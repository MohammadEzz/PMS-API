<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Http\Requests\ActiveIngredientRequest;
use App\Models\ActiveIngredient;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ActiveIngredientController extends Controller
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
            "globalname" => "globalname",
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

        $query = ActiveIngredient::select($fieldParams);

        $filterParams && $query->WhereRaw($filterParams, $queryParams);

        if(count($sortParams) > 0) {
            foreach($sortParams as $value){
                [$field, $sortType] = explode('.', $value);
                $query->orderBy($field, $sortType);
            }
        }
        else {
            $query->orderBy('id', 'asc');
        }

        $activeIngredients = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);
          

        return ApiMessagesTemplate::createResponse(true, 200, "Active Ingredients Readed Successfully", $activeIngredients);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ActiveIngredientRequest $request)
    {
        $data = $request->only(["name", "globalname"]);

        $isCreated = ActiveIngredient::create([
            "name" => $data['name'],
            "globalname" => $data['globalname'],
        ]);

        if($isCreated)
            return ApiMessagesTemplate::createResponse(true, 200, "Active ingredient added successfully", true);
        else
            return ApiMessagesTemplate::createResponse(false, 400, "Active ingredient added failed", true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $activeIngredient = ActiveIngredient::find($id);

        if($activeIngredient)
            return ApiMessagesTemplate::createResponse(true, 200, "Active ingredient readed successfully", $activeIngredient);
        else 
            return ApiMessagesTemplate::createResponse(false, 404, "Active ingredient not exist");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ActiveIngredientRequest $request, $id)
    {
        $data = $request->only(["name", "globalname"]);
        $activeIngredient = ActiveIngredient::find($id);

        if($activeIngredient) {
            $activeIngredient->name = $data['name'];
            $activeIngredient->globalname = $data['globalname'];
            $isUpdated = $activeIngredient->save();

            if($isUpdated)
                return ApiMessagesTemplate::createResponse(true, 204, "Active ingredient updated successfully");
            else
                return ApiMessagesTemplate::createResponse(true, 400, "Active ingredient updated failed");
        }
        else 
            return ApiMessagesTemplate::createResponse(false, 404, "Active ingredient not exist");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $activeIngredient = ActiveIngredient::find($id);

        if($activeIngredient) {

            $isDeleted = $activeIngredient->delete();

            if($isDeleted)
                return ApiMessagesTemplate::createResponse(true, 204, "Active ingredient deleted successfully");
            else
                return ApiMessagesTemplate::createResponse(true, 400, "Active ingredient deleted failed");
        }
        else
            return ApiMessagesTemplate::createResponse(false, 404, "Active ingredient not exist");
    }
}
