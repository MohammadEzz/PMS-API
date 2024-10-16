<?php

namespace Tests\Feature;

use App\Models\Drug;
use App\Models\PurchaseBill;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturnBill;
use App\Models\PurchaseReturnItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseReturnItemTest extends TestCase
{
    protected $data, 
    $purchaseBill, $purchaseItems, 
    $purchaseReturnBill, $purchaseReturnItems,
    $approvedPurchaseReturnBill, $approvedPurchaseReturnItem, 
    $deletedPurchaseReturnBill, $deletedPurchaseReturnItem, $deletedPurchaseItem;
    
    public function setUp() : void
    {
        parent::setUp();
        
        $drugs = Drug::factory()->count(3)->create(['created_by' => 1]);
        
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->count(3)->state(new Sequence(
            ['drug_id' => $drugs[0]->id, 'quantity' => 40, 'bonus' => 7, 'created_by' => 1],
            ['drug_id' => $drugs[1]->id, 'quantity' => 200, 'bonus' => 15, 'created_by' => 1],
            ['drug_id' => $drugs[2]->id, 'quantity' => 100, 'bonus' => 20, 'created_by' => 1])
            ), 'items')
        ->create([
            'created_by' => 1,
            'billstatus' => 'approved',
            'editable' => 0
        ]);
        $purchaseItems = $purchaseBill->items()->get();
        $deletedPurchaseItem = $purchaseItems[2];
        $purchaseItems[2]->delete();
        $purchaseItems = $purchaseBill->items()->get();
        $total = $purchaseBill->total;
        foreach($purchaseItems as $purchaseItem) {
            $total += ($purchaseItem->quantity * $purchaseItem->purchaseprice);
        }
        $purchaseBill->update(['total' => $total]);
        
        $purchaseReturnBill = PurchaseReturnBill::factory()
        ->has(PurchaseReturnItem::factory()->count(2)->state(new Sequence(
            ['purchaseitem_id' => $purchaseItems[0]->id, 'quantity' => 20, 'price' => 9.5, 'created_by' => 1],
            ['purchaseitem_id' => $purchaseItems[1]->id, 'quantity' => 10, 'price' => 9.5, 'created_by' => 1]
        )
        ), 'items')
        ->create([
            'purchasebill_id' => $purchaseBill->id, 
            'created_by' => 1]);
        $purchaseReturnItems = $purchaseReturnBill->items()->get();

        $deletedPurchaseReturnItem = $purchaseReturnItems[1];
        $deletedPurchaseReturnItem->delete();

        $purchaseReturnItems = $purchaseReturnBill->items()->get();
        $total = $purchaseReturnBill->total;
        foreach($purchaseReturnItems as $purchaseReturnItem) {
            $total += ($purchaseReturnItem->quantity * $purchaseReturnItem->price);
        }
        $purchaseReturnBill->update(['total' => $total]);
        
        $deletedPurchaseReturnBill = PurchaseReturnBill::factory()
        ->create([
            'purchasebill_id' => $purchaseBill->id, 
            'created_by' => 1]);
        $deletedPurchaseReturnBill->delete();

        $approvedPurchaseReturnBill = PurchaseReturnBill::factory()
        ->has(PurchaseReturnItem::factory()->state([
            'purchaseitem_id' => $purchaseItems[0]->id,
            'quantity' => 40,
            'price' => 14,
            'created_by' => 1
        ]), 'items')
        ->create([
            'purchasebill_id' => $purchaseBill->id, 
            'billstatus' => 'approved',
            'editable' => 0,
            'created_by' => 1]);
        $approvedPurchaseReturnItem = $approvedPurchaseReturnBill->items()->first();

        $this->assertModelExists($purchaseBill);
        $this->assertModelExists($purchaseItems[0]);
        $this->assertModelExists($purchaseReturnBill);
        $this->assertModelExists($purchaseReturnItems[0]);
        $this->assertModelExists($approvedPurchaseReturnBill);
        $this->assertModelExists($approvedPurchaseReturnItem);
        $this->assertModelMissing($deletedPurchaseReturnBill);
        $this->assertModelMissing($deletedPurchaseReturnItem);
        $this->assertModelMissing($deletedPurchaseItem);

        $this->data = [
            "purchasereturnbill_id" => $purchaseReturnBill->id,
            "purchaseitem_id" => $purchaseItems[1]->id,
            "quantity" => 70,
            "price" => 9.5
        ];
        $this->purchaseBill = $purchaseBill;
        $this->purchaseItems = $purchaseItems;
        $this->purchaseReturnBill = $purchaseReturnBill;
        $this->purchaseReturnItems = $purchaseReturnItems;
        $this->deletedPurchaseItem = $deletedPurchaseItem;
        $this->deletedPurchaseReturnBill = $deletedPurchaseReturnBill;
        $this->deletedPurchaseReturnItem = $deletedPurchaseReturnItem;
        $this->approvedPurchaseReturnBill = $approvedPurchaseReturnBill;
        $this->approvedPurchaseReturnItem = $approvedPurchaseReturnItem;
    }

    public function test_read_all_purchasereturnitem() {
        $response = $this->getJson('api/v1/purchases/returns/items');
        $response->assertStatus(200);
    }

    public function test_read_exist_purchasereturnitem() {
        $response = $this->getJson('api/v1/purchases/returns/items/' . $this->purchaseReturnItems[0]->id);
        $response->assertStatus(200);
    }

    public function checkInvalidFieldPostAction($target, $property, $tested_value) {
        $target[$property] = $tested_value;
        $response = $this->postJson('api/v1/purchases/returns/items', $target);
        $response->assertStatus(422);
    }

    public function test_success_create_purchasereturnitem() {
        $data = array_merge([], $this->data);

        $response = $this->postJson('api/v1/purchases/returns/items', $data);
        $response->assertStatus(201);
    }

    public function test_add_purchasereturnitem_into_notexist_purchasereturnbill() {
        $data = array_merge([], $this->data);
        $data["purchasereturnbill_id"] = $this->deletedPurchaseReturnBill->id;

        $response = $this->postJson('api/v1/purchases/returns/items', $data);
        $response->assertStatus(404);
    }
    
    public function test_link_notexist_purchaseitem_with_purchasereturnitem() {
        $data = array_merge([], $this->data);
        $data["purchaseitem_id"] = $this->deletedPurchaseItem->id;

        $response = $this->postJson('api/v1/purchases/returns/items', $data);
        $response->assertStatus(404);
    }

    public function test_add_purchasereturnitem_on_approved_purchasereturnbill() {
        $data = array_merge([], $this->data);
        $data["purchasereturnbill_id"] = $this->approvedPurchaseReturnBill->id;

        $response = $this->postJson('api/v1/purchases/returns/items', $data);
        $response->assertStatus(405);
    }

    public function test_add_purchaseitem_from_underreview_purchasebill_into_purchasereturnitem() {
        $data = array_merge([], $this->data);
        $this->purchaseBill->update([
            'billstatus' => 'underreview',
            'editable' => 1,
        ]);

        $response = $this->postJson('api/v1/purchases/returns/items', $data);
        $response->assertStatus(405);

        $this->purchaseBill->update([
            'billstatus' => 'approved',
            'editable' => 0,
        ]);
    }

    public function test_invalid_purchasereturnbill_id_on_create_purchasereturnitem() {
        $data = array_merge([], $this->data);

        $this->checkInvalidFieldPostAction($data, 'purchasereturnbill_id', '');
        $this->checkInvalidFieldPostAction($data, 'purchasereturnbill_id', 's');
        $this->checkInvalidFieldPostAction($data, 'purchasereturnbill_id', 0);
    }

    public function test_invalid_purchaseitem_id_on_create_purchasereturnitem() {
        $data = array_merge([], $this->data);

        $this->checkInvalidFieldPostAction($data, 'purchaseitem_id', '');
        $this->checkInvalidFieldPostAction($data, 'purchaseitem_id', 's');
        $this->checkInvalidFieldPostAction($data, 'purchaseitem_id', 0);
    }
    
    public function test_invalid_quantity_on_create_purchasereturnitem() {
        $data = array_merge([], $this->data);

        $this->checkInvalidFieldPostAction($data, 'quantity', '');
        $this->checkInvalidFieldPostAction($data, 'quantity', 's');
        $this->checkInvalidFieldPostAction($data, 'quantity', 0);
    }
    
    public function test_invalid_price_on_create_purchasereturnitem() {
        $data = array_merge([], $this->data);

        $this->checkInvalidFieldPostAction($data, 'price', '');
        $this->checkInvalidFieldPostAction($data, 'price', 's');
        $this->checkInvalidFieldPostAction($data, 'price', -1);
    }

    public function test_update_exist_purchasereturnitem() {
        $purchaseReturnItem =  $this->purchaseReturnItems[0];
        $purchaseReturnItem->quantity = 10;
        $purchaseReturnItem->price = 8;

        $response = $this->putJson('api/v1/purchases/returns/items/' . $purchaseReturnItem->id, $purchaseReturnItem->toArray());
        $response->assertStatus(204);
    }

    public function test_update_notexist_purchasereturnitem() {
        $response = $this->putJson('api/v1/purchases/returns/items/' . $this->deletedPurchaseReturnItem->id, $this->deletedPurchaseReturnItem->toArray());
        $response->assertStatus(404);
    }
    
    public function test_update_purchasereturnitem_on_notexist_purchasereturnbill() {
        $purchaseReturnItem =  $this->purchaseReturnItems[0]->toArray();
        $purchaseReturnItem['purchasereturnbill_id'] = $this->deletedPurchaseReturnBill->id;

        $response = $this->putJson('api/v1/purchases/returns/items/' . $purchaseReturnItem['id'], $purchaseReturnItem);
        $response->assertStatus(404);
    }

    public function test_update_purchasereturnitem_on_approved_purchasereturnbill() {
        $response = $this->putJson('api/v1/purchases/returns/items/' . $this->approvedPurchaseReturnItem->id, $this->approvedPurchaseReturnItem->toArray());
        $response->assertStatus(405);
    }

    public function checkInvalidFieldPutAction($target, $property, $tested_value) {
        $target->{$property} = $tested_value;
        $response = $this->putJson('api/v1/purchases/returns/items/' . $target->id, $target->toArray());
        $response->assertStatus(422);
    }

    public function test_invalid_purchasereturnbill_id_on_update_purchasereturnitem() {
        $data = $this->purchaseReturnItems[0];

        $this->checkInvalidFieldPutAction($data, 'purchasereturnbill_id', '');
        $this->checkInvalidFieldPutAction($data, 'purchasereturnbill_id', 's');
        $this->checkInvalidFieldPutAction($data, 'purchasereturnbill_id', 0);
    }

    public function test_invalid_purchaseitem_id_on_update_purchasereturnitem() {
        $data = $this->purchaseReturnItems[0];

        $this->checkInvalidFieldPutAction($data, 'purchaseitem_id', '');
        $this->checkInvalidFieldPutAction($data, 'purchaseitem_id', 's');
        $this->checkInvalidFieldPutAction($data, 'purchaseitem_id', 0);
    }

    public function test_invalid_quantity_on_update_purchasereturnitem() {
        $data = $this->purchaseReturnItems[0];

        $this->checkInvalidFieldPutAction($data, 'quantity', '');
        $this->checkInvalidFieldPutAction($data, 'quantity', 's');
        $this->checkInvalidFieldPutAction($data, 'quantity', 0);
    }

    public function test_invalid_price_on_update_purchasereturnitem() {
        $data = $this->purchaseReturnItems[0];

        $this->checkInvalidFieldPutAction($data, 'price', '');
        $this->checkInvalidFieldPutAction($data, 'price', 's');
        $this->checkInvalidFieldPutAction($data, 'price', -1);
    }

    public function test_destroy_exist_purchasereturnitem() {
        $response = $this->deleteJson('api/v1/purchases/returns/items/' . $this->purchaseReturnItems[0]->id);
        $response->assertStatus(204); 
    }

    public function test_destroy_notexist_purchasereturnitem() {
        $response = $this->deleteJson('api/v1/purchases/returns/items/' . $this->deletedPurchaseItem->id);
        $response->assertStatus(404); 
    }

    public function test_destroy_purchasereturnitem_from_approved_purchasereturnbill() {
        $response = $this->deleteJson('api/v1/purchases/returns/items/' . $this->approvedPurchaseReturnItem->id);
        $response->assertStatus(405); 
    }
}
