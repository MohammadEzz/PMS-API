<?php

namespace Tests\Feature;

use App\Models\Drug;
use App\Models\PurchaseBill;
use App\Models\PurchaseItem;
use App\Models\User;
use ArrayObject;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PurchaseItemTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = [
            "purchasebill_id" => 1,
            "drug_id" => 1,
            "quantity" => 130,
            "bonus" => 10,
            "sellprice" => 15,
            "tax" => 2,
            "discount" => 15,
            "expiredate" => $this->faker()->date()
        ];
    }

    public function test_read_all_purchaseitems() {
        
        $drug = Drug::factory()->create([
            "created_by" => 1
        ]);
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);
        $purchaseItems = PurchaseItem::factory()->create([
            "purchasebill_id" => $purchaseBill->id,
            "drug_id" => $drug->id,    
            "created_by" => 1
        ]);

        
        $this->assertModelExists($drug);
        $this->assertModelExists($purchaseBill);
        $this->assertModelExists($purchaseItems);

        $response = $this->getJson('api/v1/purchases/items');
        $response->assertStatus(200);
    }

    public function test_read_exist_purchaseitem() {
        
        $drug = Drug::factory()->create([
            "created_by" => 1
        ]);
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);
        $purchaseItems = PurchaseItem::factory()->create([
            "purchasebill_id" => $purchaseBill->id,
            "drug_id" => $drug->id,    
            "created_by" => 1
        ]);
        
        $this->assertModelExists($drug);
        $this->assertModelExists($purchaseBill);
        $this->assertModelExists($purchaseItems);

        $response = $this->getJson('api/v1/purchases/items/' . $purchaseItems->id);
        $response->assertStatus(200);
    }

    public function test_read_notexist_purchaseitem() {
        
        $drug = Drug::factory()->create([
            "created_by" => 1
        ]);
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);
        $purchaseItem = PurchaseItem::factory()->create([
            "purchasebill_id" => $purchaseBill->id,
            "drug_id" => $drug->id,    
            "created_by" => 1
        ]);
        $purchaseItem->delete();
        
        $this->assertModelExists($drug);
        $this->assertModelExists($purchaseBill);
        $this->assertModelMissing($purchaseItem);

        $response = $this->getJson('api/v1/purchases/items/' . $purchaseItem->id);
        $response->assertStatus(404);
    }

    public function test_success_create_purchaseitem() {
        
        $drug = Drug::factory()->create([
            "created_by" => 1
        ]);
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);

        $this->assertModelExists($drug);
        $this->assertModelExists($purchaseBill);
        
        $data = [
            "purchasebill_id" => $purchaseBill->id,
            "drug_id" => $drug->id,
            "quantity" => 130,
            "bonus" => 10,
            "sellprice" => 15,
            "tax" => 2,
            "discount" => 15,
            "expiredate" => $this->faker()->date()
        ];

        $response = $this->postJson('api/v1/purchases/items', $data);
        $response->assertStatus(200);
    }

    public function test_create_purchaseitem_on_approved_purchasebill() {
        $drug = Drug::factory()->create([
            "created_by" => 1
        ]);
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1,
            "billstatus" => 'approved',
            "editable" => 0
        ]);

        $this->assertModelExists($drug);
        $this->assertModelExists($purchaseBill);
        
        $data = [
            "purchasebill_id" => $purchaseBill->id,
            "drug_id" => $drug->id,
            "quantity" => 130,
            "bonus" => 10,
            "sellprice" => 15,
            "tax" => 2,
            "discount" => 15,
            "expiredate" => $this->faker()->date()
        ];

        $response = $this->postJson('api/v1/purchases/items', $data);
        $response->assertStatus(405);
    }

    public function checkInvalidFieldPostAction($target, $property, $tested_value) {
        $target[$property] = $tested_value;
        $response = $this->postJson('api/v1/purchases/items', $target);
        $response->assertStatus(422);
    }

    public function test_invalid_purchasebill_id_on_create_purchaseitem() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'purchasebill_id', '');
        $this->checkInvalidFieldPostAction($data, 'purchasebill_id', 's');
        $this->checkInvalidFieldPostAction($data, 'purchasebill_id', 0);
    }

    public function test_invalid_drug_id_on_create_purchaseitem() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'drug_id', '');
        $this->checkInvalidFieldPostAction($data, 'drug_id', 's');
        $this->checkInvalidFieldPostAction($data, 'drug_id', 0);
    }

    public function test_invalid_quantity_on_create_purchaseitem() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'quantity', '');
        $this->checkInvalidFieldPostAction($data, 'quantity', 's');
        $this->checkInvalidFieldPostAction($data, 'quantity', 0);
    }

    public function test_invalid_bonus_on_create_purchaseitem() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'bonus', 's');
        $this->checkInvalidFieldPostAction($data, 'bonus', -1);
    }

    public function test_invalid_sellprice_on_create_purchaseitem() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'sellprice', '');
        $this->checkInvalidFieldPostAction($data, 'sellprice', 's');
        $this->checkInvalidFieldPostAction($data, 'sellprice', -1);
    }

    public function test_invalid_tax_on_create_purchaseitem() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'tax', 's');
        $this->checkInvalidFieldPostAction($data, 'tax', -1);
    }
    
    public function test_invalid_discount_on_create_purchaseitem() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'discount', '');
        $this->checkInvalidFieldPostAction($data, 'discount', 's');
        $this->checkInvalidFieldPostAction($data, 'discount', -1);
    }
    
    public function test_invalid_expiredate_on_create_purchaseitem() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'expiredate', '');
        $this->checkInvalidFieldPostAction($data, 'expiredate', 's');
    }

    public function checkInvalidFieldPutAction($target, $property, $tested_value) {
        $target->{$property} = $tested_value;
        $response = $this->putJson('api/v1/purchases/items/' . $target->id, $target->toArray());
        $response->assertStatus(422);
    }

    public function test_update_exist_purchaseitem() {
        
        $drugs = Drug::factory()->count(2)->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->count(2)->state(new Sequence(
            ["drug_id"=>$drugs[0]->id, "bonus" => 5, "created_by" => 1],
            ["drug_id"=>$drugs[1]->id, "bonus" => 8, "created_by" => 1]
        )), 'items')
        ->create([ "created_by" => 1 ]);
        $purchaseBillItems = $purchaseBill->items()->get();
        foreach($purchaseBillItems as $billItem) {
            $purchaseBill->update([
                "total" => $purchaseBill->total + ($billItem->quantity * $billItem->purchaseprice)
            ]);
        }

        
        $this->assertModelExists($drugs->first());
        $this->assertModelExists($purchaseBill);
        $this->assertModelExists($purchaseBill->items()->first());

        $updatedItem = $purchaseBill->items()->first();
        $updatedItem->quantity = 100;
        $updatedItem->bonus = 17;
        $updatedItem->discount = 30;
        $updatedItem->tax = 3;
        $response = $this->putJson('api/v1/purchases/items/' . $purchaseBill->items()->first()->id, $updatedItem->toArray());
        $response->assertStatus(201);
    }

    public function test_invalid_drug_id_on_update_purchaseitem() {
        
        $drug = Drug::factory()->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->state(new Sequence(
            ["drug_id"=>$drug->id, "created_by" => 1]
        )), 'items')
        ->create([ "created_by" => 1 ]);
        $purchaseBillItem = $purchaseBill->items()->first();

        $this->checkInvalidFieldPutAction($purchaseBillItem, 'drug_id', '');
        $this->checkInvalidFieldPutAction($purchaseBillItem, 'drug_id', 's');
        $this->checkInvalidFieldPutAction($purchaseBillItem, 'drug_id', 0);
    }

    public function test_invalid_quantity_on_update_purchaseitem() {
        
        $drug = Drug::factory()->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->state(new Sequence(
            ["drug_id"=>$drug->id, "created_by" => 1]
        )), 'items')
        ->create([ "created_by" => 1 ]);
        $purchaseBillItem = $purchaseBill->items()->first();

        $this->checkInvalidFieldPutAction($purchaseBillItem, 'quantity', '');
        $this->checkInvalidFieldPutAction($purchaseBillItem, 'quantity', 's');
        $this->checkInvalidFieldPutAction($purchaseBillItem, 'quantity', 0);
    }
    
    public function test_invalid_bonus_on_update_purchaseitem() {
        
        $drug = Drug::factory()->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->state(new Sequence(
            ["drug_id"=>$drug->id, "created_by" => 1]
        )), 'items')
        ->create([ "created_by" => 1 ]);
        $purchaseBillItem = $purchaseBill->items()->first();

        $this->checkInvalidFieldPutAction($purchaseBillItem, 'bonus', 's');
        $this->checkInvalidFieldPutAction($purchaseBillItem, 'bonus', -1);
    }

    public function test_invalid_sellprice_on_update_purchaseitem() {
        
        $drug = Drug::factory()->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->state(new Sequence(
            ["drug_id"=>$drug->id, "created_by" => 1]
        )), 'items')
        ->create([ "created_by" => 1 ]);
        $purchaseBillItem = $purchaseBill->items()->first();

        $this->checkInvalidFieldPutAction($purchaseBillItem, 'sellprice', '');
        $this->checkInvalidFieldPutAction($purchaseBillItem, 'sellprice', 's');
        $this->checkInvalidFieldPutAction($purchaseBillItem, 'sellprice', -1);
    }

    public function test_invalid_tax_on_update_purchaseitem() {
        
        $drug = Drug::factory()->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->state(new Sequence(
            ["drug_id"=>$drug->id, "created_by" => 1]
        )), 'items')
        ->create([ "created_by" => 1 ]);
        $purchaseBillItem = $purchaseBill->items()->first();

        $this->checkInvalidFieldPutAction($purchaseBillItem, 'tax', 's');
        $this->checkInvalidFieldPutAction($purchaseBillItem, 'tax', -1);
    }

    public function test_invalid_discount_on_update_purchaseitem() {
        
        $drug = Drug::factory()->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->state(new Sequence(
            ["drug_id"=>$drug->id, "created_by" => 1]
        )), 'items')
        ->create([ "created_by" => 1 ]);
        $purchaseBillItem = $purchaseBill->items()->first();

        $this->checkInvalidFieldPutAction($purchaseBillItem, 'discount', '');
        $this->checkInvalidFieldPutAction($purchaseBillItem, 'discount', 's');
        $this->checkInvalidFieldPutAction($purchaseBillItem, 'discount', -1);
    }
    
    public function test_invalid_expiredate_on_update_purchaseitem() {
        
        $drug = Drug::factory()->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->state(new Sequence(
            ["drug_id"=>$drug->id, "created_by" => 1]
        )), 'items')
        ->create([ "created_by" => 1 ]);
        $purchaseBillItem = $purchaseBill->items()->first();

        $this->checkInvalidFieldPutAction($purchaseBillItem, 'expiredate', '');
        $this->checkInvalidFieldPutAction($purchaseBillItem, 'expiredate', 's');
    }

    public function test_update_notexist_purchaseitem() {
        
        $drugs = Drug::factory()->count(2)->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->count(2)->state(new Sequence(
            ["drug_id"=>$drugs[0]->id, "bonus" => 5, "created_by" => 1],
            ["drug_id"=>$drugs[1]->id, "bonus" => 8, "created_by" => 1]
        )), 'items')
        ->create([ "created_by" => 1 ]);
        $purchaseBillItems = $purchaseBill->items()->get();
        foreach($purchaseBillItems as $billItem) {
            $purchaseBill->update([
                "total" => $purchaseBill->total + ($billItem->quantity * $billItem->purchaseprice)
            ]);
        }

        $firstItem = $purchaseBill->items()->first();
        $firstItem->delete();

        
        $this->assertModelExists($drugs->first());
        $this->assertModelExists($purchaseBill);
        $this->assertModelMissing($firstItem);

        $firstItem->quantity = 100;

        $response = $this->putJson('api/v1/purchases/items/' . $firstItem->id, $firstItem->toArray());
        $response->assertStatus(404);
    } 

    public function test_update_purchaseitem_on_approved_purchasebill() {
        
        $drugs = Drug::factory()->count(2)->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->count(2)->state(new Sequence(
            ["drug_id"=>$drugs[0]->id, "bonus" => 5, "created_by" => 1],
            ["drug_id"=>$drugs[1]->id, "bonus" => 8, "created_by" => 1]
        )), 'items')
        ->create([ 
            "billstatus" => "approved",
            "editable" => 0,
            "created_by" => 1 
        ]);
        $purchaseBillItems = $purchaseBill->items()->get();
        foreach($purchaseBillItems as $billItem) {
            $purchaseBill->update([
                "total" => $purchaseBill->total + ($billItem->quantity * $billItem->purchaseprice)
            ]);
        }

        $firstItem = $purchaseBill->items()->first();

        
        $this->assertModelExists($drugs->first());
        $this->assertModelExists($purchaseBill);
        $this->assertModelExists($firstItem);

        $response = $this->putJson('api/v1/purchases/items/' . $firstItem->id, $firstItem->toArray());
        $response->assertStatus(503);
    }

    public function test_destroy_exist_purchaseitem() {
        
        $drug = Drug::factory()->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->state(new Sequence(
            ["drug_id"=>$drug->id, "created_by" => 1]
        )), 'items')
        ->create([ "created_by" => 1 ]);
        $purchaseBillItem = $purchaseBill->items()->first();

        
        $this->assertModelExists($drug);
        $this->assertModelExists($purchaseBill);
        $this->assertModelExists($purchaseBillItem);
        
        $response = $this->deleteJson('api/v1/purchases/items/' . $purchaseBillItem->id);
        $response->assertStatus(201);
    }

    public function test_destroy_notexist_purchaseitem() {
        
        $drug = Drug::factory()->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->state(new Sequence(
            ["drug_id"=>$drug->id, "created_by" => 1]
        )), 'items')
        ->create([ "created_by" => 1 ]);
        $purchaseBillItem = $purchaseBill->items()->first();
        $purchaseBillItem->delete();

        
        $this->assertModelExists($drug);
        $this->assertModelExists($purchaseBill);
        $this->assertModelMissing($purchaseBillItem);

        $response = $this->deleteJson('api/v1/purchases/items/' . $purchaseBillItem->id);
        $response->assertStatus(404);
    }

    public function test_destroy_purchaseitem_on_approved_purchasebill() {
        
        $drug = Drug::factory()->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->state(new Sequence(
            ["drug_id"=>$drug->id, "created_by" => 1]
        )), 'items')
        ->create([ 
            "billstatus" => "approved",
            "editable" => 0,
            "created_by" => 1 ]);
        $purchaseBillItem = $purchaseBill->items()->first();

        
        $this->assertModelExists($drug);
        $this->assertModelExists($purchaseBill);
        $this->assertModelExists($purchaseBillItem);
        
        $response = $this->deleteJson('api/v1/purchases/items/' . $purchaseBillItem->id);
        $response->assertStatus(503);
    }
}
