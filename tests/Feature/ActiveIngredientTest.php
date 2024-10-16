<?php

namespace Tests\Feature;

use App\Models\ActiveIngredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ActiveIngredientTest extends TestCase
{

    use WithFaker;

    public function test_read_all_activeingredients() {
        ActiveIngredient::factory()->create();

        $response = $this->getJson('api/v1/activeingredients');

        $response->assertStatus(200);
    }

    public function test_read_exist_activeingredient() {
        $activeIngredient = ActiveIngredient::factory()->create();

        $response = $this->getJson('api/v1/activeingredients/' . $activeIngredient->id);

        $response->assertStatus(200);
    }


    public function test_read_notexist_activeingredient() {
        $activeIngredient = ActiveIngredient::factory()->create();
        $activeIngredient->delete();

        $response = $this->getJson('api/v1/activeingredients/' . $activeIngredient->id);

        $response->assertStatus(404);
    }

    public function test_success_create_activeingredient() {
        $activeIngredient = [
            "name" => $this->faker()->sentence(3),
            "globalname" => $this->faker()->sentence(3),
        ];

        $response = $this->postJson("api/v1/activeingredients", $activeIngredient);

        $response->assertStatus(200);
    }



    public function test_invalid_name_on_create_activeingredientl() {
        $activeIngredient = [
            "globalname" => $this->faker()->sentence(3),
        ];

        $this->checkInvalidFieldPostAction($activeIngredient, 'name', '');
        $this->checkInvalidFieldPostAction($activeIngredient, 'name', 's');
        $this->checkInvalidFieldPostAction($activeIngredient, 'name', $this->faker()->sentence(100));
    }

    public function test_invalid_globalname_on_create_activeingredientl() {
        $activeIngredient = [
            "name" => $this->faker()->sentence(3),
        ];

        $this->checkInvalidFieldPostAction($activeIngredient, 'globalname', 's');
        $this->checkInvalidFieldPostAction($activeIngredient, 'globalname', $this->faker()->sentence(100));
    }

    public function checkInvalidFieldPostAction($target, $property, $tested_value) {
        $target[$property] = $tested_value;
        $response = $this->postJson('api/v1/activeingredients', $target);
        $response->assertStatus(422);
    }

    public function test_update_exist_activeingredient() {
        $activeIngredient = ActiveIngredient::factory()->create();
        $activeIngredient->name = $this->faker()->sentence(3);
        $activeIngredient->globalname = $this->faker()->sentence(3);

        $response = $this->putJson('api/v1/activeingredients/' . $activeIngredient->id, $activeIngredient->toArray());

        $response->assertStatus(204);
    }

    public function test_invalid_name_on_update_activeingredientl() {
        $activeIngredient = ActiveIngredient::factory()->create();

        $this->checkInvalidFieldPutAction($activeIngredient, 'name', '');
        $this->checkInvalidFieldPutAction($activeIngredient, 'name', 's');
        $this->checkInvalidFieldPutAction($activeIngredient, 'name', $this->faker()->sentence(100));
    }

    public function test_invalid_globalname_on_update_activeingredientl() {
        $activeIngredient = ActiveIngredient::factory()->create();

        $this->checkInvalidFieldPutAction($activeIngredient, 'globalname', 's');
        $this->checkInvalidFieldPutAction($activeIngredient, 'globalname', $this->faker()->sentence(100));
    }

    public function checkInvalidFieldPutAction($target, $property, $tested_value) {
        $target->{$property} = $tested_value;
        $response = $this->putJson('api/v1/activeingredients/' . $target->id, $target->toArray());
        $response->assertStatus(422);
    }

    public function test_update_notexist_activeingredient() {
        $activeIngredient = ActiveIngredient::factory()->create();
        $activeIngredient->delete();
        $activeIngredient->name = $this->faker()->sentence(3);
        $activeIngredient->globalname = $this->faker()->sentence(3);

        $response = $this->putJson('api/v1/activeingredients/' . $activeIngredient->id, $activeIngredient->toArray());

        $response->assertStatus(404);
    }

    public function test_destroy_exist_activeingredient() {
        $activeIngredient = ActiveIngredient::factory()->create();

        $response = $this->deleteJson("api/v1/activeingredients/" . $activeIngredient->id);

        $response->assertStatus(204);
    }

    public function test_destroy_notexist_activeingredient() {
        $activeIngredient = ActiveIngredient::factory()->create();
        $activeIngredient->delete();

        $response = $this->deleteJson("api/v1/activeingredients/" . $activeIngredient->id);

        $response->assertStatus(404);
    }
}
