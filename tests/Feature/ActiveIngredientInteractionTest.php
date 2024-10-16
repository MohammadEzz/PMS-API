<?php

namespace Tests\Feature;

use App\Models\ActiveIngredient;
use App\Models\DrugInteraction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ActiveIngredientInteractionTest extends TestCase
{
    public function test_read_all_active_ingredient_interactions() {

        $activeIngredient = ActiveIngredient::factory()->count(4)->create();

        DrugInteraction::factory()
        ->create(["activeingredient1" => $activeIngredient[0]->id, "activeingredient2" => $activeIngredient[1]->id]);
        DrugInteraction::factory()
        ->create(["activeingredient1" => $activeIngredient[0]->id, "activeingredient2" => $activeIngredient[2]->id]);
        DrugInteraction::factory()
        ->create(["activeingredient1" => $activeIngredient[0]->id, "activeingredient2" => $activeIngredient[3]->id]);

        $this->assertModelExists($activeIngredient[3]);

        $response = $this->getJson("api/v1/activeingredients/{$activeIngredient[0]->id}/interactions");
        $response->assertStatus(200);
    }

    public function test_read_not_exist_active_ingredient_when_read_all_active_ingredient_interactions() {
        $activeIngredient = ActiveIngredient::factory()->create();

        $activeIngredient->delete();
        $this->assertModelMissing($activeIngredient);

        $response = $this->getJson("api/v1/activeingredients/{$activeIngredient->id}/interactions");
        $response->assertStatus(404);
    }

}
