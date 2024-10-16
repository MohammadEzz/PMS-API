<?php

namespace Tests\Feature;

use App\Models\Drug;
use App\Models\DrugAlternative;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DrugAlternativeTest extends TestCase
{
    public function test_read_all_drugalternatives() {
        $user = User::factory()->create();
        $drugs = Drug::factory()->count(2)->create(["created_by" => $user->id]);
        DrugAlternative::factory()->state(new Sequence(function($sequance) {
            return ["order" => $sequance->index];
        }))->create([
            "drug_id" => $drugs[0]->id,
            "alternative_id" => $drugs[1]->id,
        ]);

        $response = $this->getJson('api/v1/drugalternatives');
        $response->assertStatus(200);
    }

    public function test_read_exist_drugalternative() {
        $user = User::factory()->create();
        $drugs = Drug::factory()->count(2)->create(["created_by" => $user->id]);
        $drugAlternative = DrugAlternative::factory()->state(new Sequence(function($sequance) {
            return ["order" => $sequance->index];
        }))->create([
            "drug_id" => $drugs[0]->id,
            "alternative_id" => $drugs[1]->id,
        ]);

        $response = $this->getJson('api/v1/drugalternatives/' . $drugAlternative->id);
        $response->assertStatus(200);
    }

    public function test_read_notexist_drugalternative() {
        $user = User::factory()->create();
        $drugs = Drug::factory()->count(2)->create(["created_by" => $user->id]);
        $drugAlternative = DrugAlternative::factory()->state(new Sequence(function($sequance) {
            return ["order" => $sequance->index];
        }))->create([
            "drug_id" => $drugs[0]->id,
            "alternative_id" => $drugs[1]->id,
        ]);
        $drugAlternative->delete();

        $response = $this->getJson('api/v1/drugalternatives/' . $drugAlternative->id);
        $response->assertStatus(404);
    }

    public function test_success_create_drugalternative() {
        $user = User::factory()->create();
        $drugs = Drug::factory()->count(2)->create(["created_by" => $user->id]);
        $drugAlternative = [
            "drug_id" => $drugs[0]->id,
            "alternative_id" => $drugs[1]->id,
            "order" => 111
        ];

        $response = $this->postJson('api/v1/drugalternatives', $drugAlternative);
        $response->assertStatus(201);
    }

    public function test_invalid_drug_id_on_create_drugalternative() {
        $drugAlternative = [
            "alternative_id" => 1,
            "order" => 1
        ];

        $this->checkInvalidFieldPostAction($drugAlternative, 'drug_id', '');
        $this->checkInvalidFieldPostAction($drugAlternative, 'drug_id', "some text");
        $this->checkInvalidFieldPostAction($drugAlternative, 'drug_id', 0);
    }

    public function test_invalid_alternative_id_on_create_drugalternative() {
        $drugAlternative = [
            "drug_id" => 1,
            "order" => 1
        ];

        $this->checkInvalidFieldPostAction($drugAlternative, 'alternative_id', '');
        $this->checkInvalidFieldPostAction($drugAlternative, 'alternative_id', "some text");
        $this->checkInvalidFieldPostAction($drugAlternative, 'alternative_id', 0);
    }

    public function test_invalid_order_on_create_drugalternative() {
        $drugAlternative = [
            "drug_id" => 1,
            "alternative_id" => 2
        ];

        $this->checkInvalidFieldPostAction($drugAlternative, 'order', -1);
        $this->checkInvalidFieldPostAction($drugAlternative, 'order', "some text");
        $this->checkInvalidFieldPostAction($drugAlternative, 'order', '');
    }

    public function test_invalid_drug_and_alternative_with_same_value_on_create_drugalternative() {
        $drugAlternative = [
            "drug_id" => 1,
            "alternative_id" => 1,
            "order" => 1
        ];

        $this->checkInvalidFieldPostAction($drugAlternative, 'drug_id', $drugAlternative['alternative_id']);
    }

    public function checkInvalidFieldPostAction($target, $property, $tested_value) {
        $target[$property] = $tested_value;

        $response = $this->postJson('api/v1/drugalternatives', $target);
        $response->assertStatus(422);
    }

    public function test_update_exist_drugalternative() {
        $user = User::factory()->create();
        $drugs = Drug::factory()->count(2)->create(["created_by" => $user->id]);
        $drugAlternative = DrugAlternative::factory()->state(new Sequence(function($sequance) {
            return ["order" => $sequance->index];
        }))->create([
            "drug_id" => $drugs[0]->id,
            "alternative_id" => $drugs[1]->id,
        ]);
        $drugAlternative->order = 55;

        $response = $this->putJson('api/v1/drugalternatives/' . $drugAlternative->id, $drugAlternative->toArray());
        $response->assertStatus(204);
    }

    public function test_invalid_drug_id_on_update_drugalternative() {
        $user = User::factory()->create();
        $drugs = Drug::factory()->count(2)->create(["created_by" => $user->id]);
        $drugAlternative = DrugAlternative::factory()->create([
            "drug_id" => $drugs[0]->id,
            "alternative_id" => $drugs[1]->id,
            "order" => 0
        ]);

        $this->checkInvalidFieldPutAction($drugAlternative, 'drug_id', '');
        $this->checkInvalidFieldPutAction($drugAlternative, 'drug_id', "some text");
        $this->checkInvalidFieldPutAction($drugAlternative, 'drug_id', 0);
    }

    public function test_invalid_alternative_id_on_update_drugalternative() {
        $user = User::factory()->create();
        $drugs = Drug::factory()->count(2)->create(["created_by" => $user->id]);
        $drugAlternative = DrugAlternative::factory()->create([
            "drug_id" => $drugs[0]->id,
            "alternative_id" => $drugs[1]->id,
            "order" => 0
        ]);

        $this->checkInvalidFieldPutAction($drugAlternative, 'alternative_id', '');
        $this->checkInvalidFieldPutAction($drugAlternative, 'alternative_id', "some text");
        $this->checkInvalidFieldPutAction($drugAlternative, 'alternative_id', 0);
    }

    public function test_invalid_order_on_update_drugalternative() {
        $user = User::factory()->create();
        $drugs = Drug::factory()->count(2)->create(["created_by" => $user->id]);
        $drugAlternative = DrugAlternative::factory()->create([
            "drug_id" => $drugs[0]->id,
            "alternative_id" => $drugs[1]->id,
            "order" => 0
        ]);

        $this->checkInvalidFieldPutAction($drugAlternative, 'order', '');
        $this->checkInvalidFieldPutAction($drugAlternative, 'order', "some text");
        $this->checkInvalidFieldPutAction($drugAlternative, 'order',  -1);
    }

    public function test_invalid_drug_and_alternative_with_same_value_on_update_drugalternative() {
        $user = User::factory()->create();
        $drugs = Drug::factory()->count(2)->create(["created_by" => $user->id]);
        $drugAlternative = DrugAlternative::factory()->create([
            "drug_id" => $drugs[0]->id,
            "alternative_id" => $drugs[1]->id,
            "order" => 0
        ]);

        $this->checkInvalidFieldPutAction($drugAlternative, 'drug_id', $drugAlternative['alternative_id']);
    }

    public function checkInvalidFieldPutAction($target, $property, $tested_value) {
        $target->{$property} = $tested_value;

        $response = $this->putJson('api/v1/drugalternatives/' . $target->id, $target->toArray());
        $response->assertStatus(422);
    }

    public function test_update_notexist_drugalternative() {
        $user = User::factory()->create();
        $drugs = Drug::factory()->count(2)->create(["created_by" => $user->id]);
        $drugAlternative = DrugAlternative::factory()->state(new Sequence(function($sequance) {
            return ["order" => $sequance->index];
        }))->create([
            "drug_id" => $drugs[0]->id,
            "alternative_id" => $drugs[1]->id,
        ]);
        $drugAlternative->order = 55;
        $drugAlternative->delete();

        $response = $this->putJson('api/v1/drugalternatives/' . $drugAlternative->id, $drugAlternative->toArray());
        $response->assertStatus(404);
    }

    public function test_destroy_exist_drugalternative() {
        $user = User::factory()->create();
        $drugs = Drug::factory()->count(2)->create(["created_by" => $user->id]);
        $drugAlternative = DrugAlternative::factory()->state(new Sequence(function($sequance) {
            return ["order" => $sequance->index];
        }))->create([
            "drug_id" => $drugs[0]->id,
            "alternative_id" => $drugs[1]->id,
        ]);

        $response = $this->deleteJson('api/v1/drugalternatives/' . $drugAlternative->id);
        $response->assertStatus(204);
    }

    public function test_destroy_notexist_drugalternative() {
        $user = User::factory()->create();
        $drugs = Drug::factory()->count(2)->create(["created_by" => $user->id]);
        $drugAlternative = DrugAlternative::factory()->state(new Sequence(function($sequance) {
            return ["order" => $sequance->index];
        }))->create([
            "drug_id" => $drugs[0]->id,
            "alternative_id" => $drugs[1]->id,
        ]);
        $drugAlternative->delete();

        $response = $this->deleteJson('api/v1/drugalternatives/' . $drugAlternative->id);
        $response->assertStatus(404);
    }
}
