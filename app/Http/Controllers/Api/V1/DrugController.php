<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Http\Requests\DrugRequest;
use App\Models\Drug;
use App\Models\PurchaseItem;
use ArrayObject;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function index(Request $request, ApiFilter $filter, ApiSort $sort, ApiField $field)
    {

       $fields = [
            'id' => 'drugs.id',
            "name" => "drugs.name",
            "brandname" => "drugs.brandname",
            "type" => "options.name as type",
            "description" => "drugs.description",
            "barcode" => "drugs.barcode",
            "middleunitnum" => "drugs.middleunitnum",
            "smallunitnum" => "drugs.smallunitnum",
            "visible" => "drugs.visible",
            "createdby" => "users.firstname",
            "created_at" => "drugs.created_at",
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
        $query = Drug::query()
        ->join('options', 'drugs.type', '=', 'options.id')            
        ->leftJoin('purchaseitems', 'purchaseitems.drug_id', '=', 'drugs.id')
        ->leftJoin('users', 'users.id', '=', 'drugs.created_by')
        ->select([...$fieldParams, DB::raw('max(purchaseitems.quantity) as maxbillqty')]);
        
        // Where
        $filterParams && $query->WhereRaw($filterParams, $queryParams);

        // Order By | Sort
        if(count($sortParams) > 0) {
            foreach($sortParams as $value){
                [$field, $sortType] = explode('.', $value);
                $query->orderBy($field, $sortType);
            }
        }
        else {
            $query->orderBy('drugs.id', 'desc');
        }

        // Grouping
        $query->groupBy('drugs.id');  

        $drugs = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);
        
        return ApiMessagesTemplate::apiResponseDefaultMessage(true, 200, "Drugs readed successfully", $drugs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DrugRequest $request)
    {
        $data = $request->input();
        $isCreated = Drug::create([
            "name" => $data['name'],
            "brandname" => $data['brandname'],
            "type" => $data['type'],
            "description" => $data['description'],
            "barcode" => $data['barcode'],
            "middleunitnum" => $data['middleunitnum'],
            "smallunitnum" => $data['smallunitnum'],
            "visible" => $data['visible'],
            "created_by" => $data['created_by'],
        ]);

        if($isCreated)
            return ApiMessagesTemplate::apiResponseDefaultMessage(true, 200, "Drug added successfully", $isCreated);
        else
            return ApiMessagesTemplate::apiResponseDefaultMessage(false, 400, "Drug added failed");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $drug = Drug::find($id);

        if($drug)
            return ApiMessagesTemplate::createResponse(true, 200, "Drug readed successfully", $drug);
        else
            return ApiMessagesTemplate::createResponse(false, 404, "Drug not exist");
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
        $drug = Drug::find($id);

        if($drug) {
            $drug->name = $request->input('name');
            $drug->brandname = $request->input('brandname');
            $drug->type = $request->input('type');
            $drug->description = $request->input('description');
            $drug->barcode = $request->input('barcode');
            $drug->middleunitnum = $request->input('middleunitnum');
            $drug->smallunitnum = $request->input('smallunitnum');
            $drug->visible = $request->input('visible');
            $drug->created_by = $request->input('created_by');
            $isUpdated = $drug->save();

            if($isUpdated)
                return ApiMessagesTemplate::apiResponseDefaultMessage(true, 204, "Drug updated successfully");
            else
                return ApiMessagesTemplate::apiResponseDefaultMessage(false, 400, "Drug updated failed");
        }
        else {
            return ApiMessagesTemplate::apiResponseDefaultMessage(false, 404, "Drug not exist");
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
        $drug = Drug::find($id);

        if($drug) {
            // Check if any purchase transaction happen on this Durg
            $checkDrugInPurchaseBill = PurchaseItem::query()->where('drug_id', $id)->get();
            if($checkDrugInPurchaseBill->isNotEmpty()) {
                return ApiMessagesTemplate::createResponse(false, 503, "Drug not deletable after linked with purchase bill");
            }

            $isDeleted = Drug::find($id)->delete();
            if($isDeleted) {
                return ApiMessagesTemplate::apiResponseDefaultMessage(true, 204, "Drug deleted successfully");
            }
            else {
                return ApiMessagesTemplate::apiResponseDefaultMessage(false, 400, "Drug deleted failed");
            }
        }
        else {
            return ApiMessagesTemplate::apiResponseDefaultMessage(false, 404, "Drug with id: $id not exist");
        }
    }
}
