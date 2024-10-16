<?php

namespace Tests\Feature;

use App\Models\Contraindication;
use App\Models\Drug;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DrugContraindicationTest extends TestCase
{
    use WithFaker;
/*  */
    public function test_read_all_drug_contraindications() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(["created_by" => $user->id]);
        $contraindication = Contraindication::factory()->create(["drug_id" => $drug->id]);
        $this->assertModelExists($contraindication);

        $response = $this->getJson("api/v1/drugs/$drug->id/contraindications");
        $response->assertStatus(200);
    }

    public function test_read_not_exist_drug_when_read_all_drug_contraindications() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(["created_by" => $user->id]);
        $contraindication = Contraindication::factory()->create(["drug_id" => $drug->id]);
        $this->assertModelExists($contraindication);

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $response = $this->getJson("api/v1/drugs/$drug->id/contraindications");
        $response->assertStatus(404);
    }

    public function test_read_exist_drug_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(["created_by" => $user->id]);
        $contraindication = Contraindication::factory()->create(["drug_id" => $drug->id]);
        $this->assertModelExists($contraindication);

        $response = $this->getJson("api/v1/drugs/$drug->id/contraindications/$contraindication->id");
        $response->assertStatus(200);
    }

    public function test_read_notexist_drug_when_read_drug_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(["created_by" => $user->id]);
        $contraindication = Contraindication::factory()->create(["drug_id" => $drug->id]);
        $this->assertModelExists($contraindication);

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $response = $this->getJson("api/v1/drugs/$drug->id/contraindications/$contraindication->id");
        $response->assertStatus(404);
    }

    public function test_read_notexist_contraindication_when_read_drug_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(["created_by" => $user->id]);
        $contraindication = Contraindication::factory()->create(["drug_id" => $drug->id]);
        $this->assertModelExists($contraindication);

        $contraindication->delete();
        $this->assertModelMissing($contraindication);

        $response = $this->getJson("api/v1/drugs/$drug->id/contraindications/$contraindication->id");
        $response->assertStatus(404);
    }

    public function test_success_create_drug_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(["created_by" => $user->id]);
        $contraindication = [
            "category" => 1,
            "description" => $this->faker()->sentence(4),
            "level" => 123,
            "order" => 1
        ];

        $response = $this->postJson("api/v1/drugs/$drug->id/contraindications", $contraindication);
        $response->assertStatus(200);
    }

    public function test_noexist_drug_when_create_drug_contraindication (){
        $user = User::factory()->create();
        $drug = Drug::factory()->create(["created_by" => $user->id]);
        $contraindication = [
            "category" => 1,
            "description" => $this->faker()->sentence(4),
            "level" => 123,
            "order" => 1
        ];

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $response = $this->postJson("api/v1/drugs/$drug->id/contraindications", $contraindication);
        $response->assertStatus(404);
    }

    public function checkInvalidFieldPostAction($target, $property, $testedValue, $drug_id) {
        $target[$property] = $testedValue;
        $response = $this->postJson("api/v1/drugs/$drug_id/contraindications", $target);
        $response->assertStatus(422);
    }

    public function test_invalid_category_id_when_create_drugs_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $data = [
            "description" => $this->faker()->sentence(4),
            "level" => 123,
            "order" => 1,
        ];

        $this->checkInvalidFieldPostAction($data, 'category', '', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'category', 's', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'category', 0, $drug->id);
    }

    public function test_invalid_description_when_create_drugs_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $data = [
            "category" => 22,
            "level" => 123,
            "order" => 1,
        ];

        $this->checkInvalidFieldPostAction($data, 'description', '', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'description', 's', $drug->id);
    }

    public function test_invalid_level_when_create_drugs_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $data = [
            "description" => $this->faker()->sentence(4),
            "category" => 22,
            "order" => 1,
        ];

        $this->checkInvalidFieldPostAction($data, 'level', '', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'level', 's', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'level', 0, $drug->id);
    }

    public function test_invalid_order_when_create_drugs_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(['created_by' => $user->id]);
        $data = [
            "description" => $this->faker()->sentence(4),
            "category" => 22,
            "level" => 123,
        ];

        $this->checkInvalidFieldPostAction($data, 'order', '', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'order', 's', $drug->id);
        $this->checkInvalidFieldPostAction($data, 'order', -1, $drug->id);
    }

    public function test_success_update_drug_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()
        ->hasContraindications(1,[
            'category' => 333,
            'level' => 1000,
            'order' => 32,
        ])
        ->create(['created_by' => $user->id]);

        $contraindication = $drug->contraindications()->get()[0];
        $this->assertModelExists($contraindication);

        $data=[
            'category' => $contraindication->category,
            "description" => $contraindication->description,
            'level' => 23,
            'order' => $contraindication->order,
        ];

        $response = $this->putJson("api/v1/drugs/$drug->id/contraindications/$contraindication->id", $data);
        $response->assertStatus(200);
    }

    public function test_notexist_drug_id_when_update_drug_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()
        ->hasContraindications(1)
        ->create(['created_by' => $user->id]);
        $contraindication = $drug->contraindications()->get()[0];

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $data=[
            'category' => $contraindication->category,
            "description" => $contraindication->description,
            'level' => $contraindication->level,
            'order' => $contraindication->order,
        ];

        $response = $this->putJson("api/v1/drugs/$drug->id/contraindications/$contraindication->id", $data);
        $response->assertStatus(404);
    }

    public function test_notexist_contraindication_id_when_update_drug_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()
        ->hasContraindications(1)
        ->create(['created_by' => $user->id]);
        $contraindication = $drug->contraindications()->get()[0];

        $contraindication->delete();
        $this->assertModelMissing($contraindication);

        $data=[
            'category' => $contraindication->category,
            "description" => $contraindication->description,
            'level' => $contraindication->level,
            'order' => $contraindication->order,
        ];

        $response = $this->putJson("api/v1/drugs/$drug->id/contraindications/$contraindication->id", $data);
        $response->assertStatus(404);
    }

    public function checkInvalidFieldPutAction($target, $property, $testedValue, $drug_id, $contraindication_id) {
        $target[$property] = $testedValue;

        $response = $this->putJson("api/v1/drugs/$drug_id/contraindications/$contraindication_id", $target);
        $response->assertStatus(422);
    }

    public function test_invalid_category_when_update_drugs_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()
        ->hasContraindications(1,[
            'category' => 333,
            'level' => 1000,
            'order' => 32,
        ])
        ->create(['created_by' => $user->id]);

        $contraindication = $drug->contraindications()->get()[0];
        $this->assertModelExists($contraindication);

        $data=[
            "description" => $contraindication->description,
            'level' => $contraindication->level,
            'order' => $contraindication->order,
        ];

        $this->checkInvalidFieldPutAction($data, 'category', '', $drug->id, $contraindication->id);
        $this->checkInvalidFieldPutAction($data, 'category', 's', $drug->id, $contraindication->id);
        $this->checkInvalidFieldPutAction($data, 'category', 0, $drug->id, $contraindication->id);
    }

    public function test_invalid_description_when_update_drugs_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()
        ->hasContraindications(1,[
            'category' => 333,
            'level' => 1000,
            'order' => 32,
        ])
        ->create(['created_by' => $user->id]);

        $contraindication = $drug->contraindications()->get()[0];
        $this->assertModelExists($contraindication);

        $data=[
            'category' => $contraindication->category,
            'level' => $contraindication->level,
            'order' => $contraindication->order,
        ];

        $this->checkInvalidFieldPutAction($data, 'description', '', $drug->id, $contraindication->id);
        $this->checkInvalidFieldPutAction($data, 'description', 's', $drug->id, $contraindication->id);
    }

    public function test_invalid_level_when_update_drugs_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()
        ->hasContraindications(1,[
            'category' => 333,
            'level' => 1000,
            'order' => 32,
        ])
        ->create(['created_by' => $user->id]);

        $contraindication = $drug->contraindications()->get()[0];
        $this->assertModelExists($contraindication);

        $data=[
            'category' => $contraindication->category,
            "description" => $contraindication->description,
            'order' => $contraindication->order,
        ];

        $this->checkInvalidFieldPutAction($data, 'level', '', $drug->id, $contraindication->id);
        $this->checkInvalidFieldPutAction($data, 'level', 's', $drug->id, $contraindication->id);
        $this->checkInvalidFieldPutAction($data, 'level', 0, $drug->id, $contraindication->id);
    }

    public function test_invalid_order_when_update_drugs_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()
        ->hasContraindications(1,[
            'category' => 333,
            'level' => 1000,
            'order' => 32,
        ])
        ->create(['created_by' => $user->id]);

        $contraindication = $drug->contraindications()->get()[0];
        $this->assertModelExists($contraindication);

        $data=[
            'category' => $contraindication->category,
            "description" => $contraindication->description,
            'level' => $contraindication->level,
        ];

        $this->checkInvalidFieldPutAction($data, 'order', '', $drug->id, $contraindication->id);
        $this->checkInvalidFieldPutAction($data, 'order', 's', $drug->id, $contraindication->id);
        $this->checkInvalidFieldPutAction($data, 'order', -1, $drug->id, $contraindication->id);
    }

    public function test_destroy_exist_drug_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->hasContraindications(1)->create(['created_by' => $user->id]);
        $contraindication = $drug->contraindications()->get()[0];

        $response = $this->deleteJson("api/v1/drugs/$drug->id/contraindications/$contraindication->id");
        $response->assertStatus(201);
    }

    public function test_notexist_drug_id_when_destroy_drug_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->hasContraindications(1)->create(['created_by' => $user->id]);
        $contraindication = $drug->contraindications()->get()[0];

        $drug->delete();
        $this->assertSoftDeleted($drug);

        $response = $this->deleteJson("api/v1/drugs/$drug->id/contraindications/$contraindication->id");
        $response->assertStatus(404);
    }

    public function test_notexist_contraindication_id_when_destroy_drug_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->hasContraindications(1)->create(['created_by' => $user->id]);
        $contraindication = $drug->contraindications()->get()[0];

        $contraindication->delete();
        $this->assertModelMissing($contraindication);

        $response = $this->deleteJson("api/v1/drugs/$drug->id/contraindications/$contraindication->id");
        $response->assertStatus(404);
    }
}
