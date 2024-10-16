<?php

namespace Tests\Feature;

use App\Models\Drug;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DrugsAlternativesTest extends TestCase
{
    public function test_read_all_drugs_alternatives() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()->hasAlternatives(1, ["alternative_id" => $alternativeDrug->id])->create(['created_by' => $user->id]);
        $alternative = $drug->alternatives()->get()[0];

        $this->assertModelExists($alternative);

        $response = $this->getJson("api/v1/drugs/$drug->id/alternatives");
        $response->assertStatus(200);
    }

    public function test_not_exist_drug_id_when_read_all_drugs_alternatives() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()
        ->hasAlternatives(1, ["alternative_id" => $alternativeDrug->id])
        ->create(['created_by' => $user->id]);

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $response = $this->getJson("api/v1/drugs/$drug->id/alternatives");
        $response->assertStatus(404);
    }

    public function test_read_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()
        ->hasAlternatives(1, ["alternative_id" => $alternativeDrug->id])
        ->create(['created_by' => $user->id]);

        $this->assertModelExists($alternativeDrug);
        $this->assertModelExists($drug);

        $response = $this->getJson("api/v1/drugs/$drug->id/alternatives/$alternativeDrug->id");
        $response->assertStatus(200);
    }

    public function test_read_not_exist_drug_when_read_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()
        ->hasAlternatives(1, ["alternative_id" => $alternativeDrug->id])
        ->create(['created_by' => $user->id]);

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $response = $this->getJson("api/v1/drugs/$drug->id/alternatives/$alternativeDrug->id");
        $response->assertStatus(404);
    }

    public function test_read_not_exist_alternative_when_read_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()
        ->hasAlternatives(1, ["alternative_id" => $alternativeDrug->id])
        ->create(['created_by' => $user->id]);

        $alternativeDrug->delete();
        $this->assertSoftDeleted($alternativeDrug);

        $response = $this->getJson("api/v1/drugs/$drug->id/alternatives/$alternativeDrug->id");
        $response->assertStatus(404);
    }

    public function test_read_not_linked_drug_and_alternative_when_read_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()->create(["created_by" => $user->id]);

        $this->assertModelExists($drug);
        $this->assertModelExists($alternativeDrug);

        $response = $this->getJson("api/v1/drugs/$drug->id/alternatives/$alternativeDrug->id");
        $response->assertStatus(404);
    }

    public function test_success_create_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()->create(["created_by" => $user->id]);

        $this->assertModelExists($drug);
        $this->assertModelExists($alternativeDrug);

        $data = [
            "alternative_id" => $alternativeDrug->id,
            "order" => 77
        ];

        $response = $this->postJson("api/v1/drugs/$drug->id/alternatives", $data);
        $response->assertStatus(200);
    }

    public function test_not_exist_drug_id_when_create_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()->create(["created_by" => $user->id]);

        $this->assertModelExists($alternativeDrug);

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $data = [
            "alternative_id" => $alternativeDrug->id,
            "order" => 44
        ];

        $response = $this->postJson("api/v1/drugs/$drug->id/alternatives", $data);
        $response->assertStatus(404);
    }

    public function test_not_exist_alternative_id_when_create_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()->create(["created_by" => $user->id]);

        $this->assertModelExists($alternativeDrug);

        $alternativeDrug->delete();
        $this->assertSoftDeleted($alternativeDrug);

        $data = [
            "alternative_id" => $alternativeDrug->id,
            "order" => 44
        ];

        $response = $this->postJson("api/v1/drugs/$drug->id/alternatives", $data);
        $response->assertStatus(404);
    }

    public function checkInvalidFieldPostAction($target, $property, $testedValue, $drugId) {
        $target[$property] = $testedValue;
        $response = $this->postJson("api/v1/drugs/$drugId/alternatives", $target);
        $response->assertStatus(422);
    }

    public function test_invalid_alternative_id_when_create_drug_alternative() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(["created_by" => $user->id]);

        $this->assertModelExists($drug);

        $data = [
            "order" => 77
        ];

        $this->checkInvalidFieldPostAction($data, "alternative_id", '', $drug->id);
        $this->checkInvalidFieldPostAction($data, "alternative_id", 's', $drug->id);
        $this->checkInvalidFieldPostAction($data, "alternative_id", 0, $drug->id);
    }

    public function test_invalid_order_when_create_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()->create(["created_by" => $user->id]);

        $this->assertModelExists($drug);
        $this->assertModelExists($alternativeDrug);

        $data = [
            "alternative_id" => $alternativeDrug->id
        ];

        $this->checkInvalidFieldPostAction($data, "order", '', $drug->id);
        $this->checkInvalidFieldPostAction($data, "order", 's', $drug->id);
        $this->checkInvalidFieldPostAction($data, "order", -1, $drug->id);
    }

    public function test_update_exist_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()
        ->hasAlternatives(1, ["alternative_id" => $alternativeDrug->id])
        ->create(["created_by" => $user->id]);
        $newAlternativeDrug = Drug::factory()->create(["created_by" => $user->id]);

        $this->assertModelExists($drug);
        $this->assertModelExists($alternativeDrug);

        $data = [
            "alternative_id" => $newAlternativeDrug->id,
            "order" => 88
        ];

        $response = $this->putJson("api/v1/drugs/$drug->id/alternatives/$alternativeDrug->id", $data);
        $response->assertStatus(200);
    }

    public function test_not_exist_drug_id_when_update_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()
        ->hasAlternatives(1, ["alternative_id" => $alternativeDrug->id])
        ->create(["created_by" => $user->id]);
        $newAlternativeDrug = Drug::factory()->create(["created_by" => $user->id]);

        $this->assertModelExists($alternativeDrug);

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $data = [
            "alternative_id" => $newAlternativeDrug->id,
            "order" => 88
        ];

        $response = $this->putJson("api/v1/drugs/$drug->id/alternatives/$alternativeDrug->id", $data);
        $response->assertStatus(404);
    }

    public function test_not_exist_alternative_id_when_update_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()
        ->hasAlternatives(1, ["alternative_id" => $alternativeDrug->id])
        ->create(["created_by" => $user->id]);
        $newAlternativeDrug = Drug::factory()->create(["created_by" => $user->id]);

        $this->assertModelExists($drug);

        $alternativeDrug->delete();
        $this->assertSoftDeleted($alternativeDrug);

        $data = [
            "alternative_id" => $newAlternativeDrug->id,
            "order" => 88
        ];

        $response = $this->putJson("api/v1/drugs/$drug->id/alternatives/$alternativeDrug->id", $data);
        $response->assertStatus(404);
    }

    public function test_update_not_linked_drug_and_alternative_when_update_drug_alternative() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(["created_by" => $user->id]);
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $newAlternativeDrug = Drug::factory()->create(["created_by" => $user->id]);

        $this->assertModelExists($drug);
        $this->assertModelExists($alternativeDrug);
        $this->assertModelExists($newAlternativeDrug);

        $data = [
            "alternative_id" => $newAlternativeDrug->id,
            "order" => 88
        ];

        $response = $this->putJson("api/v1/drugs/$drug->id/alternatives/$alternativeDrug->id", $data);
        $response->assertStatus(404);
    }

    public function checkInvalidFieldPutAction($target, $property, $testedValue, $drug_id, $alternative_id) {
        $target[$property] = $testedValue;

        $response = $this->putJson("api/v1/drugs/$drug_id/alternatives/$alternative_id", $target);
        $response->assertStatus(422);
    }

    public function test_invalid_alternative_id_when_update_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()
        ->hasAlternatives(1, ["alternative_id" => $alternativeDrug->id])
        ->create(["created_by" => $user->id]);

        $this->assertModelExists($drug);
        $this->assertModelExists($alternativeDrug);

        $data = [
            "order" => $alternativeDrug->order
        ];

        $this->checkInvalidFieldPutAction($data, "alternative_id", '', $drug->id, $alternativeDrug->id);
        $this->checkInvalidFieldPutAction($data, "alternative_id", 's', $drug->id, $alternativeDrug->id);
        $this->checkInvalidFieldPutAction($data, "alternative_id", 0, $drug->id, $alternativeDrug->id);
    }

    public function test_invalid_order_when_update_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()
        ->hasAlternatives(1, ["alternative_id" => $alternativeDrug->id])
        ->create(["created_by" => $user->id]);
        $newAlternativeDrug = Drug::factory()->create(["created_by" => $user->id]);

        $this->assertModelExists($drug);
        $this->assertModelExists($alternativeDrug);

        $data = [
            "alternative_id" => $newAlternativeDrug->id
        ];

        $this->checkInvalidFieldPutAction($data, "order", '', $drug->id, $alternativeDrug->id);
        $this->checkInvalidFieldPutAction($data, "order", 's', $drug->id, $alternativeDrug->id);
        $this->checkInvalidFieldPutAction($data, "order", -1, $drug->id, $alternativeDrug->id);
    }

    public function test_destroy_exist_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()
        ->hasAlternatives(1, ["alternative_id" => $alternativeDrug->id])
        ->create(["created_by" => $user->id]);
        $alternative = $drug->alternatives()->get()[0];

        $this->assertModelExists($alternativeDrug);
        $this->assertModelExists($drug);
        $this->assertModelExists($alternative);

        $response = $this->deleteJson("api/v1/drugs/$drug->id/alternatives/$alternativeDrug->id");
        $response->assertStatus(200);
    }

    public function test_destroy_not_exist_drug_id_when_destroy_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()
        ->hasAlternatives(1, ["alternative_id" => $alternativeDrug->id])
        ->create(["created_by" => $user->id]);
        $alternative = $drug->alternatives()->get()[0];

        $this->assertModelExists($alternativeDrug);
        $this->assertModelExists($alternative);

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $response = $this->deleteJson("api/v1/drugs/$drug->id/alternatives/$alternativeDrug->id");
        $response->assertStatus(404);
    }

    public function test_destroy_not_exist_alternative_id_when_destroy_drug_alternative() {
        $user = User::factory()->create();
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);
        $drug = Drug::factory()
        ->hasAlternatives(1, ["alternative_id" => $alternativeDrug->id])
        ->create(["created_by" => $user->id]);
        $alternative = $drug->alternatives()->get()[0];

        $this->assertModelExists($drug);
        $this->assertModelExists($alternative);

        $alternativeDrug->delete();
        $this->assertSoftDeleted($alternativeDrug);

        $response = $this->deleteJson("api/v1/drugs/$drug->id/alternatives/$alternativeDrug->id");
        $response->assertStatus(404);
    }

    public function test_destroy_not_linked_drug_and_alternative_when_destroy_drug_alternative() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(["created_by" => $user->id]);
        $alternativeDrug = Drug::factory()->create(["created_by" => $user->id]);

        $this->assertModelExists($drug);
        $this->assertModelExists($alternativeDrug);

        $response = $this->deleteJson("api/v1/drugs/$drug->id/alternatives/$alternativeDrug->id");
        $response->assertStatus(404);
    }
}
