<?php

namespace Tests\Feature;

use App\Models\ActiveIngredient;
use App\Models\DrugInteraction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DrugInteractionTest extends TestCase
{
    public function test_read_all_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        DrugInteraction::factory()->create(
            [
                'activeingredient1' => $activeIngredients[0]->id,
                'activeingredient2' => $activeIngredients[1]->id
            ]);

        $response = $this->getJson('api/v1/druginteractions');
        $response->assertStatus(200);
    }

    public function test_read_exist_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = DrugInteraction::factory()->create(
            [
                'activeingredient1' => $activeIngredients[0]->id,
                'activeingredient2' => $activeIngredients[1]->id
            ]);

        $response = $this->getJson("api/v1/druginteractions/$drugInteraction->id");
        $response->assertStatus(200);
    }


    public function test_read_notexist_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = DrugInteraction::factory()->create(
            [
                'activeingredient1' => $activeIngredients[0]->id,
                'activeingredient2' => $activeIngredients[1]->id
            ]);
        $drugInteraction->delete();

        $response = $this->getJson('api/v1/druginteractions/' . $drugInteraction->id);
        $response->assertStatus(404);
    }

    public function test_success_create_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = [
            'activeingredient1' => $activeIngredients[0]->id,
            'activeingredient2' => $activeIngredients[1]->id,
            'level' => 4,
            "description" => "New Created Drug Interaction"
        ];

        $response = $this->postJson('api/v1/druginteractions', $drugInteraction);

        $response->assertStatus(201);
    }

    public function test_invalid_activeingredient1_on_create_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = [
            'activeingredient2' => $activeIngredients[1]->id,
            'level' => 4,
            "description" => "New Created Drug Interaction"
        ];

        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        $this->checkInvalidFieldPostAction($drugInteraction, 'activeingredient1', '');
        $this->checkInvalidFieldPostAction($drugInteraction, 'activeingredient1', 's');
        $this->checkInvalidFieldPostAction($drugInteraction, 'activeingredient1', 0);
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function test_invalid_activeingredient2_on_create_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = [
            'activeingredient1' => $activeIngredients[1]->id,
            'level' => 4,
            "description" => "New Created Drug Interaction"
        ];

        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        $this->checkInvalidFieldPostAction($drugInteraction, 'activeingredient2', '');
        $this->checkInvalidFieldPostAction($drugInteraction, 'activeingredient2', 's');
        $this->checkInvalidFieldPostAction($drugInteraction, 'activeingredient2', 0);
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function test_invalid_level_on_create_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = [
            'activeingredient1' => $activeIngredients[0]->id,
            'activeingredient2' => $activeIngredients[1]->id,
            "description" => "New Created Drug Interaction"
        ];

        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        $this->checkInvalidFieldPostAction($drugInteraction, 'level', '');
        $this->checkInvalidFieldPostAction($drugInteraction, 'level', 's');
        $this->checkInvalidFieldPostAction($drugInteraction, 'level', 0);
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function test_invalid_description_on_create_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = [
            'activeingredient1' => $activeIngredients[0]->id,
            'activeingredient2' => $activeIngredients[1]->id,
            "level" => 1
        ];

        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        $this->checkInvalidFieldPostAction($drugInteraction, 'description', 's');
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function test_invalid_activeingredient1_and_activeingredient2_with_same_value_on_create_druginteraction() {
        $activeIngredient = ActiveIngredient::factory()->create();
        $drugInteraction = [
            'activeingredient1' => $activeIngredient->id,
            'activeingredient2' => $activeIngredient->id,
            "level" => 1,
            "description" => "Some Text"
        ];

        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        $this->checkInvalidFieldPostAction($drugInteraction, 'activeingredient1', $drugInteraction['activeingredient2']);
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function checkInvalidFieldPostAction($target, $property, $tested_value) {
        $target[$property] = $tested_value;
        $response = $this->postJson('api/v1/druginteractions', $target);
        $response->assertStatus(422);
    }

    public function test_update_exist_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = DrugInteraction::factory()->create(
            [
                'activeingredient1' => $activeIngredients[0]->id,
                'activeingredient2' => $activeIngredients[1]->id
            ]);
        $drugInteraction->description = "New Updated Drug Interaction";

        $response = $this->putJson('api/v1/druginteractions/' . $drugInteraction->id, $drugInteraction->toArray());

        $response->assertStatus(204);
    }


    public function test_invalid_activeingredient1_on_update_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = DrugInteraction::factory()->create(
            [
                'activeingredient1' => $activeIngredients[0]->id,
                'activeingredient2' => $activeIngredients[1]->id
            ]);

        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        $this->checkInvalidFieldPutAction($drugInteraction, 'activeingredient1', '');
        $this->checkInvalidFieldPutAction($drugInteraction, 'activeingredient1', 's');
        $this->checkInvalidFieldPutAction($drugInteraction, 'activeingredient1', 0);
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function test_invalid_activeingredient2_on_update_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = DrugInteraction::factory()->create(
            [
                'activeingredient1' => $activeIngredients[0]->id,
                'activeingredient2' => $activeIngredients[1]->id
            ]);

        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        $this->checkInvalidFieldPutAction($drugInteraction, 'activeingredient2', '');
        $this->checkInvalidFieldPutAction($drugInteraction, 'activeingredient2', 's');
        $this->checkInvalidFieldPutAction($drugInteraction, 'activeingredient2', 0);
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function test_invalid_level_on_update_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = DrugInteraction::factory()->create(
            [
                'activeingredient1' => $activeIngredients[0]->id,
                'activeingredient2' => $activeIngredients[1]->id
            ]);

        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        $this->checkInvalidFieldPutAction($drugInteraction, 'level', '');
        $this->checkInvalidFieldPutAction($drugInteraction, 'level', 's');
        $this->checkInvalidFieldPutAction($drugInteraction, 'level', 0);
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function test_invalid_description_on_update_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = DrugInteraction::factory()->create(
            [
                'activeingredient1' => $activeIngredients[0]->id,
                'activeingredient2' => $activeIngredients[1]->id
            ]);

        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        $this->checkInvalidFieldPutAction($drugInteraction, 'description', 's');
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function test_invalid_activeingredient1_and_activeingredient2_with_same_value_on_update_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->create();
        $drugInteraction = DrugInteraction::factory()->create(
            [
                'activeingredient1' => $activeIngredients->id,
                'activeingredient2' => $activeIngredients->id
            ]);

        DB::statement("SET FOREIGN_KEY_CHECKS = 0");
        $this->checkInvalidFieldPutAction($drugInteraction, 'activeingredient1', $drugInteraction['activeingredient2']);
        DB::statement("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function checkInvalidFieldPutAction($target, $property, $tested_value) {
        $target->{$property} = $tested_value;

        $response = $this->putJson('api/v1/druginteractions/' . $target->id, $target->toArray());
        $response->assertStatus(422);
    }

    public function test_update_notexist_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = DrugInteraction::factory()->create(
            [
                'activeingredient1' => $activeIngredients[0]->id,
                'activeingredient2' => $activeIngredients[1]->id,
            ]);

        $drugInteraction->delete();

        $drugInteraction->description = "New Updated Drug Interaction";

        $response = $this->putJson('api/v1/druginteractions/' . $drugInteraction->id, $drugInteraction->toArray());
        $response->assertStatus(404);
    }

    public function test_destroy_exist_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = DrugInteraction::factory()->create(
            [
                'activeingredient1' => $activeIngredients[0]->id,
                'activeingredient2' => $activeIngredients[1]->id
            ]);

        $response = $this->deleteJson('api/v1/druginteractions/' . $drugInteraction->id);
        $response->assertStatus(204);
    }

    public function test_destroy_notexist_druginteraction() {
        $activeIngredients = ActiveIngredient::factory()->count(2)->create();
        $drugInteraction = DrugInteraction::factory()->create(
            [
                'activeingredient1' => $activeIngredients[0]->id,
                'activeingredient2' => $activeIngredients[1]->id
            ]);
        $drugInteraction->delete();

        $response = $this->deleteJson('api/v1/druginteractions/' . $drugInteraction->id);
        $response->assertStatus(404);
    }
}
