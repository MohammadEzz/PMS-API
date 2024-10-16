<?php

namespace Tests\Feature;

use App\Models\Drug;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DrugTest extends TestCase
{
    use WithFaker;
    // use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_read_all_drugs()
    {
        $user = User::factory()->create();
        $drugs = Drug::factory()->count(3)->create(["created_by" => $user->id]);

        $this->assertModelExists($user);
        $this->assertModelExists($drugs[0]);
        
        $response = $this->getJson('api/v1/drugs');

        $response->assertStatus(200);
    }

    public function test_read_exist_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();

        $response = $this->getJson('api/v1/drugs/' . $drug->id);

        $response->assertStatus(200);
    }

    public function test_read_notexist_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();
        Drug::destroy($drug->id);

        $response = $this->getJson('api/v1/drugs/' . $drug->id);

        $response->assertStatus(404);
    }

    public function test_success_create_drug() {
        $user = User::factory()->create();
        $drug = [
            "name" => "Drug 1",
            "brandname" => "Drug 1",
            "type" => 1,
            "description" => "Drug Descriptions post",
            "barcode" => 123456789123,
            "middleunitnum" => 4,
            "smallunitnum" => 20,
            "visible" => 0,
            "created_by" => $user->id
        ];

        $response = $this->postJson('api/v1/drugs', $drug);

        $response->assertStatus(200);
    }

    public function test_invalid_name_on_create_drug() {
        $user = User::factory()->create();
        $drug = [
            "brandname" => "Drug 1",
            "type" => 1,
            "description" => "Drug Descriptions post",
            "barcode" => 123456789123,
            "middleunitnum" => 4,
            "smallunitnum" => 20,
            "visible" => 0,
            "created_by" => $user->id
        ];

        $this->checkInvalidFieldPostAction($drug, 'name', '');
        $this->checkInvalidFieldPostAction($drug, 'name', 'a');
        $this->checkInvalidFieldPostAction($drug, 'name', $this->faker()->sentence(100));
    }

    public function test_invalid_brandname_on_create_drug() {
        $user = User::factory()->create();
        $drug = [
            "name" => "Drug 1",
            "type" => 1,
            "description" => "Drug Descriptions post",
            "barcode" => 123456789123,
            "middleunitnum" => 4,
            "smallunitnum" => 20,
            "visible" => 0,
            "created_by" => $user->id
        ];

        $this->checkInvalidFieldPostAction($drug, 'brandname', 'a');
        $this->checkInvalidFieldPostAction($drug, 'brandname', $this->faker()->sentence(100));
    }

    public function test_invalid_type_on_create_drug() {
        $user = User::factory()->create();
        $drug = [
            "name" => "Drug 1",
            "brandname" => "Drug 1",
            "description" => "Drug Descriptions post",
            "barcode" => 123456789123,
            "middleunitnum" => 4,
            "smallunitnum" => 20,
            "visible" => 0,
            "created_by" => $user->id
        ];

        $this->checkInvalidFieldPostAction($drug, 'type', true);
        $this->checkInvalidFieldPostAction($drug, 'type', 0);
        $this->checkInvalidFieldPostAction($drug, 'type', 'some text');
    }

    public function test_invalid_description_on_create_drug() {
        $user = User::factory()->create();
        $drug = [
            "name" => "Drug 1",
            "brandname" => "Drug 1",
            "type" => 1,
            "barcode" => 123456789123,
            "middleunitnum" => 4,
            "smallunitnum" => 20,
            "visible" => 0,
            "created_by" => $user->id
        ];

        $this->checkInvalidFieldPostAction($drug, 'description', true);
        $this->checkInvalidFieldPostAction($drug, 'description', "s");
    }

    public function test_invalid_barcode_on_create_drug() {
        $user = User::factory()->create();
        $drug = [
            "name" => "Drug 1",
            "brandname" => "Drug 1",
            "type" => 1,
            "description" => "Drug Descriptions post",
            "middleunitnum" => 4,
            "smallunitnum" => 20,
            "visible" => 0,
            "created_by" => $user->id
        ];

        $this->checkInvalidFieldPostAction($drug, 'barcode', 's');
        $this->checkInvalidFieldPostAction($drug, 'barcode', -1);
    }

    public function test_invalid_middleunitnum_on_create_drug() {
        $user = User::factory()->create();
        $drug = [
            "name" => "Drug 1",
            "brandname" => "Drug 1",
            "type" => 1,
            "description" => "Drug Descriptions post",
            "barcode" => 123456789123,
            "smallunitnum" => 20,
            "visible" => 0,
            "created_by" => $user->id
        ];

        $this->checkInvalidFieldPostAction($drug, 'middleunitnum', '');
        $this->checkInvalidFieldPostAction($drug, 'middleunitnum', 's');
        $this->checkInvalidFieldPostAction($drug, 'middleunitnum', 0);
        $this->checkInvalidFieldPostAction($drug, 'middleunitnum', 101);
    }

    public function test_invalid_smallunitnum_on_create_drug() {
        $user = User::factory()->create();
        $drug = [
            "name" => "Drug 1",
            "brandname" => "Drug 1",
            "type" => 1,
            "description" => "Drug Descriptions post",
            "barcode" => 123456789123,
            "middleunitnum" => 20,
            "visible" => 0,
            "created_by" => $user->id
        ];

        $this->checkInvalidFieldPostAction($drug, 'smallunitnum', 's');
        $this->checkInvalidFieldPostAction($drug, 'smallunitnum', 0);
        $this->checkInvalidFieldPostAction($drug, 'smallunitnum', 101);
    }

    public function test_invalid_visible_on_create_drug() {
        $user = User::factory()->create();
        $drug = [
            "name" => "Drug 1",
            "brandname" => "Drug 1",
            "type" => 1,
            "description" => "Drug Descriptions post",
            "barcode" => 123456789123,
            "middleunitnum" => 20,
            "smallunitnum" => 20,
            "created_by" => $user->id
        ];

        $this->checkInvalidFieldPostAction($drug, 'visible', '');
        $this->checkInvalidFieldPostAction($drug, 'visible', 's');
        $this->checkInvalidFieldPostAction($drug, 'visible', 3);
    }

    public function test_invalid_created_by_on_create_drug() {
        $drug = [
            "name" => "Drug 1",
            "brandname" => "Drug 1",
            "type" => 1,
            "description" => "Drug Descriptions post",
            "barcode" => 123456789123,
            "middleunitnum" => 20,
            "smallunitnum" => 20,
            "visible" => 1
        ];

        $this->checkInvalidFieldPostAction($drug, 'created_by', '');
        $this->checkInvalidFieldPostAction($drug, 'created_by', 's');
        $this->checkInvalidFieldPostAction($drug, 'created_by', 0);
    }

    public function checkInvalidFieldPostAction($target, $property, $tested_value) {
        $target[$property] = $tested_value;
        $response = $this->postJson('api/v1/drugs', $target);
        $response->assertStatus(422);
    }

    public function test_update_exist_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();
        $drug->name = "New Drug";

        $response = $this->putJson('api/v1/drugs/' . $drug->id, $drug->toArray());

        $response->assertStatus(204);
    }

    public function test_invalid_name_on_update_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();

        $this->checkInvalidFieldPutAction($drug, 'name', '');
        $this->checkInvalidFieldPutAction($drug, 'name', 'a');
        $this->checkInvalidFieldPutAction($drug, 'name', $this->faker()->sentence(100));
    }

    public function test_invalid_brandname_on_update_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();

        $this->checkInvalidFieldPutAction($drug, 'brandname', 'a');
        $this->checkInvalidFieldPutAction($drug, 'brandname', $this->faker()->sentence(100));
    }

    public function test_invalid_type_on_update_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();

        $this->checkInvalidFieldPutAction($drug, 'type', 0);
        $this->checkInvalidFieldPutAction($drug, 'type', 'some text');
        $this->checkInvalidFieldPutAction($drug, 'type', '');
    }

    public function test_invalid_description_on_update_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();

        $this->checkInvalidFieldPutAction($drug, 'description', "s");
    }

    public function test_invalid_barcode_on_update_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();

        $this->checkInvalidFieldPutAction($drug, 'barcode', 's');
        $this->checkInvalidFieldPutAction($drug, 'barcode', -1);
    }

    public function test_invalid_middleunitnum_on_update_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();

        $this->checkInvalidFieldPutAction($drug, 'middleunitnum', '');
        $this->checkInvalidFieldPutAction($drug, 'middleunitnum', 's');
        $this->checkInvalidFieldPutAction($drug, 'middleunitnum', 0);
        $this->checkInvalidFieldPutAction($drug, 'middleunitnum', 101);
    }

    public function test_invalid_smallunitnum_on_update_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();

        $this->checkInvalidFieldPutAction($drug, 'smallunitnum', 's');
        $this->checkInvalidFieldPutAction($drug, 'smallunitnum', 0);
        $this->checkInvalidFieldPutAction($drug, 'smallunitnum', 101);
    }

    public function test_invalid_visible_on_update_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();

        $this->checkInvalidFieldPutAction($drug, 'visible', '');
        $this->checkInvalidFieldPutAction($drug, 'visible', 's');
        $this->checkInvalidFieldPutAction($drug, 'visible', 3);
    }

    public function test_invalid_created_by_on_update_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();

        $this->checkInvalidFieldPutAction($drug, 'created_by', '');
        $this->checkInvalidFieldPutAction($drug, 'created_by', 's');
        $this->checkInvalidFieldPutAction($drug, 'created_by', 0);
    }

    public function checkInvalidFieldPutAction($target, $property, $tested_value) {
        $target->{$property} = $tested_value;
        $response = $this->putJson('api/v1/drugs/' . $target->id, $target->toArray());
        $response->assertStatus(422);
    }

    public function test_update_notexist_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();
        $drug->delete();
        $drug->name = "New Drug";

        $response = $this->putJson('api/v1/drugs/' . $drug->id, $drug->toArray());

        $response->assertStatus(404);
    }

    public function test_destroy_exist_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();

        $response = $this->deleteJson('api/v1/drugs/' . $drug->id);

        $response->assertStatus(204);
    }


    public function test_destroy_notexist_drug() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();
        $drug->delete();

        $response = $this->deleteJson('api/v1/drugs/' . $drug->id);

        $response->assertStatus(404);
    }
}
