<?php

namespace Tests\Feature;

use App\Models\Debit;
use App\Models\Drug;
use App\Models\Inventory;
use App\Models\Price;
use App\Models\PurchaseBill;
use App\Models\PurchaseItem;
use App\Models\SalesItem;
use App\Models\SalesReturnItem;
use App\Models\SalesReturnBill;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SalesReturnItemTest extends TestCase
{

    protected $data, 
    $salesReturnBill, $salesReturnItem, 
    $deletedSalesReturnItem, $deletedSalesReturnBill, 
    $approvedSalesReturnBill;
    public function setUp() : void {
        parent::setUp();

        $durgs = Drug::factory()->count(3)->create(['created_by' => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->count(3)->state(new Sequence(
            ['drug_id'=>$durgs[0]->id, 'created_by' => 1, 'quantity' => 50, 'bonus' => 5, 'purchaseprice' => 8],
            ['drug_id'=>$durgs[1]->id, 'created_by' => 1, 'quantity' => 60, 'bonus' => 6, 'purchaseprice' => 8],
            ['drug_id'=>$durgs[2]->id, 'created_by' => 1, 'quantity' => 70, 'bonus' => 7, 'purchaseprice' => 8]
        )),'items')
        ->create(["created_by" => 1, 'billstatus' => 'approved', 'editable' => 0]);
        
        $purchaseItems = $purchaseBill->items()->get();
        $total = 0;
        foreach($purchaseItems as $purchaseItem) {
            $total += ($purchaseItem->quantity * $purchaseItem->purchaseprice);
            Price::create([
                'drug_id' => $purchaseItem->drug_id,
                'price' => $purchaseItem->sellprice,
                'editable' => 0,
                'created_by' => 1
            ]);

            Inventory::create([
                'purchaseitem_id' => $purchaseItem->id,
                'drug_id' => $purchaseItem->drug_id,
                'quantity' => $purchaseItem->quantity,
                'expiredate' => $purchaseItem->expiredate
            ]);
        }

        $purchaseBill->update(['total' => $total, 'updated_by' => 1]);
        Debit::create(['creditor_id' => $purchaseBill->supplier_id, 'creditor_type' => 'supplier','amount' => $total]);
        $inventoryItems = $purchaseBill->inventoryItems()->get();
        
        $salesReturnBills = SalesReturnBill::factory()->count(3)->create();
        $salesReturnBill = $salesReturnBills[0];
        $deletedSalesReturnBill = $salesReturnBills[1];
        $deletedSalesReturnBill->delete();
        $approvedSalesReturnBill = $salesReturnBills[2];
        $approvedSalesReturnBill->update(['billstatus' => 'approved', 'editable' => 0]);
        
        $salesReturnItems = SalesItem::factory()->count(2)
        ->state(new Sequence(
            ['inventory_id' => $inventoryItems[0], 'quantity' => 0, 'price_id' => $inventoryItems[0]->lastPrice()->id],
            ['inventory_id' => $inventoryItems[1], 'quantity' => 3, 'price_id' => $inventoryItems[1]->lastPrice()->id],
            ))
        ->create([
            'bill_id' => $salesReturnBill->id,
            'bill_type' => 'return',
        ]);
        $salesReturnItem = $salesReturnItems[0];
        $deletedSalesReturnItem = $salesReturnItems[1];
        $deletedSalesReturnItem->delete();

        $this->assertModelExists($salesReturnItem);
        $this->assertModelMissing($deletedSalesReturnItem);
        $this->assertModelMissing($deletedSalesReturnBill);
        $this->assertModelExists($approvedSalesReturnBill);

        $this->data = [
            'bill_id' => $salesReturnBill->id,
            'inventory_id' => $inventoryItems[2]->id,
            'quantity' => 19,
            'discount' => 15,
            'price_id' => $inventoryItems[2]->lastPrice()->id
        ];
        $this->salesReturnBill = $salesReturnBill;
        $this->salesReturnItem = $salesReturnItem;
        $this->deletedSalesReturnItem = $deletedSalesReturnItem;
        $this->deletedSalesReturnBill = $deletedSalesReturnBill;
        $this->approvedSalesReturnBill = $approvedSalesReturnBill;
    }

    public function test_read_all_salesreturnitem() {
        $response = $this->getJson('api/v1/sales/returns/items');
        $response->assertStatus(200);
    }

    public function test_read_exist_salesreturnitem() {
        $response = $this->getJson('api/v1/sales/returns/items/' . $this->salesReturnItem->id);
        $response->assertStatus(200);
    }

    public function test_read_notexist_salesreturnitem() {
        $response = $this->getJson('api/v1/sales/returns/items/' . $this->deletedSalesReturnItem->id);
        $response->assertStatus(404);
    }

    public function test_success_create_salesreturnitem() {
        $response = $this->postJson('api/v1/sales/returns/items', $this->data);
        $response->assertStatus(201);
    }

    public function test_add_salesreturnitem_to_approved_salesReturnbill() {
        $data = array_merge($this->data, ['bill_id' => $this->approvedSalesReturnBill->id]);
        $response = $this->postJson('api/v1/sales/returns/items', $data);
        $response->assertStatus(405);
    }
    
    public function test_add_salesreturnitem_to_notexit_salesReturnbill() {
        $data = array_merge($this->data, ['bill_id' => $this->deletedSalesReturnBill->id]);
        $response = $this->postJson('api/v1/sales/returns/items', $data);
        $response->assertStatus(404);
    }

    public function checkInvalidFieldPostAction($target, $property, $tested_value) {
        $target[$property] = $tested_value;
        $response = $this->postJson('api/v1/sales/returns/items', $target);
        $response->assertStatus(422);
    }

    public function test_invalid_bill_id_on_create_salesreturnitem() {
        $this->checkInvalidFieldPostAction($this->data, 'bill_id', '');
        $this->checkInvalidFieldPostAction($this->data, 'bill_id', 's');
        $this->checkInvalidFieldPostAction($this->data, 'bill_id', 0);
    }

    public function test_invalid_inventory_id_on_create_salesreturnitem() {
        $this->checkInvalidFieldPostAction($this->data, 'inventory_id', '');
        $this->checkInvalidFieldPostAction($this->data, 'inventory_id', 's');
        $this->checkInvalidFieldPostAction($this->data, 'inventory_id', 0);
    }

    public function test_update_exist_salesreturnitem() {
        $data = array_merge($this->salesReturnItem->toArray(), ['quantity' => 50, 'discount' => 3.5]);
        $response = $this->putJson('api/v1/sales/returns/items/' . $this->salesReturnItem->id, $data);
        $response->assertStatus(201);
    }

    public function test_update_notexist_salesreturnitem() {        
        $data = array_merge($this->salesReturnItem->toArray(), ['quantity' => 33, 'discount' => 3]);
        
        $response = $this->putJson('api/v1/sales/returns/items/' . $this->deletedSalesReturnItem->id, $data);
        $response->assertStatus(404);
    }

    public function test_update_salesreturnitem_with_approved_sales_bill() {
        $this->salesReturnItem->update(['bill_id' => $this->approvedSalesReturnBill->id]);
        $data = ['quantity' => 5, 'discount' => 5];
        
        $response = $this->putJson('api/v1/sales/returns/items/' . $this->salesReturnItem->id, $data);
        $response->assertStatus(405);
    }

    public function checkInvalidFieldPutAction($target, $property, $tested_value) {
        $target->{$property} = $tested_value;
        $response = $this->putJson('api/v1/sales/returns/items/' . $target->id, $target->toArray());
        $response->assertStatus(422);
    }

    public function test_invalid_quantity_on_update_salesreturnitem() {
        $this->checkInvalidFieldPutAction($this->salesReturnItem, 'quantity', '');
        $this->checkInvalidFieldPutAction($this->salesReturnItem, 'quantity', 's');
        $this->checkInvalidFieldPutAction($this->salesReturnItem, 'quantity', 0);
        $this->checkInvalidFieldPutAction($this->salesReturnItem, 'quantity', 100);
    }
    
    public function test_invalid_discount_on_update_salesreturnitem() {
        $this->checkInvalidFieldPutAction($this->salesReturnItem, 'discount', 's');
        $this->checkInvalidFieldPutAction($this->salesReturnItem, 'discount', -1);
    }

    public function test_destroy_exist_salesreturnitem() {
        $response = $this->deleteJson('api/v1/sales/returns/items/' . $this->salesReturnItem->id);
        $response->assertStatus(201);
    }  

    public function test_destroy_notexist_salesreturnitem() {
        $response = $this->deleteJson('api/v1/sales/returns/items/' . $this->deletedSalesReturnItem->id);
        $response->assertStatus(404);
    }  

    public function test_delete_salesreturnitem_with_approved_sales_bill() {
        $this->salesReturnItem->update(['bill_id' => $this->approvedSalesReturnBill->id]);

        $response = $this->deleteJson('api/v1/sales/returns/items/' . $this->salesReturnItem->id);
        $response->assertStatus(405);
    }
}
