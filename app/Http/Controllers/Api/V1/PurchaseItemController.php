<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseItem;
use App\Http\Requests\PurchaseItemRequest;
use App\Http\Helpers\PurchaseBill\PurchaseBillOperations;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Models\Drug;
use App\Models\PurchaseBill;
use Exception;
use Illuminate\Http\Request;

class PurchaseItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ApiSort $sort, ApiField $field, ApiFilter $filter)
    {
        $fields = [
            "id" => "purchaseitems.id",
            'purchasebill_id' => "purchasebill_id",
            "drug_id" => "drug_id",
            "drugname" => "drugs.name",
            "drugbarcode" => "drugs.barcode",
            "quantity" => "quantity",
            "bonus" => "bonus",
            "purchaseprice" => "purchaseprice",
            "sellprice" => "sellprice",
            "tax" => "tax",
            "discount" => "discount",
            "expiredate" => "expiredate",
            "created_by" => "purchaseitems.created_by",
            "updated_by" => "purchaseitems.updated_by",
            "created_at" => "purchaseitems.created_at",
            "updated_at" => "purchaseitems.updated_at",
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
        $query = PurchaseItem::query()->select($fieldParams)
        ->leftJoin('drugs', 'purchaseitems.drug_id', '=', 'drugs.id');

        // Where
        $filterParams && $query->WhereRaw($filterParams, $queryParams);
        
        // Order By | Sorting
        if(count($sortParams) > 0) {
            foreach($sortParams as $value){
                [$field, $sortType] = explode('.', $value);
                $query->orderBy($field, $sortType);
            }
        }
        else $query->orderBy('id', 'asc');
       
        $purchaseItems = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

        return ApiMessagesTemplate::createResponse(true, 200, "Purchase Items Readed Successfully", $purchaseItems);
        }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchaseItemRequest $request, PurchaseBillOperations $operations)
    {
        $data = $request->only([
            "purchasebill_id",
            "drug_id",
            "quantity",
            "bonus",
            "sellprice",
            "tax",
            "discount",
            "expiredate",
        ]);
        $data['created_by'] = 1;

        $bill = PurchaseBill::find($data['purchasebill_id']);

        
        $isDrugExist = $bill->items()->where('drug_id', $data['drug_id'])->first();

        if($isDrugExist) {
            return ApiMessagesTemplate::createResponse(false, 405, 'Cannot Add Purchase Item With Drug Already Exist In Purchase Bill');
        }

        if($bill->editable === 0){
            return ApiMessagesTemplate::createResponse(false, 405, 'Cannot Add Purchase Item To Apporved Purchase Bill');
        }

        $billTotal = $operations->calculateTotal($data['quantity'], $data['sellprice'], $data['tax'], $data['discount']);
        $purchasePricePerItem = $operations->calculatePurchasePricePerItem($billTotal, $data['quantity']);
        $data['purchaseprice'] = $purchasePricePerItem;

        DB::beginTransaction();
        try {
            $isCreated = PurchaseItem::create($data);

            $bill = DB::table('purchasebills')
            ->where('id', $data['purchasebill_id'])
            ->lockForUpdate()
            ->first();

            $total = $bill->total + $data['purchaseprice'] * (int)$data['quantity'];

            DB::table('purchasebills')
            ->where('id', $data['purchasebill_id'])
            ->update([
                "total" => $total
            ]);

            $itemDrug = Drug::findOrFail($isCreated->drug_id);
            $isCreated->totalbill = round($total, 3);
            $isCreated->purchaseprice = round($isCreated->purchaseprice, 3);
            $isCreated->drugname = $itemDrug->name;
            $isCreated->drugbarcode = $itemDrug->barcode;

            DB::commit();
            return ApiMessagesTemplate::createResponse(true, 200, "Purchase Item Created Successfully", $isCreated);
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
        $purchaseItem = PurchaseItem::findOrFail($id);
        return ApiMessagesTemplate::createResponse(true, 201, "Purchase Item Readed Successfully", $purchaseItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PurchaseItemRequest $request, $id, PurchaseBillOperations $operations)
    {
        $data = $request->only([
            'drug_id',
            'quantity', 
            'bonus', 
            'sellprice', 
            'tax', 
            'discount', 
            'expiredate']);
        $data['updated_by'] = 1;

        $item = PurchaseItem::findOrFail($id);
        $itemBill = $item->bill()->first();

        if($itemBill->editable === 1) {

            if($item->quantity !== (int)$data['quantity'] 
                ||
                $item->sellprice !== (float)$data['sellprice'] 
                ||
                $item->tax !== (float)$data['tax'] 
                ||
                $item->discount !== (float)$data['discount'])
            {
                $previousItemTotal = $operations->calculateTotal($item->quantity, $item->sellprice, $item->tax, $item->discount);
                $newItemTotal = $operations->calculateTotal($data['quantity'], $data['sellprice'], $data['tax'], $data['discount']);
                $purchasePricePerItem = $operations->calculatePurchasePricePerItem($newItemTotal, $data['quantity']);
                $data['purchaseprice'] = $purchasePricePerItem;
                $newBillTotal= $itemBill->total - $previousItemTotal + $newItemTotal;
                
                DB::beginTransaction();
                try{
                    DB::table('purchasebills')->where('id', $itemBill->id)->lockForUpdate()->get();
                    DB::table('purchaseitems')->where('id', $id)->lockForUpdate();
                    DB::table('purchasebills')->where('id', $itemBill->id)->update(["total" => $newBillTotal]);
                    $item->update($data);
                    
                    $itemDrug = Drug::findOrFail($item->drug_id);
                    $item->totalbill = round($newBillTotal, 3);
                    $item->purchaseprice = round($item->purchaseprice, 3);
                    $item->drugname = $itemDrug->name;
                    $item->drugbarcode = $itemDrug->barcode;
                    DB::commit();
                    return ApiMessagesTemplate::createResponse(true, 200, "Purchase Item Updated Successfully", $item);
                    $purchasePricePerItem = $operations->calculatePurchasePricePerItem($newItemTotal, $data['quantity']);
                    }
                catch(Exception $e) {
                    DB::rollBack();
                    return ApiMessagesTemplate::createResponse(false, 503, $e->getMessage());
                }
            }
            else {
                $item->drug_id = $data['drug_id'];
                $item->bouns = $data['bonus'];
                $item->expiredate = $data['expiredate'];
                $item->updated_by = 1;
                $isUpdated = $item->save();

                if($isUpdated)
                    return ApiMessagesTemplate::createResponse(true, 201, "Purchase Item Updated Successfully");
                else
                    return ApiMessagesTemplate::createResponse(false, 503, "Purchase Item Updated Failed");
            }
        }
        else
            return ApiMessagesTemplate::createResponse(false, 503, "Purchase Item Not Editable After Bill Approved");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseBillOperations $operations, $id)
    {
        $purchaseItem = PurchaseItem::findOrFail($id);

        if($purchaseItem) {
            $itemBill = $purchaseItem->bill()->first();

            if($itemBill->editable === 1) {
                $itemTotal = $operations->calculateTotal($purchaseItem['quantity'], $purchaseItem['sellprice'], $purchaseItem['tax'], $purchaseItem['discount']);
                $totalBillWithoutDeletedItem = $itemBill->total - $itemTotal;
                DB::beginTransaction();
                try{
                    DB::table('purchasebills')->where('id', $itemBill->id)->lockForUpdate();
                    DB::table('purchaseitems')->where('id', $id)->lockForUpdate();
                    $itemBill->update(["total" => $totalBillWithoutDeletedItem]);
                    $purchaseItem->delete();
                    DB::commit();
                    return ApiMessagesTemplate::createResponse(true, 200, "Purchase Item Deleted Successfully", ['totalbill' => $totalBillWithoutDeletedItem]);
                }
                catch(Exception $e){
                    DB::rollBack();
                    return ApiMessagesTemplate::createResponse(false, 503, $e->getMessage());
                }
            }
            else
                return ApiMessagesTemplate::createResponse(false, 503, "Purchase Item Not Deletable After Bill Approved");

        }
        else 
            return ApiMessagesTemplate::createResponse(false, 404, "Purchase Item Not Exist");
    }
}
