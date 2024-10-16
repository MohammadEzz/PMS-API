<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\PurchaseReturnItemRequest;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturnBill;
use App\Models\PurchaseReturnItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiSort;
use App\Http\Helpers\Api\ApiFilter;

class PurchaseReturnItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ApiSort $sort, ApiField $field, ApiFilter $filter)
    {
        $fields = [
            "id" => "purchasereturnitems.id",
            "purchasereturnbill_id" => "purchasereturnbill_id",
            "purchaseitem_id" => "purchasereturnitems.purchaseitem_id",
            "drug_id"=>"drugs.id as drug_id",
            "drugname"=>"drugs.name",
            "drugbarcode"=>"drugs.barcode",
            "quantity" => "purchasereturnitems.quantity",
            "price" => "purchasereturnitems.price",
            "inventoryqty"=>"inventory.quantity as inventoryqty",
            "expiredate"=>"purchaseitems.expiredate",
            "tax"=>"purchaseitems.tax",
            "discount"=>"purchaseitems.discount",
            "purchaseprice"=>"purchaseitems.purchaseprice",
            "created_by" => "purchasereturnitems.created_by",
            "updated_by" => "purchasereturnitems.updated_by",
            "created_at" => "purchasereturnitems.created_at",
            "updated_at" => "purchasereturnitems.updated_at",
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
        $query = PurchaseReturnItem::query()->select($fieldParams)
        ->join('purchasereturnbills', 'purchasereturnbills.id', '=', 'purchasereturnbill_id')
        ->join('purchaseitems', 'purchaseitems.id', '=', 'purchasereturnitems.purchaseitem_id')
        ->join('inventory', 'inventory.purchaseitem_id', '=', 'purchasereturnitems.purchaseitem_id')
        ->join('drugs', 'drugs.id', '=', 'inventory.drug_id');

        // Where
        $filterParams && $query->WhereRaw($filterParams, $queryParams);

        //Order By | Sorting
        if(count($sortParams) > 0) {
            foreach($sortParams as $value){
                [$field, $sortType] = explode('.', $value);
                $query->orderBy($field, $sortType);
            }
        }
        else $query->orderBy('purchasereturnitems.id', 'asc');
        
        $purchaseReturnItems = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);
        
        return ApiMessagesTemplate::createResponse(true, 200, "Purchase return items readed successfully", $purchaseReturnItems);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchaseReturnItemRequest $request)
    {
        $data = $request->only([
            'purchasereturnbill_id',
            'purchaseitem_id',
            'quantity',
            'price',
        ]);
        $data['created_by'] = 1;

        $purchaseReturnBill = PurchaseReturnBill::findOrFail($data['purchasereturnbill_id']);
        $purchaseItem = PurchaseItem::findOrFail($data['purchaseitem_id']);
        $purchaseBill = $purchaseItem->bill()->first();

        if($purchaseBill->editable === 1){
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Add Purchase Item From Non Approved Purchase Bill"); 
        }

        if($purchaseReturnBill->editable === 0) {
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Add Purchase Return Item To Approved Purchase Return Bill");
        }

        DB::beginTransaction();
        try {
            $purchaseReturnBill = DB::table('purchasereturnbills')->where('id', $data['purchasereturnbill_id'])->lockForUpdate()->first();
            $purchaseReturnBillItem = PurchaseReturnItem::create($data);
            $returnBillTotal = $purchaseReturnBill->total + ($data['quantity'] * $data['price']);
            DB::table('purchasereturnbills')
            ->where('id', $purchaseReturnBill->id)
            ->update(['total' => $returnBillTotal]);
            
            DB::commit();
            $purchaseReturnBillItem->totalreturnbill = $returnBillTotal;
            return ApiMessagesTemplate::createResponse(true, 201, "Purchase Return Item Created Successfully", $purchaseReturnBillItem);   
        }
        catch(Exception $e) {
            DB::rollBack();
            return ApiMessagesTemplate::createResponse(false, 503, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) 
    {

        $purchaseReturnItem = PurchaseReturnItem::find($id);
        if($purchaseReturnItem)
            return ApiMessagesTemplate::createResponse(true, 200, "Purchase Return Item Readed Successfully", $purchaseReturnItem);  
        else
            return ApiMessagesTemplate::createResponse(true, 404, "Purchase Return Item Not Exist", $purchaseReturnItem);        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PurchaseReturnItemRequest $request, $id)
    {
        $data = $request->only([
            'purchasereturnbill_id',
            'purchaseitem_id',
            'quantity',
            'price'
        ]);
        $data['updated_by'] = 1;
        
        $purchaseReturnItem = PurchaseReturnItem::find($id);
        $purchaseReturnBill = PurchaseReturnBill::find($data['purchasereturnbill_id']);

        if(!$purchaseReturnItem)
            return ApiMessagesTemplate::createResponse(true, 404, "Purchase Return Item Not Exist", $purchaseReturnItem);
        if(!$purchaseReturnBill)
            return ApiMessagesTemplate::createResponse(true, 404, "Purchase Return Bill Not Exist", $purchaseReturnItem);

        if($purchaseReturnBill->editable === 0) {
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Update Purchase Return Item On Approved Purchase Return Bill");
        }

        $previousPurchaseReturnItemTotal = $purchaseReturnItem->quantity * $purchaseReturnItem->price;
        $newPurchaseReturnItemTotal = (int)$data['quantity'] * (float)$data['price'];
        $newReturnBillTotal = $purchaseReturnBill->total - $previousPurchaseReturnItemTotal + $newPurchaseReturnItemTotal;
        
        DB::beginTransaction();
        try { 
            DB::table('purchasereturnitems')->where('id', $id)->lockForUpdate();
            DB::table('purchasereturnbills')->where('id', $data['purchasereturnbill_id'])->lockForUpdate();
            $purchaseReturnItem->update($data);
            $purchaseReturnBill->update(['total' => $newReturnBillTotal]);
           
            DB::commit();
            $purchaseReturnItem->totalreturnbill = $newReturnBillTotal;
            return ApiMessagesTemplate::createResponse(true, 200, "Purchase Return Item Updated Successfully", $purchaseReturnItem);
        }
        catch(Exception $e) {
            DB::rollBack();
            return ApiMessagesTemplate::createResponse(false, 503, $e->getMessage());
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
        $purchaseReturnItem = PurchaseReturnItem::findOrFail($id);
        $purchaseReturnBill = $purchaseReturnItem->bill()->first();
        
        if(!$purchaseReturnItem)
            return ApiMessagesTemplate::createResponse(true, 404, "Purchase Return Item Not Exist", $purchaseReturnItem);
        if(!$purchaseReturnBill)
            return ApiMessagesTemplate::createResponse(true, 404, "Purchase Return Bill Not Exist", $purchaseReturnItem);

        if($purchaseReturnBill->editable === 0) {
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Delete Purchase Return Item From Approved Purchase Return Bill");
        }

        DB::beginTransaction();
        try{
            DB::table('purchasereturnbills')->where('id', $purchaseReturnBill->id)->lockForUpdate();
            $newPurchaseReturnTotal = $purchaseReturnBill->total - ($purchaseReturnItem->quantity * $purchaseReturnItem->price);
            DB::table('purchasereturnbills')->where('id', $purchaseReturnBill->id)
            ->update(['total' => $newPurchaseReturnTotal]);
            $purchaseReturnItem->delete();

            DB::commit();
            return ApiMessagesTemplate::createResponse(true, 200, "Purchase Return Item Deleted Successfully", ["totalreturnbill" => $newPurchaseReturnTotal]);
        }
        catch(Exception $e) {
            DB::rollBack();
            return ApiMessagesTemplate::createResponse(false, 503, $e->getMessage());
        }
    }
}
