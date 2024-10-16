<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Http\Requests\DrugInteractionRequest;
use App\Models\DrugInteraction;
use Illuminate\Http\Request;

class DrugInteractionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ApiFilter $filter, ApiSort $sort, ApiField $field)
    {

        $fields = [
            'id' => 'druginteractions.id',
            'activeingredient1' => 'ai1.name as activeingredient1',
            'activeingredient2' => 'ai2.name as activeingredient2',
            'level' => 'options.name as level',
            'description' => 'druginteractions.description',
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

        // Select & Join
        $query = DrugInteraction::
        join('activeingredients as ai1', 'ai1.id', '=', 'activeingredient1')
        ->join('activeingredients as ai2', 'ai2.id', '=', 'activeingredient2')
        ->join('options', 'druginteractions.level', '=', 'options.id')
        ->select($fieldParams);

        // Where
        $filterParams && $query->WhereRaw($filterParams, $queryParams);

        // Order By | Sort
        if(count($sortParams) > 0) {
            foreach($sortParams as $value) {
                [$field, $sortType] = explode('.', $value);
                $field = $this->mapSortParamWithTableField($field);
                $query->orderBy($field, $sortType);
            }
        }
        else {
            $query->orderBy('druginteractions.id', 'desc');
        }
       
        $drugInteractions = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

        return ApiMessagesTemplate::createResponse(true, 200, "Drug interactions readed successfully", $drugInteractions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DrugInteractionRequest $request)
    {
        $data = $request->only(['activeingredient1', 'activeingredient2', 'level', 'description']);

        $drugInteraction = new DrugInteraction();
        $drugInteraction->activeingredient1 = $data['activeingredient1'];
        $drugInteraction->activeingredient2 = $data['activeingredient2'];
        $drugInteraction->level = $data['level'];
        $drugInteraction->description = $data['description'];
        $isCreated = $drugInteraction->save();

        if($isCreated)
            return ApiMessagesTemplate::createResponse(true, 201, "Drug interactions added successfully", $drugInteraction);
        else
            return ApiMessagesTemplate::createResponse(false, 400, "Drug interactions added failed");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $drugInteraction = DrugInteraction::find($id);

        if($drugInteraction)
            return ApiMessagesTemplate::createResponse(true, 200, "Drug interaction readed successfully", $drugInteraction);
        else
            return ApiMessagesTemplate::createResponse(true, 404, "Drug interaction readed failed");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DrugInteractionRequest $request, $id)
    {
        $data = $request->only(['activeingredient1', 'activeingredient2', 'level', 'description']);

        $drugInteraction = DrugInteraction::find($id);

        if($drugInteraction) {
            $drugInteraction->activeingredient1 = $data['activeingredient1'];
            $drugInteraction->activeingredient2 = $data['activeingredient2'];
            $drugInteraction->level = $data['level'];
            $drugInteraction->description = $data['description'];
            $isUpdated = $drugInteraction->save();

            if($isUpdated)
                return ApiMessagesTemplate::createResponse(true, 204, "Drug interaction updated successfully");
            else    
                return ApiMessagesTemplate::createResponse(false, 400, "Drug interaction updated failed");
        }
        else
            return ApiMessagesTemplate::createResponse(true, 404, "Drug interaction not exist");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $drugInteraction = DrugInteraction::find($id);

        if($drugInteraction) {
            $isDeleted = $drugInteraction->delete();

            if($isDeleted)
                return ApiMessagesTemplate::createResponse(true, 204, "Drug interaction deleted successfully");
            else
                return ApiMessagesTemplate::createResponse(true, 400, "Drug interaction deleted failed");
        }
        else {
            return ApiMessagesTemplate::createResponse(true, 404, "Drug interaction not exist");
        }
    }
}
