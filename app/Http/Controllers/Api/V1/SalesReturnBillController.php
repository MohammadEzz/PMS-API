<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\SalesReturnBillRequest;
use App\Models\Inventory;
use App\Models\SalesReturnBill;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReturnBillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $salesReturnBills = SalesReturnBill::all();
        return ApiMessagesTemplate::createResponse(true, 200, "Sales Return Bills Readed Successfully", $salesReturnBills);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $salesReturnBill = SalesReturnBill::create(['created_by' => 1]);
        return ApiMessagesTemplate::createResponse(true, 201, "Sales Return Bill Created Successfully", $salesReturnBill);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $salesReturnBill = SalesReturnBill::findOrFail($id);
        return ApiMessagesTemplate::createResponse(true, 200, "Sales Return Bill Readed Successfully", $salesReturnBill);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SalesReturnBillRequest $request, $id)
    {
        $data = $request->only(['salesbill_id', 'discount']);

        $salesReturnBill = SalesReturnBill::findOrFail($id);
        if($salesReturnBill->editable === 0) {
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Update Sales Return Bill After Approved");
        }

        $billTotal = $salesReturnBill->total;
        $paymentAmount = $billTotal - ($billTotal * $salesReturnBill->discount / 100);
        $data['paymentamount'] = $paymentAmount;
        $data['updated_by'] = 1;
        $salesReturnBill->update($data);

        return ApiMessagesTemplate::createResponse(true, 200, "Sales Return Bill Updated Successfully",  ['total' => $billTotal, 'paymentamount' => $paymentAmount]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $salesReturnBill = SalesReturnBill::findOrFail($id);
        
        if($salesReturnBill->editable === 0) {
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Delete Sales Return Bill After Approved");
        }
        
        $salesReturnItems = $salesReturnBill->items()->get();   
        DB::beginTransaction();
        try{

            foreach($salesReturnItems as $salesReturnItem) {
                $inventory = Inventory::findOrFail($salesReturnItem['inventory_id']);
                $inventory->update(['quantity' => $inventory->quantity - $salesReturnItem->quantity]);
                $salesReturnItem->delete();
            }

            $salesReturnBill->delete();

            DB::commit();
            return ApiMessagesTemplate::createResponse(true, 204);
        }
        catch(Exception $e) {
            DB::rollBack();
            return ApiMessagesTemplate::createResponse(false, 503, $e->getMessage());
        }
    }
}
