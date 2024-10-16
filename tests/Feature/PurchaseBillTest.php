<?php

namespace Tests\Feature;

use App\Models\Drug;
use App\Models\PurchaseBill;
use App\Models\PurchaseItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PurchaseBillTest extends TestCase
{
    // use RefreshDatabase;
    use WithFaker;

    public function test_read_all_purchasebills() {
        
        $purchaseBills = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);
        
        $this->assertModelExists($purchaseBills);

        $response = $this->getJson('api/v1/purchases');
        $response->assertStatus(200);
    }

    public function test_read_exist_purchasebill() {
        
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);

        
        $this->assertModelExists($purchaseBill);

        $response = $this->getJson("api/v1/purchases/" . $purchaseBill->id);

        $response->assertStatus(200);
    }

    public function test_read_notexist_purchasebill() {
        
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);
        $purchaseBill->delete();

        
        $this->assertModelMissing($purchaseBill);

        $response = $this->getJson("api/v1/purchases/" . $purchaseBill->id);
        $response->assertStatus(404);
    }

    public function test_success_create_purchasebill() {
        $data=[
            "supplier_id" => 1,
            "dealer_id" => 1,
            "issuedate" => $this->faker()->date(),
            "billnumber" => $this->faker()->randomNumber(5, true),
            "paymenttype" => "prepaid",
            "billstatus" => "underreview",
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $response = $this->postJson("api/v1/purchases", $data);
        $response->assertStatus(200);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function checkInvalidFieldPostAction($target, $property, $tested_value) {
        $target[$property] = $tested_value;
        $response = $this->postJson('api/v1/purchases', $target);
        $response->assertStatus(422);
    }

    public function test_invalid_supplier_id_on_create_purchasebill() {
        $data=[
            "dealer_id" => 1,
            "issuedate" => $this->faker()->date(),
            "billnumber" => $this->faker()->randomNumber(5, true),
            "paymenttype" => "prepaid",
            "billstatus" => "underreview",
        ];

        $this->checkInvalidFieldPostAction($data, 'supplier_id', '');
        $this->checkInvalidFieldPostAction($data, 'supplier_id', 's');
        $this->checkInvalidFieldPostAction($data, 'supplier_id', 0);
    }
    
    public function test_invalid_dealer_id_on_create_purchasebill() {
        $data=[
            "supplier_id" => 1,
            "issuedate" => $this->faker()->date(),
            "billnumber" => $this->faker()->randomNumber(5, true),
            "paymenttype" => "prepaid",
            "billstatus" => "underreview",
        ];

        $this->checkInvalidFieldPostAction($data, 'dealer_id', '');
        $this->checkInvalidFieldPostAction($data, 'dealer_id', 's');
        $this->checkInvalidFieldPostAction($data, 'dealer_id', 0);
    }

    public function test_invalid_billnumber_on_create_purchasebill() {
        $data=[
            "supplier_id" => 1,
            "dealer_id" => 1,
            "issuedate" => $this->faker()->date(),
            "paymenttype" => "prepaid",
            "billstatus" => "underreview",
        ];

        $this->checkInvalidFieldPostAction($data, 'billnumber', '');
        $this->checkInvalidFieldPostAction($data, 'billnumber', 's');
        $this->checkInvalidFieldPostAction($data, 'billnumber', 0);
    }

    public function test_invalid_issuedate_on_create_purchasebill() {
        $data=[
            "supplier_id" => 1,
            "dealer_id" => 1,
            "billnumber" => $this->faker()->randomNumber(5, true),
            "paymenttype" => "prepaid",
            "billstatus" => "underreview",
        ];

        $this->checkInvalidFieldPostAction($data, 'issuedate', '');
        $this->checkInvalidFieldPostAction($data, 'issuedate', 's');
    }

    public function test_invalid_paymenttype_on_create_purchasebill() {
        $data=[
            "supplier_id" => 1,
            "dealer_id" => 1,
            "billnumber" => $this->faker()->randomNumber(5, true),
            "issuedate" => $this->faker()->date(),
            "billstatus" => "underreview",
        ];

        $this->checkInvalidFieldPostAction($data, 'paymenttype', '');
        $this->checkInvalidFieldPostAction($data, 'paymenttype', 's');
    }
    
    public function test_invalid_billstatus_on_create_purchasebill() {
        $data=[
            "supplier_id" => 1,
            "dealer_id" => 1,
            "billnumber" => $this->faker()->randomNumber(5, true),
            "issuedate" => $this->faker()->date(),
            "paymenttype" => "prepaid",
        ];

        $this->checkInvalidFieldPostAction($data, 'billstatus', '');
        $this->checkInvalidFieldPostAction($data, 'billstatus', 's');
    }
    
    public function checkInvalidFieldPutAction($target, $property, $tested_value) {
        $target->{$property} = $tested_value;
        $response = $this->putJson('api/v1/purchases/' . $target->id, $target->toArray());
        $response->assertStatus(422);
    }

    public function test_update_exist_purchasebill() {
        
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);

        
        $this->assertModelExists($purchaseBill);

        $purchaseBill->supplier_id = 6;
        $purchaseBill->dealer_id = 6;

        $response = $this->putJson('api/v1/purchases/' . $purchaseBill->id, $purchaseBill->toArray());
        $response->assertStatus(201);
    }

    public function test_invalid_supplier_id_on_update_purchasebill(){
        
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);

        $this->checkInvalidFieldPutAction($purchaseBill, 'supplier_id', '');
        $this->checkInvalidFieldPutAction($purchaseBill, 'supplier_id', 's');
        $this->checkInvalidFieldPutAction($purchaseBill, 'supplier_id', 0);
    }

    public function test_invalid_dealer_id_on_update_purchasebill(){
        
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);

        $this->checkInvalidFieldPutAction($purchaseBill, 'dealer_id', '');
        $this->checkInvalidFieldPutAction($purchaseBill, 'dealer_id', 's');
        $this->checkInvalidFieldPutAction($purchaseBill, 'dealer_id', 0);
    }
    
    public function test_invalid_billnumber_on_update_purchasebill(){
        
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);

        $this->checkInvalidFieldPutAction($purchaseBill, 'billnumber', '');
        $this->checkInvalidFieldPutAction($purchaseBill, 'billnumber', 's');
        $this->checkInvalidFieldPutAction($purchaseBill, 'billnumber', 0);
    }

    public function test_invalid_issuedate_on_update_purchasebill(){
        
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);

        $this->checkInvalidFieldPutAction($purchaseBill, 'issuedate', '');
        $this->checkInvalidFieldPutAction($purchaseBill, 'issuedate', 's');
    } 

    public function test_invalid_paymenttype_on_update_purchasebill(){
        
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);

        $this->checkInvalidFieldPutAction($purchaseBill, 'paymenttype', '');
        $this->checkInvalidFieldPutAction($purchaseBill, 'paymenttype', 's');
    }

    public function test_invalid_billstatus_on_update_purchasebill(){
        
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);

        $this->checkInvalidFieldPutAction($purchaseBill, 'billstatus', '');
        $this->checkInvalidFieldPutAction($purchaseBill, 'billstatus', 's');
    }

    public function test_update_notexist_purchasebill() {
        
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);
        $purchaseBill->delete();

        
        $this->assertModelMissing($purchaseBill);

        $purchaseBill->supplier_id = 20;

        $response = $this->putJson('api/v1/purchases/' . $purchaseBill->id, $purchaseBill->toArray());
        $response->assertStatus(404);
    }

    public function test_update_approved_purchasebill() {
        
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1,
            "billstatus" => "approved",
            "editable" => 0
        ]);

        
        $this->assertModelExists($purchaseBill);

        $response = $this->putJson('api/v1/purchases/' . $purchaseBill->id, $purchaseBill->toArray());
        $response->assertStatus(503);
    }

    public function test_destroy_exist_purchasebill() {
        
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);

        
        $this->assertModelExists($purchaseBill);

        $response = $this->deleteJson('api/v1/purchases/' . $purchaseBill->id);
        $response->assertStatus(201);
    }

    public function test_destroy_approved_purchasebill() {
        
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1,
            "billstatus" => "approved",
            "editable" => 0
        ]);

        
        $this->assertModelExists($purchaseBill);

        $response = $this->deleteJson('api/v1/purchases/' . $purchaseBill->id);
        $response->assertStatus(503);
    }

    public function test_destroy_notexist_purchasebill() {
        
        $purchaseBill = PurchaseBill::factory()->create([
            "created_by" => 1
        ]);
        $purchaseBill->delete();

        
        $this->assertModelMissing($purchaseBill);

        $response = $this->deleteJson('api/v1/purchases/' . $purchaseBill->id);
        $response->assertStatus(404);
    }

    public function test_success_approving_purchasebill() {
        
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

        $response = $this->postJson('api/v1/purchases/' . $purchaseBill->id . '/approve');
        $response->assertStatus(204);
    }

    public function test_approving_notexist_purchasebill() {
        
        $drugs = Drug::factory()->count(2)->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->count(2)->state(new Sequence(
            ["drug_id"=>$drugs[0]->id, "bonus"=>5, "created_by"=>1],
            ["drug_id"=>$drugs[1]->id, "bonus"=>8, "created_by"=>1]
        )), 'items')
        ->create([ "created_by" => 1 ]);
        
        $purchaseBill->delete();

        
        $this->assertModelExists($drugs->first());
        $this->assertModelMissing($purchaseBill);

        $response = $this->postJson('api/v1/purchases/' . $purchaseBill->id . '/approve');
        $response->assertStatus(404);
    } 

    public function test_approving_approved_purchasebill() {
        
        $drugs = Drug::factory()->count(2)->create(["created_by" => 1]);
        $purchaseBill = PurchaseBill::factory()
        ->has(PurchaseItem::factory()->count(2)->state(new Sequence(
            ["drug_id"=>$drugs[0]->id, "bonus"=>5, "created_by"=>1],
            ["drug_id"=>$drugs[1]->id, "bonus"=>8, "created_by"=>1]
        )), 'items')
        ->create([ "created_by" => 1, 'billstatus' => 'approved', 'editable' => 0 ]);
        
        
        $this->assertModelExists($drugs->first());
        $this->assertModelExists($purchaseBill);

        $response = $this->postJson('api/v1/purchases/' . $purchaseBill->id . '/approve');
        $response->assertStatus(405);
    } 
}
