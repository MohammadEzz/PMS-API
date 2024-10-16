<?php

namespace Tests\Feature;

use App\Models\SalesBill;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SalesBillTest extends TestCase
{
    protected $data,
    $salesBill, $deletedSalesBill,
    $approvedSalesBill;
    public function setUp() : void {
        parent::setUp();
        $salesBills = SalesBill::factory()->count(3)->state(new Sequence(
            ['client_id' => 1],
            ['client_id' => 2],
            ['client_id' => 3]))->create();
        $salesBill = $salesBills[0];

        $deletedSalesBill = $salesBills[1];
        $deletedSalesBill->delete();

        $approvedSalesBill = $salesBills[2];
        $approvedSalesBill->update(['billstatus' => 'approved', 'editable' => 0, 'paidstatus' => 'paid']);

        $this->assertModelExists($salesBill);
        $this->assertModelMissing($deletedSalesBill);
        $this->assertModelExists($approvedSalesBill);

        $this->data = [
            'discount' => 5.5
        ];
        $this->salesBill = $salesBill;
        $this->deletedSalesBill = $deletedSalesBill;
        $this->approvedSalesBill = $approvedSalesBill;
    }
    public function test_read_all_salesbill() {
        $response = $this->getJson('api/v1/sales');
        $response->assertStatus(200);
    }

    public function test_read_exist_salesbill() {
        $response = $this->getJson('api/v1/sales/' . $this->salesBill->id);
        $response->assertStatus(200);
    }

    public function test_read_notexist_salesbill() {
        $response = $this->getJson('api/v1/sales/' . $this->deletedSalesBill->id);
        $response->assertStatus(404);
    }

    public function test_success_create_salesbill() {
        $response = $this->postJson('api/v1/sales', $this->data);
        $response->assertStatus(201);
    }

    public function checkInvalidFieldPostAction($target, $property, $tested_value) {
        $target[$property] = $tested_value;
        $response = $this->postJson('api/v1/sales', $target);
        $response->assertStatus(422);
    }

    public function test_update_exist_salesbill() {
        $this->salesBill->client_id = 55;

        $response = $this->putJson('api/v1/sales/' . $this->salesBill->id, $this->salesBill->toArray());
        $response->assertStatus(201);
    }

    public function test_update_notexist_salesbill() {
        $this->deletedSalesBill->client_id = 100;

        $response = $this->putJson('api/v1/sales/' . $this->deletedSalesBill->id, $this->deletedSalesBill->toArray());
        $response->assertStatus(404);
    }

    public function test_update_approved_salesbill() {
        $this->approvedSalesBill->client_id = 200;

        $response = $this->putJson('api/v1/sales/' . $this->approvedSalesBill->id, $this->approvedSalesBill->toArray());
        $response->assertStatus(405);
    }

    public function checkInvalidFieldPutAction($target, $property, $tested_value) {
        $target->{$property} = $tested_value;
        $response = $this->putJson('api/v1/sales/' . $target->id, $target->toArray());
        $response->assertStatus(422);
    }

    public function test_invalid_client_id_on_update_salesbill() {
        $this->checkInvalidFieldPutAction($this->salesBill, 'client_id', 's');
        $this->checkInvalidFieldPutAction($this->salesBill, 'client_id', 0);
    }

    public function test_invalid_discount_on_update_salesbill() {
        $this->checkInvalidFieldPutAction($this->salesBill, 'discount', 's');
        $this->checkInvalidFieldPutAction($this->salesBill, 'discount', -1);
    }

    public function test_destroy_exist_salesbill() {
        $response = $this->deleteJson('api/v1/sales/' . $this->salesBill->id);
        $response->assertStatus(204);
    }

    public function test_destroy_notexist_salesbill() {
        $response = $this->deleteJson('api/v1/sales/' . $this->deletedSalesBill->id);
        $response->assertStatus(404);
    }

    public function test_destroy_approved_salesbill() {
        $response = $this->deleteJson('api/v1/sales/' . $this->approvedSalesBill->id);
        $response->assertStatus(405);
    }
}
