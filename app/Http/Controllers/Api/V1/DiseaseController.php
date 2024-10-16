<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiSort;
use App\Http\Requests\DiseaseRequest;
use App\Models\Disease;
use ArrayObject;
use Exception;
use Illuminate\Http\Request;

class DiseaseController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ApiFilter $filter, ApiSort $sort, ApiField $field)
    {
        $fields = [
            'id' => 'diseases.id',
            "name" => "diseases.name",
            "globalname" => "diseases.globalname",
            "category" => "options.name as category",
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
        $query = Disease::select($fieldParams)
        ->join('options', 'options.id', '=', 'categoryid');

        // Where
        $filterParams && $query->WhereRaw($filterParams, $queryParams);

        // Order By | Sort
        foreach($sortParams as $value){
            [$field, $sortType] = explode('.', $value);
            $field = $fields[$field];
            $query->orderBy($field, $sortType);
        }
       
        $diseases = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

        return ApiMessagesTemplate::createResponse(true, 200, "Diseases readed successfully", $diseases);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DiseaseRequest $request)
    {
        $data = $request->only(['categoryid', 'name', 'globalname']);

        $isCreated = Disease::query()->create([
            "categoryid" => $data['categoryid'],
            "name" => $data['name'],
            "globalname" => $data['globalname'],
        ]);

        if($isCreated)
            return ApiMessagesTemplate::createResponse(true, 200, "Diseases added successfully", $isCreated);
        else
            return ApiMessagesTemplate::createResponse(false, 500, "Diseases added failed");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $disease = Disease::find($id);

        if($disease)
            return ApiMessagesTemplate::createResponse(true, 200, "Disease readed successfully", $disease);
        else
            return ApiMessagesTemplate::createResponse(false, 404, "Disease not exist");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DiseaseRequest $request, $id)
    {
        $disease = Disease::find($id);

        if($disease) {

            $data = $request->only(['categoryid', 'name', 'globalname']);
            $isUpdated = $disease->update([
                'categoryid' => $data['categoryid'],
                'name' => $data['name'],
                'globalname' => $data['globalname'],
            ]);

            if($isUpdated)
                return ApiMessagesTemplate::createResponse(true, 201, "Disease updated successfully");
            else
                return ApiMessagesTemplate::createResponse(false, 500, "Disease updated failed");
        }
        else
            return ApiMessagesTemplate::createResponse(false, 404, "Disease not exist");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $disease = Disease::find($id);

        if($disease) {
            $isDeleted = $disease->delete();

            if ($isDeleted)
                return ApiMessagesTemplate::createResponse(true, 201, "Disease deleted successfully");
            else
                return ApiMessagesTemplate::createResponse(false, 500, "Disease deleted failed");
        }
        else
            return ApiMessagesTemplate::createResponse(false, 404, "Disease not exist");
    }
}
