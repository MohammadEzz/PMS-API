<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Repository\DiseaseRepository;
use App\Http\Requests\DiseaseRequest;
use App\Models\Disease;
use Illuminate\Http\Request;

class DiseaseController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, DiseaseRepository $repository)
    {
        $fields = [
            'id' => 'diseases.id',
            "name" => "diseases.name",
            "globalname" => "diseases.globalname",
            "category" => "options.name as category",
        ];

        $diseases = $repository->fetchListOfItems($request, $fields);

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
        $data = $request->validated();

        $isCreated = Disease::query()->create([
            "categoryid" => $data['categoryid'],
            "name" => $data['name'],
            "globalname" => $data['globalname'],
        ]);

        if($isCreated)
            return ApiMessagesTemplate::createResponse(true, 201, "Diseases added successfully", ["id" => $isCreated->id]);
        
        return response()->json(['message' => 'Server Error'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $disease = Disease::findOrFail($id);

        return ApiMessagesTemplate::createResponse(true, 200, "Disease readed successfully", $disease); 
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
        $disease = Disease::findOrFail($id);

        if($disease) {

            $data = $request->validated();

            $isUpdated = $disease->update([
                'categoryid' => $data['categoryid'],
                'name' => $data['name'],
                'globalname' => $data['globalname'],
            ]);
            
            if($isUpdated)
                return response()->json([], 204);

            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $disease = Disease::findOrFail($id);

        $isDeleted = $disease->delete();
        
        if ($isDeleted)
            return response()->json([], 204);
        
        return response()->json(['message' => 'Server Error'], 500);
    }
}
