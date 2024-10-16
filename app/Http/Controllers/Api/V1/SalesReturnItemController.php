<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\SalesItemRequest;
use App\Models\Inventory;
use App\Models\Price;
use App\Models\SalesItem;
use App\Models\SalesReturnBill;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReturnItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $salesReturnItems = SalesItem::where('bill_type','return')->get();
        return ApiMessagesTemplate::createResponse(true, 200, "Sales Return Items Readed Successfully", $salesReturnItems);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SalesItemRequest $request)
    {
        $data = $request->only(['inventory_id', 'bill_id']);
        $data['quantity'] = 1;
        $datap['discount'] = 0;
        $data['bill_type'] = 'return';
        
        $salesReturnBill = SalesReturnBill::findOrFail($data['bill_id']);
        $inventory = Inventory::findOrFail($data['inventory_id']);
        $price = Price::findOrFail($inventory->lastPrice()->id);

        if($salesReturnBill->editable === 0){
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Add Sales Return Item After Sales Return Bill Approved");
        }

        if($inventory->quantity === 0) {
            return ApiMessagesTemplate::createResponse(false, 405, "Inventory Is Empty");
        }
        
        $totalBill = $salesReturnBill->total + $price->price;
        $paymentAmount = $totalBill - ($totalBill * $salesReturnBill->discount / 100);

        $data = [
            'bill_id' => $salesReturnBill->id,
            'bill_type' => 'return',
            'inventory_id' => $inventory->id,
            'quantity' => 1,
            'discount' => 0,
            'price_id' => $price->id
        ];

        DB::beginTransaction();
        try {
            DB::table('inventory')->where('id',$inventory->id)->lockForUpdate();
            DB::table('salesreturnbills')->where('id',$salesReturnBill->id)->lockForUpdate();
            
            $salesReturnBill->update(['total' => $totalBill, 'paymentamount' => $paymentAmount]);
            $inventory->update(['quantity' => $inventory->quantity + 1]);
            
            $salesReturnItem = SalesItem::create($data);
            $salesReturnItem->price = $price->price;
            $salesReturnItem->drugid = $inventory->drug()->first()->id;
            $salesReturnItem->drugname = $inventory->drug()->first()->name;
            $salesReturnItem->itemtotal = $price->price;
            $salesReturnItem->total = $totalBill;
            $salesReturnItem->paymentamount = $totalBill - ($totalBill * $salesReturnBill->discount / 100);
            
            DB::commit();
            return ApiMessagesTemplate::createResponse(true, 201, "Sales Return Item Created Successfully", $salesReturnItem);
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
        $salesReturnItem = SalesItem::findOrFail($id);
        return ApiMessagesTemplate::createResponse(true, 200, "Sales Return Item Readed Successfully", $salesReturnItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SalesItemRequest $request, $id)
    {
        $data = $request->only(['quantity', 'discount']);

        $salesReturnItem = SalesItem::findOrFail($id);
        $salesReturnBill = SalesReturnBill::findOrFail($salesReturnItem->bill_id);
        
        if($salesReturnBill->editable === 0) {
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Updated Sales Return Item After Sales Return Bill Approved");
        }

        $inventory = Inventory::findOrFail($salesReturnItem->inventory_id);
        $price = Price::findOrFail($salesReturnItem->price_id);

        $inventoryQuantity = $inventory->quantity - $salesReturnItem->quantity + (float)$data['quantity'];
        $previousSalesReturnItemDiscount = $price->price * ($salesReturnItem->discount) / 100;
        $previousSalesReturnItemTotal = $salesReturnItem->quantity * ($price->price - $previousSalesReturnItemDiscount);
        $newSalesReturnItemTotalDiscount = $price->price * ((float)$data['discount']) / 100;
        $newSalesReturnItemTotal = (float)$data['quantity'] * ($price->price - $newSalesReturnItemTotalDiscount);
        $newBillTotal = $salesReturnBill->total - $previousSalesReturnItemTotal + $newSalesReturnItemTotal;
        $paymentAmount = $newBillTotal - ($newBillTotal * $salesReturnBill->discount / 100);

        DB::beginTransaction();
        try{
            DB::table('inventory')->where('id',$inventory->id)->lockForUpdate();
            DB::table('salesitems')->where('id',$salesReturnItem->id)->lockForUpdate();
            DB::table('salesreturnbills')->where('id',$salesReturnBill->id)->lockForUpdate();

            $inventory->update(['quantity' => $inventoryQuantity]);
            $salesReturnItem->update($data);
            $salesReturnBill->update(['total' => $newBillTotal, 'paymentamount' => $paymentAmount]);
            
            DB::commit();
            return ApiMessagesTemplate::createResponse(true, 201, "Sales Return Item Updated Successfully", ['itemtotal' => $newSalesReturnItemTotal, 'total' => $newBillTotal, 'paymentamount' => $paymentAmount]);
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
        $salesReturnItem = SalesItem::findOrFail($id);

        $salesReturnBill = SalesReturnBill::findOrFail($salesReturnItem->bill_id);
        if($salesReturnBill->editable === 0) {
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Deleted Sales Return Item After Saled Bill Approved");
        }

        $inventory = Inventory::findOrFail($salesReturnItem->inventory_id);
        $price = Price::findOrFail($salesReturnItem->price_id);

        $inventoryQuantity = $inventory->quantity - $salesReturnItem->quantity;
        $salesReturnItemDiscount = ($price->price * ($salesReturnItem->discount) / 100);
        $newBillTotal = $salesReturnBill->total - ($salesReturnItem->quantity * ($price->price - $salesReturnItemDiscount));
        $paymentAmount = $newBillTotal - ($newBillTotal * $salesReturnBill->discount / 100);

        DB::beginTransaction();
        try{
            DB::table('inventory')->where('id',$inventory->id)->lockForUpdate();
            DB::table('salesitems')->where('id',$salesReturnItem->id)->lockForUpdate();
            DB::table('salesreturnbills')->where('id',$salesReturnBill->id)->lockForUpdate();

            $inventory->update(['quantity' => $inventoryQuantity]);
            $salesReturnItem->delete();
            $salesReturnBill->update(['total' => $newBillTotal, 'paymentamount' => $paymentAmount]);
            
            DB::commit();
            return ApiMessagesTemplate::createResponse(true, 201, "Sales Item Deleted Successfully", ['total' => $newBillTotal, 'paymentamount' => $paymentAmount]);
        }
        catch(Exception $e) {
            DB::rollBack();
            return ApiMessagesTemplate::createResponse(false, 503, $e->getMessage());
        }
    }
}
