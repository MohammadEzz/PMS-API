<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiMessagesTemplate;
use App\Http\Helpers\Api\ApiSort;
use App\Http\Requests\PurchaseBillRequest;
use App\Models\Debit;
use App\Models\Inventory;
use App\Models\Price;
use App\Models\PurchaseBill;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseBillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ApiSort $sort, ApiField $field, ApiFilter $filter)
    {
        $fields = [
            "id" => "purchasebills.id",
            "supplier" => "suppliers.name",
            "supplier_id" => "purchasebills.supplier_id",
            "dealer" => "dealers.name as dealer",
            "dealer_id" => "dealer_id",
            "billnumber" => "billnumber",
            "issuedate" => "issuedate",
            "paymenttype" => "paymenttype",
            "paidstatus" => "paidstatus",
            "billstatus" => "billstatus",
            "total" => "total",
            "created_by" => "purchasebills.created_by",
            "updated_by" => "purchasebills.updated_by",
            "created_at" => "purchasebills.created_at",
            "updated_at" => "purchasebills.updated_at",
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
        $query = PurchaseBill::query()->select($fieldParams)
        ->leftJoin('suppliers', 'purchasebills.supplier_id', '=', 'suppliers.id')
        ->leftJoin('dealers', 'purchasebills.dealer_id', '=', 'dealers.id');

        // Where
        $filterParams && $query->WhereRaw($filterParams, $queryParams);

        // Order By | Sorting
        if(count($sortParams) > 0) {
            foreach($sortParams as $value){
                [$field, $sortType] = explode('.', $value);
                $query->orderBy($field, $sortType);
            }
        }
        else $query->orderBy('issuedate', 'desc');

       
        $purchseBills = $rangeParams == 'all' ? $query->get() : $query->paginate($rangeParams);

        return ApiMessagesTemplate::createResponse(true, 200, "Purchase Bills Readed Successfully", $purchseBills);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchaseBillRequest $request)
    {
        $data = $request->only([
            "supplier_id",
            "dealer_id",
            "billnumber",
            "issuedate",
            "paymenttype",
            "paidstatus",
        ]);
        $data['created_by'] = 1;

        $isCreated = PurchaseBill::create($data);
        if($isCreated)
            return ApiMessagesTemplate::createResponse(true, 200, "Purchase Bill Created Successfully", $isCreated);    
        else
            return ApiMessagesTemplate::createResponse(false, 400, "Purchase Bill Created failed");    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $purchaseBill = PurchaseBill::findOrFail($id);
       
        $purchaseBill = PurchaseBill::
        where('purchasebills.id',$id)
        ->leftJoin('suppliers', 'suppliers.id', '=', 'purchasebills.supplier_id')
        ->leftJoin('dealers', 'dealers.id', '=', 'purchasebills.dealer_id')
        ->leftJoin('users as u1', 'u1.id', '=', 'purchasebills.created_by')
        ->leftJoin('users as u2', 'u2.id', '=', 'purchasebills.updated_by')
        ->first(
            [
                'purchasebills.id as id',
                'suppliers.name as supplier',
                'purchasebills.supplier_id',
                'dealers.name as dealer',
                'dealer_id',
                'billnumber',
                'paymenttype',
                'paidstatus',
                'billstatus',
                'issuedate',
                'total',
                'purchasebills.created_by',
                'u1.firstname as created_by_fname',
                'u1.lastname as created_by_lname',
                'purchasebills.updated_by',
                'u2.firstname as updated_by_fname',
                'u2.lastname as updated_by_lname',
                'purchasebills.created_at',
                'purchasebills.updated_at'
            ]
        );
        return ApiMessagesTemplate::createResponse(true, 200, "Purchase Bill Readed Successfully", $purchaseBill);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PurchaseBillRequest $request, $id)
    {
       $data = $request->only([
            "supplier_id",
            "dealer_id",
            "billnumber",
            "issuedate",
            "paymenttype",
            "paidstatus",
        ]);
        $data['updated_by'] = 1;

        $purchaseBill = PurchaseBill::findOrFail($id);

        if($purchaseBill->editable === 0) {
            return ApiMessagesTemplate::createResponse(false, 503, "Purchase Bill Not Editable After Approved");
        }

        $purchaseBill->update($data);
        return ApiMessagesTemplate::createResponse(true, 201, "Purchase Bill Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $purchaseBill = PurchaseBill::findOrFail($id);

        if($purchaseBill->editable === 0) {
            return ApiMessagesTemplate::createResponse(false, 503, "Purchase Bill Not Deletable After Approved");
        }

        $purchaseBill->delete();
        return ApiMessagesTemplate::createResponse(true, 201, "Purchase Bill Deleted Successfully");
    }

    public function approveBill($id) {
        $purchaseBill = PurchaseBill::findOrFail($id);

        if($purchaseBill->editable === 0) {
            return ApiMessagesTemplate::createResponse(false, 405, "Purchase Bill Already Approved");
        }

        DB::beginTransaction();
        try {
            $bill = Purchasebill::where('id', $id)->lockForUpdate()->first();

            $bill->update([
                "billstatus" => "approved",
                "editable" => 0
            ]);

            $purchaseBillItems = $purchaseBill->items()->get();
            if($purchaseBillItems->isNotEmpty()) {
                foreach($purchaseBillItems as $purchaseBillItem){
                    Inventory::create([
                        "purchaseitem_id" => $purchaseBillItem->id,
                        "drug_id" => $purchaseBillItem->drug_id,
                        "quantity" => $purchaseBillItem->quantity + $purchaseBillItem->bonus,
                        "expiredate" => $purchaseBillItem->expiredate,
                    ]);

                    Price::create([
                        "drug_id" => $purchaseBillItem->drug_id,
                        "price" => $purchaseBillItem->sellprice,
                        "editable" => 0,
                        "created_by" => 1,
                    ]);
                }
            }
            
            $supplierDebit = Debit::where([
                ["creditor_id", "=", $bill->supplier_id],
                ["creditor_type", "=", "supplier"]
            ])->first();

            if($supplierDebit) {
                $supplierDebit = DB::table('debits')->where([
                    ["creditor_id", "=", $bill->supplier_id],
                    ["creditor_type", "=", "supplier"]
                ])->lockForUpdate()->first();

                DB::table('debits')->where([
                    ["creditor_id", "=", $bill->supplier_id],
                    ["creditor_type", "=", "supplier"]
                ])->update([
                    "amount" => $supplierDebit->amount + $bill->total
                ]);
            }
            else {
                Debit::create([
                    "creditor_id" => $bill->supplier_id,
                    "creditor_type" => "supplier",
                    "amount" => $bill->total
                ]);
            }

            DB::commit();
            return ApiMessagesTemplate::createResponse(true, 204);
        }
        catch(Exception $e) {
            DB::rollback();
            return ApiMessagesTemplate::createResponse(false, 503, $e->getMessage());
        }
    }
}