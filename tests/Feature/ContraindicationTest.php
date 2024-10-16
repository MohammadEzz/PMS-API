<?php

namespace Tests\Feature;

use App\Models\Contraindication;
use App\Models\Drug;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContraindicationTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function test_read_all_contraindications() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();
        Contraindication::factory()->state(new Sequence(
            function($sequance) use ($drug){
                return ['drug_id' => $drug->id, "order" => $sequance->index];
            }))->count(2)->create();

        $response = $this->getJson('api/v1/contraindications');

        $response->assertStatus(200);
    }

    public function test_read_exist_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();
        $contraindication = Contraindication::factory()->state(new Sequence(
            function($sequance) use ($drug){
                return ['drug_id' => $drug->id, "order" => $sequance->index];
            }))->create();

        $response = $this->getJson('api/v1/contraindications/' . $contraindication->id);

        $response->assertStatus(200);
    }

    public function test_read_notexist_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();
        $contraindication = Contraindication::factory()->state(new Sequence(
            function($sequance) use ($drug){
                return ['drug_id' => $drug->id, "order" => $sequance->index];
            }))->create();
        $contraindication->delete();

        $response = $this->getJson('api/v1/contraindications/' . $contraindication->id);

        $response->assertStatus(404);
    }

    public function test_success_create_contraindication() {
        $this->setUpFaker();
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();
        $contraindication = [
            "category" => 1,
            "description" =>$this->faker()->sentence(20),
            "level" => 1,
            "order" => 1,
            "drug_id" => $drug->id
        ];

        $response = $this->postJson("api/v1/contraindications", $contraindication);
        $response->assertStatus(201);
    }

    public function test_invalid_category_on_create_contraindication() {
        $this->setUpFaker();
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();
        $contraindication = [
            "description" =>$this->faker()->sentence(20),
            "level" => 1,
            "order" => 1,
            "drug_id" => $drug->id
        ];

        $this->checkInvalidFieldPostAction($contraindication, 'category', '');
        $this->checkInvalidFieldPostAction($contraindication, 'category', 's');
        $this->checkInvalidFieldPostAction($contraindication, 'category', 0);
    }

    public function test_invalid_description_on_create_contraindication() {
        $this->setUpFaker();
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();
        $contraindication = [
            "category" =>1,
            "level" => 1,
            "order" => 1,
            "drug_id" => $drug->id
        ];

        $this->checkInvalidFieldPostAction($contraindication, 'description', '');
        $this->checkInvalidFieldPostAction($contraindication, 'description', 's');
    }

    public function test_invalid_level_on_create_contraindication() {
        $this->setUpFaker();
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();
        $contraindication = [
            "category" =>1,
            "description" => "Some Text",
            "order" => 1,
            "drug_id" => $drug->id
        ];

        $this->checkInvalidFieldPostAction($contraindication, 'level', '');
        $this->checkInvalidFieldPostAction($contraindication, 'level', 's');
        $this->checkInvalidFieldPostAction($contraindication, 'level', 0);
    }

    public function test_invalid_order_on_create_contraindication() {
        $this->setUpFaker();
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();
        $contraindication = [
            "category" =>1,
            "description" => "Some Text",
            "level" => 1,
            "drug_id" => $drug->id
        ];

        $this->checkInvalidFieldPostAction($contraindication, 'order', '');
        $this->checkInvalidFieldPostAction($contraindication, 'order', 's');
        $this->checkInvalidFieldPostAction($contraindication, 'order', -1);
    }

    public function test_invalid_drug_id_on_create_contraindication() {
        $this->setUpFaker();
        $user = User::factory()->create();
        $drug = Drug::factory()->state(['created_by' => $user->id])->create();
        $contraindication = [
            "category" =>1,
            "description" => "Some Text",
            "level" => 1,
            "order" => 0
        ];

        $this->checkInvalidFieldPostAction($contraindication, 'drug_id', '');
        $this->checkInvalidFieldPostAction($contraindication, 'drug_id', 's');
        $this->checkInvalidFieldPostAction($contraindication, 'drug_id', 0);
    }

    public function checkInvalidFieldPostAction($target, $property, $tested_value) {
        $target[$property] = $tested_value;
        $response = $this->postJson('api/v1/contraindications', $target);
        $response->assertStatus(422);
    }

    public function test_update_exist_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(["created_by" => $user->id])->create();
        $contraindication = Contraindication::factory()->state(new Sequence(
            function($sequance) use ($drug) {
                return ["drug_id" => $drug->id, "order" => $sequance->index];
            }
        ))->create();
        $contraindication->description = "New Updated Value On Contraindication";

        $response = $this->putJson("api/v1/contraindications/" . $contraindication->id, $contraindication->toArray());
        $response->assertStatus(201);
    }


    public function test_invalid_category_on_update_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->create(["created_by" => $user->id]);
        $contraindication = Contraindication::factory()->state(new Sequence(
            function($sequance) use ($drug) {
                return ["drug_id" => $drug->id, "order" => $sequance->index];
            }
        ))->create();

        $this->checkInvalidFieldPutAction($contraindication, 'category', '');
        $this->checkInvalidFieldPutAction($contraindication, 'category', 's');
        $this->checkInvalidFieldPutAction($contraindication, 'category', 0);
    }

    public function test_invalid_description_on_update_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(["created_by" => $user->id])->create();
        $contraindication = Contraindication::factory()->state(new Sequence(
            function($sequance) use ($drug) {
                return ["drug_id" => $drug->id, "order" => $sequance->index];
            }
        ))->create();

        $this->checkInvalidFieldPutAction($contraindication, 'description', '');
        $this->checkInvalidFieldPutAction($contraindication, 'description', 's');
    }

    public function test_invalid_level_on_update_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(["created_by" => $user->id])->create();
        $contraindication = Contraindication::factory()->state(new Sequence(
            function($sequance) use ($drug) {
                return ["drug_id" => $drug->id, "order" => $sequance->index];
            }
        ))->create();

        $this->checkInvalidFieldPutAction($contraindication, 'level', '');
        $this->checkInvalidFieldPutAction($contraindication, 'level', 's');
        $this->checkInvalidFieldPutAction($contraindication, 'level', 0);
    }

    public function test_invalid_order_on_update_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(["created_by" => $user->id])->create();
        $contraindication = Contraindication::factory()->state(new Sequence(
            function($sequance) use ($drug) {
                return ["drug_id" => $drug->id, "order" => $sequance->index];
            }
        ))->create();

        $this->checkInvalidFieldPutAction($contraindication, 'order', '');
        $this->checkInvalidFieldPutAction($contraindication, 'order', 's');
        $this->checkInvalidFieldPutAction($contraindication, 'order', -1);
    }

    public function test_invalid_drug_id_on_update_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(["created_by" => $user->id])->create();
        $contraindication = Contraindication::factory()->state(new Sequence(
            function($sequance) use ($drug) {
                return ["drug_id" => $drug->id, "order" => $sequance->index];
            }
        ))->create();

        $this->checkInvalidFieldPutAction($contraindication, 'drug_id', '');
        $this->checkInvalidFieldPutAction($contraindication, 'drug_id', 's');
        $this->checkInvalidFieldPutAction($contraindication, 'drug_id', 0);
    }

    public function checkInvalidFieldPutAction($target, $property, $tested_value) {
        $target->{$property} = $tested_value;
        $response = $this->putJson('api/v1/contraindications/' . $target->id, $target->toArray());
        $response->assertStatus(422);
    }

    public function test_update_notexist_contraindication() {
        $user = User::factory()->create();
        $drug = Drug::factory()->state(["created_by" => $user->id])->create();
        $contraindication = Contraindication::factory()->state(new Sequence(
            function($sequance) use ($drug) {
                return ["drug_id" => $drug->id, "order" => $sequance->index];
            }
        ))->create();
        $contraindication->delete();
        $contraindication->description = "New Updated Value On Contraindication";

        $response = $this->putJson("api/v1/contraindications/" . $contraindication->id, $contraindication->toArray());

        $response->assertStatus(404);
    }

    public function test_destroy_exist_contraindication (){
        $user = User::factory()->create();
        $drug = Drug::factory()->state(["created_by" => $user->id])->create();
        $contraindication = Contraindication::factory()->state(new Sequence(
            function($sequance) use($drug){
                return ["drug_id" => $drug->id, "order" => $sequance->index];
            }
        ))->create();

        $response = $this->deleteJson("api/v1/contraindications/" . $contraindication->id);

        $response->assertStatus(204);
    }

    public function test_destroy_notexist_contraindication (){
        $user = User::factory()->create();
        $drug = Drug::factory()->state(["created_by" => $user->id])->create();
        $contraindication = Contraindication::factory()->state(new Sequence(
            function($sequance) use($drug){
                return ["drug_id" => $drug->id, "order" => $sequance->index];
            }
        ))->create();
        $contraindication->delete();

        $response = $this->deleteJson("api/v1/contraindications/" . $contraindication->id);

        $response->assertStatus(404);
    }
}
