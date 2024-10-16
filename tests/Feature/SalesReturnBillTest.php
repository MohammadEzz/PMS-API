<?php

namespace Tests\Feature;

use App\Models\SalesReturnBill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SalesReturnBillTest extends TestCase
{
    protected $data,
    $salesReturnBill, $deletedSalesReturnBill,
    $approvedSalesReturnBill;
    public function setUp() : void {
        parent::setUp();

        $salesReturnBills = SalesReturnBill::factory()->count(3)->create();
        $salesReturnBill = $salesReturnBills[0];

        $deletedSalesReturnBill = $salesReturnBills[1];
        $deletedSalesReturnBill->delete();

        $approvedSalesReturnBill = $salesReturnBills[2];
        $approvedSalesReturnBill->update(['billstatus' => 'approved', 'editable' => 0]);

        $this->assertModelExists($salesReturnBill);
        $this->assertModelMissing($deletedSalesReturnBill);
        $this->assertModelExists($approvedSalesReturnBill);

        $this->data = [
            'discount' => 5,
            'salesbill_id' => 1
        ];
        $this->salesReturnBill = $salesReturnBill;
        $this->deletedSalesReturnBill = $deletedSalesReturnBill;
        $this->approvedSalesReturnBill = $approvedSalesReturnBill;

    }

    public function test_read_all_salesreturnbill() {
        $response = $this->getJson('api/v1/sales/returns');
        $response->assertStatus(200);
    }

    public function test_read_exist_salesreturnbill() {
        $response = $this->getJson('api/v1/sales/returns/' . $this->salesReturnBill->id);
        $response->assertStatus(200);
    }
    
    public function test_read_notexist_salesreturnbill() {
        $response = $this->getJson('api/v1/sales/returns/' . $this->deletedSalesReturnBill->id);
        $response->assertStatus(404);
    }

    public function test_success_create_salesreturnbill() {
        $response = $this->postJson('api/v1/sales/returns', $this->data);
        $response->assertStatus(201);
    }

    public function test_update_exist_salesreturnbill() {
        $response = $this->putJson('api/v1/sales/returns/' . $this->salesReturnBill->id, $this->data);
        $response->assertStatus(200);
    }

    public function test_update_notexist_salesreturnbill() {
        $response = $this->putJson('api/v1/sales/returns/' . $this->deletedSalesReturnBill->id, $this->data);
        $response->assertStatus(404);
    }

    public function test_update_approved_salesreturnbill() {
        $response = $this->putJson('api/v1/sales/returns/' . $this->approvedSalesReturnBill->id, $this->data);
        $response->assertStatus(405);
    }

    public function checkInvalidFieldPutAction($target, $property, $tested_value) {
        $target->{$property} = $tested_value;
        $response = $this->putJson('api/v1/sales/returns/' . $target->id, $target->toArray());
        $response->assertStatus(422);
    }

    public function test_invalid_salesbill_id_on_update_salesreturnbill() {
        $this->checkInvalidFieldPutAction($this->salesReturnBill, 'salesbill_id', 's');
        $this->checkInvalidFieldPutAction($this->salesReturnBill, 'salesbill_id', 0);
    }

    public function test_invalid_discount_on_update_salesreturnbill() {
        $this->checkInvalidFieldPutAction($this->salesReturnBill, 'discount', 's');
        $this->checkInvalidFieldPutAction($this->salesReturnBill, 'discount', -1);
    }

    public function test_destroy_exist_salesreturnbill() {
        $response = $this->deleteJson('api/v1/sales/returns/' . $this->salesReturnBill->id);
        $response->assertStatus(204);
    }

    public function test_destroy_notexist_salesreturnbill() {
        $response = $this->deleteJson('api/v1/sales/returns/' . $this->deletedSalesReturnBill->id);
        $response->assertStatus(404);
    }

    public function test_destroy_approved_salesreturnbill() {
        $response = $this->deleteJson('api/v1/sales/returns/' . $this->approvedSalesReturnBill->id);
        $response->assertStatus(405);
    }

}
