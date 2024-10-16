<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\PurchaseReturnBillRequest;
use App\Models\Debit;
use App\Models\Inventory;
use App\Models\PurchaseBill;
use App\Models\PurchaseReturnBill;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiSort;
use App\Http\Helpers\Api\ApiFilter;

class PurchaseReturnBillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ApiSort $sort, ApiField $field, ApiFilter $filter)
    {
        $fields = [
            "id" => "purchasereturnbills.id",
            'purchasebill_id'=> 'purchasereturnbills.purchasebill_id',
            'billnumber'=> 'purchasebills.billnumber',
            "supplier" => "suppliers.name",
            "supplier_id" => "purchasebills.supplier_id",
            "issuedate" => "purchasereturnbills.issuedate",
            "billstatus" => "purchasereturnbills.billstatus",
            "total" => "purchasereturnbills.total",
            "created_by" => "purchasereturnbills.created_by",
            "updated_by" => "purchasereturnbills.updated_by",
            "created_at" => "purchasereturnbills.created_at",
            "updated_at" => "purchasereturnbills.updated_at",
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
        $query = PurchaseReturnBill::query()->select($fieldParams)
        ->join('purchasebills', 'purchasebills.id', '=', 'purchasereturnbills.purchasebill_id')
        ->leftJoin('suppliers', 'purchasebills.supplier_id', '=', 'suppliers.id');

        // Where
        $filterParams && $query->WhereRaw($filterParams, $queryParams);

        // Order By | Sorting
        if(count($sortParams) > 0) {
            foreach($sortParams as $value){
                [$field, $sortType] = explode('.', $value);
                $query->orderBy($field, $sortType);
            }
        }
        else $query
        ->orderBy('purchasereturnbills.issuedate', 'desc')
        ->orderBy('id', 'desc');
        
        $purchseReturnBills = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

        return ApiMessagesTemplate::createResponse(true, 200, "Purchase Return Bills Readed Successfully", $purchseReturnBills);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchaseReturnBillRequest $request)
    {
        $data = $request->only(['purchasebill_id', 'issuedate']);
        $data['created_by'] = 1;

        $purchaseBill = PurchaseBill::find($data['purchasebill_id']);

        if(!$purchaseBill)
            return ApiMessagesTemplate::createResponse(false, 404, "Purchase Bill Not Exist");

        if($purchaseBill->editable === 1) {
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Link Purchase Return Bill With Non Approved Purchase Bill");
        }

        $isCreated = PurchaseReturnBill::create($data); 
        if($isCreated)
            return ApiMessagesTemplate::createResponse(true, 201, "Purchase Return Bill Created Successfully", $isCreated);
        else
            return ApiMessagesTemplate::createResponse(true, 400, "Purchase Return Bill Created failed");
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $purchaseReturnBill = PurchaseReturnBill::find($id);
        return ApiMessagesTemplate::createResponse(true, 200, "Purchase Return Bill Readed Successfully", $purchaseReturnBill);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PurchaseReturnBillRequest $request, $id)
    {
        $data = $request->only(['purchasebill_id', 'issuedate']);

        $purchaseReturnBill = PurchaseReturnBill::find($id);

        if(!$purchaseReturnBill)
            return ApiMessagesTemplate::createResponse(false, 404, "Purchase Return Bill Not Exist");

        if($purchaseReturnBill->editable === 0)
            return ApiMessagesTemplate::createResponse(false, 405, "Purchase Return Bill Not Editable After Approved");

        $isUpdated = $purchaseReturnBill->update($data);

        if($isUpdated)
            return ApiMessagesTemplate::createResponse(true, 204, "Purchase Return Bill Updated Successfully");
        else
            return ApiMessagesTemplate::createResponse(false, 503, "Purchase Return Bill Updated Failed");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $purchaseReturnBill = PurchaseReturnBill::find($id);
        
        if(!$purchaseReturnBill)
            return ApiMessagesTemplate::createResponse(false, 404, "Purchase Return Bill Not Exist");

        if($purchaseReturnBill->editable === 0)
            return ApiMessagesTemplate::createResponse(false, 405, "Purchase Return Bill Not Deleteable After Approved");

        $isDeleted = $purchaseReturnBill->delete();

        if($isDeleted)
            return ApiMessagesTemplate::createResponse(false, 204, "Purchase Return Bill Deleted Successfully");
        else
            return ApiMessagesTemplate::createResponse(false, 400, "Purchase Return Bill Deleted failed");
    }

    public function approveBill($id) {

        $purchaseReturnBill = PurchaseReturnBill::findOrFail($id);
        $purchaseBill = $purchaseReturnBill->purchaseBill()->first();
        $purchaseReturnItems = $purchaseReturnBill->items()->get();

        foreach($purchaseReturnItems as $purchaseReturnItem){
            $itemInventory = Inventory::where('purchaseitem_id', $purchaseReturnItem->purchaseitem_id)->first();
            if($purchaseReturnItem->quantity > $itemInventory->quantity) 
                return ApiMessagesTemplate::createResponse(false, 405, "Quantities on Purchase Return Bill Greater Than Purchase Bill");
        }
        DB::beginTransaction();
        try{
            $supplierDebit = DB::table('debits')->where([
                ['creditor_id', '=', $purchaseBill->supplier_id],
                ['creditor_type', '=', 'supplier'],
            ])->lockForUpdate()->first();
            DB::table('debits')
            ->where([
                ['creditor_id', '=', $purchaseBill->supplier_id],
                ['creditor_type', '=', 'supplier'],
            ])
            ->update(['amount' => ($supplierDebit->amount - $purchaseReturnBill->total)]);
            
            foreach($purchaseReturnItems as $purchaseReturnItem) {
                $itemInventory = DB::table('inventory')->where('purchaseitem_id', '=', $purchaseReturnItem->purchaseitem_id)->lockForUpdate()->first();
                DB::table('inventory')->where('purchaseitem_id', '=', $purchaseReturnItem->purchaseitem_id)->update(['quantity' => $itemInventory->quantity - $purchaseReturnItem->quantity]);
            }

            $purchaseReturnBill->update(['billstatus' => 'approved', 'editable' => 0, 'updated_by' => 1]);
            
            DB::commit();
            return ApiMessagesTemplate::createResponse(true, 201, 'Purchase Return Bill Approved Successfully');
        }
        catch(Exception $e) {
            DB::rollBack();
            return ApiMessagesTemplate::createResponse(false, 503, $e->getMessage());
        }
    }
}
