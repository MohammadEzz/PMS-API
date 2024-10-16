<?php

namespace Tests\Feature;

use App\Models\ActiveIngredient;
use App\Models\Drug;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DrugActiveIngredientTest extends TestCase
{
    public function test_read_all_drugs_active_ingredients() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create([
            "created_by" => $user->id,
        ]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $drug->activeIngredients()->attach($activeIngredient->id, [
            'concentration' => 2000,
            'format' => 12,
            'order' => 1
        ]);

        $response = $this->getJson("api/v1/drugs/$drug->id/activeingredients");
        $response->assertStatus(200);
    }

    public function test_read_notexist_drug_when_read_all_drugs_active_ingredients() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create([
            "created_by" => $user->id,
        ]);

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $response = $this->getJson("api/v1/drugs/$drug->id/activeingredients");
        $response->assertStatus(404);
    }

    public function test_read_exist_drugs_active_ingredients() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create([
            "created_by" => $user->id,
        ]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $drug->activeIngredients()->attach($activeIngredient->id, [
            'concentration' => 2000,
            'format' => 12,
            'order' => 1
        ]);

        $response = $this->getJson("api/v1/drugs/$drug->id/activeingredients/$activeIngredient->id");
        $response->assertStatus(200);
    }

    public function test_read_not_exist_drug_when_read_drug_active_ingredient() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create([
            "created_by" => $user->id,
        ]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $drug->activeIngredients()->attach($activeIngredient->id, [
            'concentration' => 2000,
            'format' => 12,
            'order' => 1
        ]);

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $response = $this->getJson("api/v1/drugs/$drug->id/activeingredients/$activeIngredient->id");
        $response->assertStatus(404);
    }

    public function test_read_not_exist_active_ingredient_when_read_drug_active_ingredient() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create([
            "created_by" => $user->id,
        ]);
        $activeIngredient = ActiveIngredient::factory()->create();

        $activeIngredient->delete();
        $this->assertModelMissing($activeIngredient);

        $response = $this->getJson("api/v1/drugs/$drug->id/activeingredients/$activeIngredient->id");
        $response->assertStatus(404);
    }

    public function test_success_create_drug_active_ingredient() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $data = [
            "activeingredient_id" => $activeIngredient->id,
            "concentration" => 200,
            "format" => 123,
            "order" => 2
        ];

        $response = $this->postJson("api/v1/drugs/$drug->id/activeingredients", $data);
        $response->assertStatus(200);
    }

    public function test_notexist_drug_id_when_create_drug_active_ingredient() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $data = [
            "activeingredient_id" => $activeIngredient->id,
            "concentration" => 200,
            "format" => 444,
            "order" => 2
        ];

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $response = $this->postJson("api/v1/drugs/$drug->id/activeingredients", $data);
        $response->assertStatus(404);
    }

    public function test_notexist_active_ingredient_id_when_create_drug_active_ingredient() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $data = [
            "activeingredient_id" => $activeIngredient->id,
            "concentration" => 200,
            "format" => 444,
            "order" => 2
        ];

        $activeIngredient->delete();
        $this->assertModelMissing($activeIngredient);

        $response = $this->postJson("api/v1/drugs/$drug->id/activeingredients", $data);
        $response->assertStatus(404);
    }

    public function checkInvalidFieldPostAction($target, $property, $testedValue, $drug_id) {
        $target[$property] = $testedValue;
        $response = $this->postJson("api/v1/drugs/$drug_id/activeingredients", $target);
        $response->assertStatus(422);
    }

    public function test_invalid_active_ingredient_id_when_create_drugs_active_ingredients() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $data = [
            'concentration' => 1000,
            "format" => 12,
            "order" => 5
        ];

        $this->checkInvalidFieldPostAction($data, 'activeingredient_id', '', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'activeingredient_id', 's', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'activeingredient_id', 0, $drug->id);
    }

    public function test_invalid_concentration_when_create_drugs_active_ingredients() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $data = [
            'activeingredient_id' => $activeIngredient->id,
            "format" => 444,
            "order" => 2
        ];

        $this->checkInvalidFieldPostAction($data, 'concentration', '', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'concentration', 's', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'concentration', 0, $drug->id);
    }


    public function test_invalid_format_when_create_drugs_active_ingredients() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $data = [
            'activeingredient_id' => $activeIngredient->id,
            "concentration" => 333,
            "order" => 2
        ];

        $this->checkInvalidFieldPostAction($data, 'format', '', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'format', 's', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'format', 0, $drug->id);
    }

    public function test_invalid_order_when_create_drugs_active_ingredients() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $data = [
            'activeingredient_id' => $activeIngredient->id,
            "concentration" => 333,
            "format" => 2
        ];

        $this->checkInvalidFieldPostAction($data, 'order', '', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'order', 's', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'order', -1, $drug->id);
    }

    public function test_success_update_drug_active_ingredient() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $drug->activeIngredients()
             ->attach($activeIngredient->id, [
                 "concentration" => 200,
                 "format" => 123,
                 "order" => 9 ]);

        $data = [
            "activeingredient_id" => $activeIngredient->id,
            "concentration" => 500,
            "format" => 22,
            "order" => 2,
        ];

        $response = $this->putJson("api/v1/drugs/$drug->id/activeingredients/$activeIngredient->id", $data);
        $response->assertStatus(200);
    }

    public function test_notexist_drug_id_when_update_drug_active_ingredient() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $data = [
            "activeingredient_id" => $activeIngredient->id,
            "concentration" => 500,
            "format" => 22,
            "order" => 2,
        ];

        $response = $this->putJson("api/v1/drugs/$drug->id/activeingredients/$activeIngredient->id", $data);
        $response->assertStatus(404);
    }

    public function test_notexist_active_ingredient_id_when_update_drug_active_ingredient() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();

        $activeIngredient->delete();
        $this->assertModelMissing($activeIngredient);

        $data = [
            "activeingredient_id" => $activeIngredient->id,
            "concentration" => 500,
            "format" => 22,
            "order" => 2,
        ];

        $response = $this->putJson("api/v1/drugs/$drug->id/activeingredients/$activeIngredient->id", $data);
        $response->assertStatus(404);
    }

    public function test_notexitst_pivot_record_when_update_drugs_active_ingredient() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();

        $data = [
            "activeingredient_id" => $activeIngredient->id,
            "concentration" => 500,
            "format" => 22,
            "order" => 2,
        ];

        $response = $this->putJson("api/v1/drugs/$drug->id/activeingredients/$activeIngredient->id", $data);
        $response->assertStatus(404);
    }

    public function checkInvalidFieldPutAction($target, $property, $testedValue, $drug_id, $activeIngredient_id) {
        $target[$property] = $testedValue;

        $response = $this->putJson("api/v1/drugs/$drug_id/activeingredients/$activeIngredient_id", $target);
        $response->assertStatus(422);
    }

    public function test_invalid_concentration_when_update_drugs_active_ingredients() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $drug->activeIngredients()
             ->attach($activeIngredient->id, [
                 "concentration" => 200,
                 "format" => 123,
                 "order" => 9 ]);

        $data = [
            "format" => 666,
            "order" => 4
        ];

        $this->checkInvalidFieldPutAction($data, 'concentration', '', $drug->id, $activeIngredient->id);
        $this->checkInvalidFieldPutAction($data, 'concentration', 's', $drug->id, $activeIngredient->id);
        $this->checkInvalidFieldPutAction($data, 'concentration', 0, $drug->id, $activeIngredient->id);
    }

    public function test_invalid_format_when_update_drugs_active_ingredients() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $drug->activeIngredients()
             ->attach($activeIngredient->id, [
                 "concentration" => 200,
                 "format" => 123,
                 "order" => 9 ]);

        $data = [
            "concentration" => 60,
            "order" => 50
        ];

        $this->checkInvalidFieldPutAction($data, 'format', '', $drug->id, $activeIngredient->id);
        $this->checkInvalidFieldPutAction($data, 'format', 's', $drug->id, $activeIngredient->id);
        $this->checkInvalidFieldPutAction($data, 'format', 0, $drug->id, $activeIngredient->id);
    }

    public function test_invalid_order_when_update_drugs_active_ingredients() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $drug->activeIngredients()
             ->attach($activeIngredient->id, [
                 "concentration" => 200,
                 "format" => 123,
                 "order" => 9 ]);

        $data = [
            "concentration" => 432,
            "format" => 40
        ];

        $this->checkInvalidFieldPutAction($data, 'order', '', $drug->id, $activeIngredient->id);
        $this->checkInvalidFieldPutAction($data, 'order', 's', $drug->id, $activeIngredient->id);
        $this->checkInvalidFieldPutAction($data, 'order', -1, $drug->id, $activeIngredient->id);
    }

    public function test_destroy_exist_drugs_active_ingredient() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $drug->activeIngredients()
             ->attach($activeIngredient->id, [
                 "concentration" => 200,
                 "format" => 123,
                 "order" => 9 ]);

        $response = $this->deleteJson("api/v1/drugs/$drug->id/activeingredients/$activeIngredient->id");
        $response->assertStatus(201);
    }

    public function test_notexitst_drug_when_destroy_drugs_active_ingredient() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();
        $drug->activeIngredients()
             ->attach($activeIngredient->id, [
                 "concentration" => 200,
                 "format" => 123,
                 "order" => 9 ]);

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $response = $this->deleteJson("api/v1/drugs/$drug->id/activeingredients/$activeIngredient->id");
        $response->assertStatus(404);
    }

    public function test_notexitst_active_ingredient_when_destroy_drugs_active_ingredient() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();

        $activeIngredient->delete();
        $this->assertModelMissing($activeIngredient);

        $response = $this->deleteJson("api/v1/drugs/$drug->id/activeingredients/$activeIngredient->id");
        $response->assertStatus(404);
    }

    public function test_notexitst_pivot_record_when_destroy_drugs_active_ingredient() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $activeIngredient = ActiveIngredient::factory()->create();

        $response = $this->deleteJson("api/v1/drugs/$drug->id/activeingredients/$activeIngredient->id");
        $response->assertStatus(404);
    }
}
