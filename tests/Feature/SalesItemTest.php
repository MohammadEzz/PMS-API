<?php

namespace Tests\Feature;

use App\Models\Debit;
use App\Models\Drug;
use App\Models\Inventory;
use App\Models\Price;
use App\Models\PurchaseBill;
use App\Models\PurchaseItem;
use App\Models\SalesBill;
use App\Models\SalesItem;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SalesItemTest extends TestCase
{
    protected $data, 
    $salesBill, $salesItem, 
    $deletedSalesItem, $deletedSalesBill, 
    $approvedSalesBill;
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
        
        $salesBills = SalesBill::factory()->count(3)->create();
        $salesBill = $salesBills[0];
        $deletedSalesBill = $salesBills[1];
        $deletedSalesBill->delete();
        $approvedSalesBill = $salesBills[2];
        $approvedSalesBill->update(['billstatus' => 'approved', 'editable' => 0]);
        
        $salesItems = SalesItem::factory()->count(2)
        ->state(new Sequence(
            ['inventory_id' => $inventoryItems[0], 'quantity' => 0, 'price_id' => $inventoryItems[0]->lastPrice()->id],
            ['inventory_id' => $inventoryItems[1], 'quantity' => 3, 'price_id' => $inventoryItems[1]->lastPrice()->id],
            ))
        ->create([
            'bill_id' => $salesBill->id,
            'bill_type' => 'sales',
        ]);
        $salesItem = $salesItems[0];
        $deletedSalesItem = $salesItems[1];
        $deletedSalesItem->delete();

        $this->assertModelExists($salesItem);
        $this->assertModelMissing($deletedSalesItem);
        $this->assertModelMissing($deletedSalesBill);
        $this->assertModelExists($approvedSalesBill);

        $this->data = [
            'bill_id' => $salesBill->id,
            'inventory_id' => $inventoryItems[2]->id,
            'quantity' => 19,
            'discount' => 15,
            'price_id' => $inventoryItems[2]->lastPrice()->id
        ];
        $this->salesBill = $salesBill;
        $this->salesItem = $salesItem;
        $this->deletedSalesItem = $deletedSalesItem;
        $this->deletedSalesBill = $deletedSalesBill;
        $this->approvedSalesBill = $approvedSalesBill;
    }

    public function test_read_all_salesitem() {
        $response = $this->getJson('api/v1/sales/items');
        $response->assertStatus(200);
    }

    public function test_read_exist_salesitem() {
        $response = $this->getJson('api/v1/sales/items/' . $this->salesItem->id);
        $response->assertStatus(200);
    }

    public function test_read_notexist_salesitem() {
        $response = $this->getJson('api/v1/sales/items/' . $this->deletedSalesItem->id);
        $response->assertStatus(404);
    }

    public function test_success_create_salesitem() {
        $response = $this->postJson('api/v1/sales/items', $this->data);
        $response->assertStatus(201);
    }

    public function test_add_salesitem_to_approved_salesbill() {
        $data = array_merge($this->data, ['bill_id' => $this->approvedSalesBill->id]);
        $response = $this->postJson('api/v1/sales/items', $data);
        $response->assertStatus(405);
    }
    
    public function test_add_salesitem_to_notexit_salesbill() {
        $data = array_merge($this->data, ['bill_id' => $this->deletedSalesBill->id]);
        $response = $this->postJson('api/v1/sales/items', $data);
        $response->assertStatus(404);
    }

    public function checkInvalidFieldPostAction($target, $property, $tested_value) {
        $target[$property] = $tested_value;
        $response = $this->postJson('api/v1/sales/items', $target);
        $response->assertStatus(422);
    }

    public function test_invalid_bill_id_on_create_salesitem() {
        $this->checkInvalidFieldPostAction($this->data, 'bill_id', '');
        $this->checkInvalidFieldPostAction($this->data, 'bill_id', 's');
        $this->checkInvalidFieldPostAction($this->data, 'bill_id', 0);
    }

    public function test_invalid_inventory_id_on_create_salesitem() {
        $this->checkInvalidFieldPostAction($this->data, 'inventory_id', '');
        $this->checkInvalidFieldPostAction($this->data, 'inventory_id', 's');
        $this->checkInvalidFieldPostAction($this->data, 'inventory_id', 0);
    }

    public function test_update_exist_salesitem() {
        $data = array_merge($this->salesItem->toArray(), ['quantity' => 50, 'discount' => 3.5]);
        $response = $this->putJson('api/v1/sales/items/' . $this->salesItem->id, $data);
        $response->assertStatus(201);
    }

    public function test_update_notexist_salesitem() {        
        $data = array_merge($this->salesItem->toArray(), ['quantity' => 33, 'discount' => 3]);
        
        $response = $this->putJson('api/v1/sales/items/' . $this->deletedSalesItem->id, $data);
        $response->assertStatus(404);
    }

    public function test_update_salesitem_with_approved_sales_bill() {
        $this->salesItem->update(['bill_id' => $this->approvedSalesBill->id]);
        $data = ['quantity' => 5, 'discount' => 5];
        
        $response = $this->putJson('api/v1/sales/items/' . $this->salesItem->id, $data);
        $response->assertStatus(405);
    }

    public function test_update_salesitem_quantity_greater_than_inventory() {
        $data = [
            'quantity' => 99,
            'discount' => 5
        ];

        $response = $this->putJson('api/v1/sales/items/' . $this->salesItem->id, $data);
        $response->assertStatus(405);
    }

    public function checkInvalidFieldPutAction($target, $property, $tested_value) {
        $target->{$property} = $tested_value;
        $response = $this->putJson('api/v1/sales/items/' . $target->id, $target->toArray());
        $response->assertStatus(422);
    }

    public function test_invalid_quantity_on_update_salesitem() {
        $this->checkInvalidFieldPutAction($this->salesItem, 'quantity', '');
        $this->checkInvalidFieldPutAction($this->salesItem, 'quantity', 's');
        $this->checkInvalidFieldPutAction($this->salesItem, 'quantity', 0);
        $this->checkInvalidFieldPutAction($this->salesItem, 'quantity', 100);
    }
    
    public function test_invalid_discount_on_update_salesitem() {
        $this->checkInvalidFieldPutAction($this->salesItem, 'discount', 's');
        $this->checkInvalidFieldPutAction($this->salesItem, 'discount', -1);
    }

    public function test_destroy_exist_salesitem() {
        $response = $this->deleteJson('api/v1/sales/items/' . $this->salesItem->id);
        $response->assertStatus(201);
    }  

    public function test_destroy_notexist_salesitem() {
        $response = $this->deleteJson('api/v1/sales/items/' . $this->deletedSalesItem->id);
        $response->assertStatus(404);
    }  

    public function test_delete_salesitem_with_approved_sales_bill() {
        $this->salesItem->update(['bill_id' => $this->approvedSalesBill->id]);

        $response = $this->deleteJson('api/v1/sales/items/' . $this->salesItem->id);
        $response->assertStatus(405);
    }
}
