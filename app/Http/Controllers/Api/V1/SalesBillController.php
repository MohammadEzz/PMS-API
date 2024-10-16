<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\SalesBillRequest;
use App\Models\Inventory;
use App\Models\SalesBill;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesBillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $salesBills = SalesBill::all();
        return ApiMessagesTemplate::createResponse(true, 200, "Sales Bills Readed Successfully", $salesBills);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
       $salesBill = SalesBill::create(['created_by' => 1]);
       return ApiMessagesTemplate::createResponse(true, 201, "Sales Bill Created Successfully", $salesBill);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $salesBill = SalesBill::findOrFail($id);
        return ApiMessagesTemplate::createResponse(true, 200, "Sales Bill Readed Successfully", $salesBill);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SalesBillRequest $request, $id)
    {
        $data = $request->only(['client_id', 'discount']);

        $salesBill = SalesBill::findOrFail($id);
        if($salesBill->editable === 0) {
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Update Sales Bill After Approved");
        }

        $billTotal = $salesBill->total;
        $paymentAmout = $billTotal - ($billTotal * $salesBill->discount / 100);
        $data['paymentamount'] = $paymentAmout;
        $data['updated_by'] = 1;
        $salesBill->update($data);
    
        return ApiMessagesTemplate::createResponse(true, 201, "Sales Bill Updated Successfylly", ['total' => $billTotal, 'paymentamount' => $paymentAmout]);
      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $salesBill = SalesBill::findOrFail($id);
        
        if($salesBill->editable === 0) {
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Delete Sales Bill After Approved");
        }
        
        $salesItems = $salesBill->items()->get();   
        DB::beginTransaction();
        try{

            foreach($salesItems as $salesItem) {
                $inventory = Inventory::findOrFail($salesItem['inventory_id']);
                $inventory->update(['quantity' => $inventory->quantity + $salesItem->quantity]);
                $salesItem->delete();
            }

            $salesBill->delete();

            DB::commit();
            return ApiMessagesTemplate::createResponse(true, 204);
        }
        catch(Exception $e) {
            DB::rollBack();
            return ApiMessagesTemplate::createResponse(false, 503, $e->getMessage());
        }
      
    }
}
