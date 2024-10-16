<?php

namespace Tests\Feature;

use App\Models\Debit;
use App\Models\Drug;
use App\Models\Inventory;
use App\Models\Price;
use App\Models\PurchaseBill;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturnBill;
use App\Models\PurchaseReturnItem;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseReturnBillTest extends TestCase
{
    use WithFaker;

    protected $data,
    $purchaseBill, $purchaseReturnBill,
    $deletedPurchaseBill, $deletedPurchaseReturnBill,
    $approvedPurchaseReturnBill;
    public function setUp(): void {
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

        $deletedPurchaseBill = PurchaseBill::factory()->create(["created_by" => 1]);
        $deletedPurchaseBill->delete();

        $purchaseReturnBills = PurchaseReturnBill::factory()->count(2)->create([
            "purchasebill_id" => $purchaseBill->id,
            "created_by" => 1
        ]);

        $purchaseReturnBill = $purchaseReturnBills[0];
        PurchaseReturnItem::factory()->count(2)->state(new Sequence(
            ['purchaseitem_id' => $purchaseItems[0]->id, 'quantity' => 20, 'price' => 8],
            ['purchaseitem_id' => $purchaseItems[1]->id, 'quantity' => 30, 'price' => 7]
            
        ))->create([
            'purchasereturnbill_id' => $purchaseReturnBill->id,
            'created_by' => 1
        ]);
        $total = 0;
        $purchaseReturnBillItems = $purchaseReturnBill->items()->get();
        foreach($purchaseReturnBillItems as $purchaseReturnBillItem) {
            $total += ($purchaseReturnBillItem->quantity * $purchaseReturnBillItem->price);
        }
        $purchaseReturnBill->update(['total' => $total, 'updated_by' => 1]);

        $deletedPurchaseReturnBill = $purchaseReturnBills[1];
        $deletedPurchaseReturnBill->delete();

        $approvedPurchaseReturnBill = PurchaseReturnBill::factory()->create([
            "purchasebill_id" => $purchaseBill->id,
            "billstatus" => "approved",
            "editable" => 0,
            "created_by" => 1
        ]);

        $this->assertModelExists($purchaseBill);
        $this->assertModelMissing($deletedPurchaseBill);
        $this->assertModelExists($purchaseReturnBill);
        $this->assertModelMissing($deletedPurchaseReturnBill);
        $this->assertModelExists($approvedPurchaseReturnBill);

        $this->data = [
            "purchasebill_id" => $purchaseBill->id,
            "issuedate" => $this->faker()->date(),
        ];
        $this->purchaseBill = $purchaseBill;
        $this->deletedPurchaseBill = $deletedPurchaseBill;
        $this->purchaseReturnBill = $purchaseReturnBill;
        $this->deletedPurchaseReturnBill = $deletedPurchaseReturnBill;
        $this->approvedPurchaseReturnBill = $approvedPurchaseReturnBill;
    }
    
    public function test_read_all_purchasereturnbills() {
        $response = $this->getJson('api/v1/purchases/returns');
        $response->assertStatus(200);
    }

    public function test_read_exist_purchasereturnbill() {
        $response = $this->getJson('api/v1/purchases/returns/' . $this->purchaseReturnBill->id);
        $response->assertStatus(200);   
    }

    public function test_read_notexist_purchasereturnbill() {
        $response = $this->getJson('api/v1/purchases/returns/' . $this->deletedPurchaseReturnBill->id);
        $response->assertStatus(404);   
    }

    public function test_success_create_purchasereturnbill() {
        $response = $this->postJson('api/v1/purchases/returns', $this->data);
        $response->assertStatus(201);
    }

    public function test_link_purchasereturnbill_with_notexist_purchasebill() {
        $data = array_merge($this->data, ['purchasebill_id' => $this->deletedPurchaseBill->id]);
        
        $response = $this->postJson('api/v1/purchases/returns', $data);
        $response->assertStatus(404);
    }

    public function test_link_purchasereturnbill_with_underreview_purchasebill() {
        $this->purchaseBill->update([
            'billstatus' => 'underreview',
            'editable' => 1
        ]);

        $response = $this->postJson('api/v1/purchases/returns', $this->data);
        $response->assertStatus(405);

        $this->purchaseBill->update([
            'billstatus' => 'approved',
            'editable' => 0
        ]);
    }

    public function checkInvalidFieldPostAction($target, $property, $tested_value) {
        $target[$property] = $tested_value;
        $response = $this->postJson('api/v1/purchases/returns', $target);
        $response->assertStatus(422);
    }

    public function test_invalid_purchasebill_id_on_create_purchasereturnbill() {
        $this->checkInvalidFieldPostAction($this->data, 'purchasebill_id', '');
        $this->checkInvalidFieldPostAction($this->data, 'purchasebill_id', 's');
        $this->checkInvalidFieldPostAction($this->data, 'purchasebill_id', 0);
    }

    public function test_invalid_issuedate_on_create_purchasereturnbill() {
        $this->checkInvalidFieldPostAction($this->data, 'issuedate', '');
        $this->checkInvalidFieldPostAction($this->data, 'issuedate', 's');
    }

    public function checkInvalidFieldPutAction($target, $property, $tested_value) {
        $target->{$property} = $tested_value;
        $response = $this->putJson('api/v1/purchases/returns/' . $target->id, $target->toArray());
        $response->assertStatus(422);
    }

    public function test_update_exist_purchasereturnbill() {  
        $this->purchaseReturnBill->issuedate = "1988-09-15";

        $response = $this->putJson('api/v1/purchases/returns/' . $this->purchaseReturnBill->id, $this->purchaseReturnBill->toArray());
        $response->assertStatus(204);
    }

    public function test_update_notexist_purchasereturnbill() {
        $this->deletedPurchaseReturnBill->issuedate = "1988-09-15";

        $response = $this->putJson('api/v1/purchases/returns/' . $this->deletedPurchaseReturnBill->id, $this->deletedPurchaseReturnBill->toArray());
        $response->assertStatus(404);
    }

    public function test_update_approved_purchasereturnbill() {
        $response = $this->putJson('api/v1/purchases/returns/' . $this->approvedPurchaseReturnBill->id, $this->approvedPurchaseReturnBill->toArray());
        $response->assertStatus(405);
    }

    public function test_invalid_purchasebill_id_on_update_purchasereturnbill() {
        $this->checkInvalidFieldPutAction($this->purchaseReturnBill, 'purchasebill_id', '');
        $this->checkInvalidFieldPutAction($this->purchaseReturnBill, 'purchasebill_id', 's');
        $this->checkInvalidFieldPutAction($this->purchaseReturnBill, 'purchasebill_id', 0);
    }

    public function test_invalid_issuedate_on_update_purchasereturnbill() {
        $this->checkInvalidFieldPutAction($this->purchaseReturnBill, 'issuedate', '');
        $this->checkInvalidFieldPutAction($this->purchaseReturnBill, 'issuedate', 's');
    }

    public function test_destroy_exist_purchasereturnbill() {
        $response = $this->deleteJson('api/v1/purchases/returns/' . $this->purchaseReturnBill->id);
        $response->assertStatus(204);
    }
    
    public function test_destroy_notexist_purchasereturnbill() {
        $response = $this->deleteJson('api/v1/purchases/returns/' . $this->deletedPurchaseReturnBill->id);
        $response->assertStatus(404);
    }

    public function test_destroy_approved_purchasereturnbill() {
        $response = $this->deleteJson('api/v1/purchases/returns/' . $this->approvedPurchaseReturnBill->id);
        $response->assertStatus(405);
    }

    public function test_success_approving_purchasereturnbill() {
        $response = $this->postJson('api/v1/purchases/returns/' . $this->purchaseReturnBill->id . '/approve');
        $response->assertStatus(204);
    }

    public function test_success_approving_notexist_purchasereturnbill() {
        $response = $this->postJson('api/v1/purchases/returns/' . $this->deletedPurchaseReturnBill->id . '/approve');
        $response->assertStatus(404);
    }

    public function test_success_approving_approved_purchasereturnbill() {
        $response = $this->postJson('api/v1/purchases/returns/' . $this->approvedPurchaseReturnBill->id . '/approve');
        $response->assertStatus(405);
    }

    public function test_failed_approving_purchasereturnbill_with_items_greater_than_inventory() {
        $purchaseReturnItems = $this->purchaseReturnBill->items()->get();
        $purchaseReturnItems[0]->update(['quantity' => 60]);
        $purchaseReturnItems[1]->update(['quantity' => 70]);
        
        $response = $this->postJson('api/v1/purchases/returns/' . $this->purchaseReturnBill->id . '/approve');
        $response->dump();
        $response->assertStatus(405);
    }
}
