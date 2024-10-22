<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\DrugRequest;
use App\Http\Resources\DrugResource;
use App\Models\Drug;
use App\Models\PurchaseItem;
use App\Http\Repository\DrugRepository;
use Illuminate\Http\Request;

class DrugController extends Controller
{
    public function search (Request $request) {
        return Drug::search($request->q)->get();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index(Request $request, ApiFilter $filter, ApiSort $sort, ApiField $field)
    public function index(Request $request, DrugRepository $drugRepository)
    {
       $fields = [
            'id' => 'drugs.id',
            "name" => "drugs.name",
            "brandname" => "drugs.brandname",
            "type" => "options.name",
            "description" => "drugs.description",
            "barcode" => "drugs.barcode",
            "middleunitnum" => "drugs.middleunitnum",
            "smallunitnum" => "drugs.smallunitnum",
            "visible" => "drugs.visible",
            "created_by" => "users.username",
            "created_at" => "drugs.created_at",
        ];
     
        $drugs = $drugRepository->fetchListOfItems($request, $fields);
        
        return ApiMessagesTemplate::createResponse(true, 200, "Drugs readed successfully", $drugs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DrugRequest $request)
    {
        $data = $request->validated();
        $isCreated = Drug::create($data);
        if ($isCreated)
            return ApiMessagesTemplate::createResponse(true, 201, "Drug added successfully", ["id" => $isCreated->id]);   
    
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
        $drug = new DrugResource(Drug::findOrFail($id));
        
        return ApiMessagesTemplate::createResponse(true, 200, "Drug readed successfully", $drug);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DrugRequest $request, $id)
    {
        $drug = Drug::findOrFail($id);

        $data = $request->validated();
        $isUpdated = $drug->update($data);
        if($isUpdated)
            return response()->json([], 204);

        return response()->json(['message' => 'Server Error'], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $drug = Drug::findOrFail($id);

        // Check if any purchase transaction happen on this Durg
        $checkDrugInPurchaseBill = PurchaseItem::where('drug_id', $id)->get();
        if($checkDrugInPurchaseBill->isNotEmpty())
            return ApiMessagesTemplate::createResponse(false, 422, "Drug not deletable after linked with purchase bill");

        $isDeleted = $drug->delete();
        if($isDeleted)
            return response()->json([$isDeleted], 204);

        return response()->json(['message' => 'Server Error'], 500);
    }
}
