<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Requests\SalesBillRequest;
use App\Http\Requests\SalesItemRequest;
use App\Models\Inventory;
use App\Models\Price;
use App\Models\SalesBill;
use App\Models\SalesItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $salesItems = SalesItem::where('bill_type','sales')->get();
        return ApiMessagesTemplate::createResponse(true, 200, "Sales Items Readed Successfully", $salesItems);
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
        $data['bill_type'] = 'sales';
        
        $salesBill = SalesBill::findOrFail($data['bill_id']);
        $inventory = Inventory::findOrFail($data['inventory_id']);
        $price = Price::findOrFail($inventory->lastPrice()->id);

        if($salesBill->editable === 0){
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Add Sales Item After Sales Bill Approved");
        }

        if($inventory->quantity === 0) {
            return ApiMessagesTemplate::createResponse(false, 405, "Inventory Is Empty");
        }
        
        $totalBill = $salesBill->total + $price->price;
        $paymentAmount = $totalBill - ($totalBill * $salesBill->discount / 100);

        $data = [
            'bill_id' => $salesBill->id,
            'bill_type' => 'sales',
            'inventory_id' => $inventory->id,
            'quantity' => 1,
            'discount' => 0,
            'price_id' => $price->id
        ];

        DB::beginTransaction();
        try {
            DB::table('inventory')->where('id',$inventory->id)->lockForUpdate();
            DB::table('salesbills')->where('id',$salesBill->id)->lockForUpdate();
            
            $salesBill->update(['total' => $totalBill, 'paymentamount' => $paymentAmount]);
            $inventory->update(['quantity' => $inventory->quantity - 1]);
            
            $salesItem = SalesItem::create($data);
            $salesItem->price = $price->price;
            $salesItem->drugid = $inventory->drug()->first()->id;
            $salesItem->drugname = $inventory->drug()->first()->name;
            $salesItem->itemtotal = $price->price;
            $salesItem->total = $totalBill;
            $salesItem->paymentamount = $totalBill - ($totalBill * $salesBill->discount / 100);
            
            DB::commit();
            return ApiMessagesTemplate::createResponse(true, 201, "Sales Item Created Successfully", $salesItem);
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
        $salesItem = SalesItem::findOrFail($id);
        return ApiMessagesTemplate::createResponse(true, 200, "Sales Item Readed Successfully", $salesItem);
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

        $salesItem = SalesItem::findOrFail($id);
        $salesBill = SalesBill::findOrFail($salesItem->bill_id);
        
        if($salesBill->editable === 0) {
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Updated Sales Item After Saled Bill Approved");
        }

        $inventory = Inventory::findOrFail($salesItem->inventory_id);

        if((float)$data['quantity'] > $salesItem->quantity + $inventory->quantity) {
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Update Because Quantity On Sales Item > Quantity On Inventory");
        }

        $price = Price::findOrFail($salesItem->price_id);

        $inventoryQuantity = $inventory->quantity + $salesItem->quantity - (float)$data['quantity'];
        $previousSalesItemDiscount = $price->price * ($salesItem->discount) / 100;
        $previousSalesItemTotal = $salesItem->quantity * ($price->price - $previousSalesItemDiscount);
        $newSalesItemTotalDiscount = $price->price * ((float)$data['discount']) / 100;
        $newSalesItemTotal = (float)$data['quantity'] * ($price->price - $newSalesItemTotalDiscount);
        $newBillTotal = $salesBill->total - $previousSalesItemTotal + $newSalesItemTotal;
        $paymentAmount = $newBillTotal - ($newBillTotal * $salesBill->discount / 100);

        DB::beginTransaction();
        try{
            DB::table('inventory')->where('id',$inventory->id)->lockForUpdate();
            DB::table('salesitems')->where('id',$salesItem->id)->lockForUpdate();
            DB::table('salesbills')->where('id',$salesBill->id)->lockForUpdate();

            $inventory->update(['quantity' => $inventoryQuantity]);
            $salesItem->update($data);
            $salesBill->update(['total' => $newBillTotal, 'paymentamount' => $paymentAmount]);
            
            DB::commit();
            return ApiMessagesTemplate::createResponse(true, 201, "Sales Item Updated Successfully", ['itemtotal' => $newSalesItemTotal, 'total' => $newBillTotal, 'paymentamount' => $paymentAmount]);
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
        $salesItem = SalesItem::findOrFail($id);

        $salesBill = SalesBill::findOrFail($salesItem->bill_id);
        if($salesBill->editable === 0) {
            return ApiMessagesTemplate::createResponse(false, 405, "Cannot Deleted Sales Item After Saled Bill Approved");
        }

        $inventory = Inventory::findOrFail($salesItem->inventory_id);
        $price = Price::findOrFail($salesItem->price_id);

        $inventoryQuantity = $inventory->quantity + $salesItem->quantity;
        $salesItemDiscount = ($price->price * ($salesItem->discount) / 100);
        $newBillTotal = $salesBill->total - ($salesItem->quantity * ($price->price - $salesItemDiscount));
        $paymentAmount = $newBillTotal - ($newBillTotal * $salesBill->discount / 100);

        DB::beginTransaction();
        try{
            DB::table('inventory')->where('id',$inventory->id)->lockForUpdate();
            DB::table('salesitems')->where('id',$salesItem->id)->lockForUpdate();
            DB::table('salesbills')->where('id',$salesBill->id)->lockForUpdate();

            $inventory->update(['quantity' => $inventoryQuantity]);
            $salesItem->delete();
            $salesBill->update(['total' => $newBillTotal, 'paymentamount' => $paymentAmount]);
            
            DB::commit();
            return ApiMessagesTemplate::createResponse(true, 201, "Sales Item Deleted Successfully", ['total' => $newBillTotal, 'paymentamount' => $paymentAmount]);
        }
        catch(Exception $e) {
            DB::rollBack();
            return ApiMessagesTemplate::createResponse(false, 503, $e->getMessage());
        }
    }
}
