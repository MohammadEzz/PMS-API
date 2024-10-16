<?php

namespace Tests\Feature;

use App\Models\ActiveIngredient;
use App\Models\Disease;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DiseaseActiveIngredientTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_read_all_diseases_active_ingredients()
    {
        $disease = Disease::factory()->hasAttached(ActiveIngredient::factory()->count(2), ["order" =>3])->create();
        $activeIngredients = $disease->activeIngredients()->get();

        $this->assertModelExists($disease);
        $this->assertModelExists($activeIngredients->first());

        $response = $this->getJson("api/v1/diseases/$disease->id/activeingredients");
        $response->assertStatus(200);
    }

    public function test_read_notexist_drug_when_read_all_diseases_active_ingredients() {
        $disease = Disease::factory()->create();

        $disease->delete();
        $this->assertModelMissing($disease);

        $response = $this->getJson("api/v1/diseases/$disease->id/activeingredients");
        $response->assertStatus(404);
    }

    public function test_read_disease_active_ingredient() {
        $disease = Disease::factory()->hasAttached(ActiveIngredient::factory()->count(1), ["order" =>3])->create();
        $activeIngredient = $disease->activeIngredients()->first();

        $this->assertModelExists($disease);
        $this->assertModelExists($activeIngredient->first());

        $response = $this->getJson("api/v1/diseases/$disease->id/activeingredients/$activeIngredient->id");
        $response->assertStatus(200);
    }

    public function test_read_not_exist_disease_when_read_disease_active_ingredient() {
        $disease = Disease::factory()->create();
        $activeIngredient = ActiveIngredient::factory()->create();

        $disease->delete();

        $this->assertModelMissing($disease);
        $this->assertModelExists($activeIngredient);

        $response = $this->getJson("api/v1/diseases/$disease->id/activeingredients/$activeIngredient->id");
        $response->assertStatus(404);
    }

    public function test_read_not_exist_active_ingredient_when_read_disease_active_ingredient() {
        $disease = Disease::factory()->create();
        $activeIngredient = ActiveIngredient::factory()->create();

        $activeIngredient->delete();

        $this->assertModelMissing($activeIngredient);
        $this->assertModelExists($disease);

        $response = $this->getJson("api/v1/diseases/$disease->id/activeingredients/$activeIngredient->id");
        $response->assertStatus(404);
    }

    public function test_success_create_disease_active_ingredient() {
        $disease = Disease::factory()->create();
        $activeIngredient = ActiveIngredient::factory()->create();

        $this->assertModelExists($disease);
        $this->assertModelExists($activeIngredient);

        $data = [
            "activeingredient_id" => $activeIngredient->id,
            "order" => 1
        ];

        $response = $this->postJson("api/v1/diseases/$disease->id/activeingredients", $data);
        $response->assertStatus(200);
    }

    public function test_notexist_disease_id_when_create_drug_active_ingredient() {
        $disease = Disease::factory()->create();
        $activeIngredient = ActiveIngredient::factory()->create();

        $disease->delete();

        $this->assertModelMissing($disease);
        $this->assertModelExists($activeIngredient);

        $data = [
            "activeingredient_id" => $activeIngredient->id,
            "order" => 1
        ];

        $response = $this->postJson("api/v1/diseases/$disease->id/activeingredients", $data);
        $response->assertStatus(404);
    }

    public function test_notexist_active_ingredient_id_when_create_drug_active_ingredient() {
        $disease = Disease::factory()->create();
        $activeIngredient = ActiveIngredient::factory()->create();

        $activeIngredient->delete();

        $this->assertModelMissing($activeIngredient);
        $this->assertModelExists($disease);

        $data = [
            "activeingredient_id" => $activeIngredient->id,
            "order" => 1
        ];

        $response = $this->postJson("api/v1/diseases/$disease->id/activeingredients", $data);
        $response->assertStatus(404);
    }

    public function checkInvalidFieldPostAction($target, $property, $testedValue, $disease_id) {
        $target[$property] = $testedValue;
        $response = $this->postJson("api/v1/diseases/$disease_id/activeingredients", $target);
        $response->assertStatus(422);
    }


    public function test_invalid_active_ingredient_id_when_create_drugs_active_ingredients() {
        $disease = Disease::factory()->create();
        $activeIngredient = ActiveIngredient::factory()->create();

        $this->assertModelExists($disease);
        $this->assertModelExists($activeIngredient);

        $data = [
            "order" => 5,
        ];

        $this->checkInvalidFieldPostAction($data, 'activeingredient_id', '', $disease->id);
        $this->checkInvalidFieldPostAction($data, 'activeingredient_id', 's', $disease->id);
        $this->checkInvalidFieldPostAction($data, 'activeingredient_id', 0, $disease->id);
    }

    public function test_invalid_order_when_create_drugs_active_ingredients() {
        $disease = Disease::factory()->create();
        $activeIngredient = ActiveIngredient::factory()->create();

        $this->assertModelExists($disease);
        $this->assertModelExists($activeIngredient);

        $data = [
            "activeingredient_id" => $activeIngredient->id,
        ];

        $this->checkInvalidFieldPostAction($data, 'order', '', $disease->id);
        $this->checkInvalidFieldPostAction($data, 'order', 's', $disease->id);
        $this->checkInvalidFieldPostAction($data, 'order', -1, $disease->id);
    }

    public function test_success_update_disease_active_ingredient() {
        $disease = Disease::factory()
        ->hasAttached(ActiveIngredient::factory()->count(1), ["order" => 3])
        ->create();

        $activeIngredient = $disease->activeIngredients()->first();
        $newActiveIngredient = ActiveIngredient::factory()->create();

        $this->assertModelExists($disease);
        $this->assertModelExists($activeIngredient);
        $this->assertModelExists($newActiveIngredient);

        $data = [
            "activeingredient_id" => $newActiveIngredient->id,
            "order" => 5
        ];

        $response = $this->putJson("api/v1/diseases/$disease->id/activeingredients/$activeIngredient->id", $data);
        $response->assertStatus(200);
    }

    public function test_notexist_disease_id_when_update_disease_active_ingredient() {
        $disease = Disease::factory()->create();
        $activeIngredient = ActiveIngredient::factory()->create();

        $disease->delete();

        $this->assertModelMissing($disease);
        $this->assertModelExists($activeIngredient);

        $data = [
            "order" => 5
        ];

        $response = $this->putJson("api/v1/diseases/$disease->id/activeingredients/$activeIngredient->id", $data);
        $response->assertStatus(404);
    }


    public function test_notexist_active_ingredient_id_when_update_disease_active_ingredient() {
        $disease = Disease::factory()->create();
        $activeIngredient = ActiveIngredient::factory()->create();

        $activeIngredient->delete();

        $this->assertModelMissing($activeIngredient);
        $this->assertModelExists($disease);

        $data = [
            "order" => 5
        ];

        $response = $this->putJson("api/v1/diseases/$disease->id/activeingredients/$activeIngredient->id", $data);
        $response->assertStatus(404);
    }

    public function test_notexitst_pivot_record_when_update_disease_active_ingredient() {
        $disease = Disease::factory()->create();
        $activeIngredient = ActiveIngredient::factory()->create();

        $this->assertModelExists($disease);
        $this->assertModelExists($activeIngredient);

        $data = [
            "order" => 5
        ];

        $response = $this->putJson("api/v1/diseases/$disease->id/activeingredients/$activeIngredient->id", $data);
        $response->assertStatus(404);
    }

    public function checkInvalidFieldPutAction($target, $property, $testedValue, $disease_id, $activeIngredient_id) {
        $target[$property] = $testedValue;

        $response = $this->putJson("api/v1/diseases/$disease_id/activeingredients/$activeIngredient_id", $target);
        $response->assertStatus(422);
    }

    public function test_invalid_order_when_update_drugs_active_ingredients() {
        $disease = Disease::factory()
        ->hasAttached(ActiveIngredient::factory()->count(1), ["order" => 4])
        ->create();

        $activeIngredient = $disease->activeIngredients()->first();

        $this->assertModelExists($disease);
        $this->assertModelExists($activeIngredient);

        $data = [];

        $this->checkInvalidFieldPutAction($data, 'order', '', $disease->id, $activeIngredient->id);
        $this->checkInvalidFieldPutAction($data, 'order', 's', $disease->id, $activeIngredient->id);
        $this->checkInvalidFieldPutAction($data, 'order', -1, $disease->id, $activeIngredient->id);
    }

    public function test_destroy_exist_disease_active_ingredient() {
        $disease = Disease::factory()
        ->hasAttached(ActiveIngredient::factory()->count(1), ["order" => 4])
        ->create();

        $activeIngredient = $disease->activeIngredients()->first();

        $this->assertModelExists($disease);
        $this->assertModelExists($activeIngredient);

        $response = $this->deleteJson("api/v1/diseases/$disease->id/activeingredients/$activeIngredient->id");
        $response->assertStatus(201);
    }

    public function test_notexitst_disease_when_destroy_diseases_active_ingredient() {
        $disease = Disease::factory()->create();
        $activeIngredient = ActiveIngredient::factory()->create();

        $disease->delete();

        $this->assertModelMissing($disease);
        $this->assertModelExists($activeIngredient);

        $response = $this->deleteJson("api/v1/diseases/$disease->id/activeingredients/$activeIngredient->id");
        $response->assertStatus(404);
    }

    public function test_notexitst_active_ingredient_when_destroy_diseases_active_ingredient() {
        $disease = Disease::factory()->create();
        $activeIngredient = ActiveIngredient::factory()->create();

        $activeIngredient->delete();

        $this->assertModelMissing($activeIngredient);
        $this->assertModelExists($disease);

        $response = $this->deleteJson("api/v1/diseases/$disease->id/activeingredients/$activeIngredient->id");
        $response->assertStatus(404);
    }

    public function test_notexitst_pivot_record_when_destroy_diseases_active_ingredient() {
        $disease = Disease::factory()->create();
        $activeIngredient = ActiveIngredient::factory()->create();

        $this->assertModelExists($disease);
        $this->assertModelExists($activeIngredient);

        $response = $this->deleteJson("api/v1/diseases/$disease->id/activeingredients/$activeIngredient->id");
        $response->assertStatus(404);
    }
}
