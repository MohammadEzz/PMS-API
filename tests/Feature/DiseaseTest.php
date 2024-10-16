<?php

namespace Tests\Feature;

use App\Models\Disease;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DiseaseTest extends TestCase
{

    use WithFaker;

    public function test_read_all_diseases() {
        Disease::factory()->count(2)->create();

        $response = $this->getJson('api/v1/diseases');
        $response->assertStatus(200);
    }

    public function test_read_exist_disease() {
        $disease = Disease::factory()->create();

        $response = $this->getJson("api/v1/diseases/$disease->id");
        $response->assertStatus(200);
    }

    public function test_read_notexist_disease() {
        $disease = Disease::factory()->create();
        $disease->delete();

        $response = $this->getJson("api/v1/diseases/$disease->id");
        $response->assertStatus(404);
    }

    public function test_success_create_disease() {
        $disease = [
            "categoryid" => 1,
            "name" => "New Created Disease",
            "globalname" => "New Created Disease Global",
        ];

        $response = $this->postJson('api/v1/diseases', $disease);
        $response->assertStatus(200);
    }

    public function test_invalid_categoryid_on_create_disease() {
        $disease = [
            "name" => "New Created Disease",
            "globalname" => "New Created Disease Global",
        ];

        $this->checkInvalidFieldPostAction($disease, 'categoryid', '');
        $this->checkInvalidFieldPostAction($disease, 'categoryid', 's');
        $this->checkInvalidFieldPostAction($disease, 'categoryid', 0);
    }

    public function test_invalid_name_on_create_disease() {
        $disease = [
            "categoryid" => 1,
            "globalname" => "New Created Disease Global",
        ];

        $this->checkInvalidFieldPostAction($disease, 'name', '');
        $this->checkInvalidFieldPostAction($disease, 'name', 's');
        $this->checkInvalidFieldPostAction($disease, 'name', $this->faker()->sentence(100));
    }

    public function test_invalid_globalname_on_create_disease() {
        $disease = [
            "categoryid" => 1,
            "name" => $this->faker()->sentence(3)
        ];

        $this->checkInvalidFieldPostAction($disease, 'globalname', 's');
        $this->checkInvalidFieldPostAction($disease, 'globalname', $this->faker()->sentence(100));
    }

    public function checkInvalidFieldPostAction($target, $property, $tested_value) {
        $target[$property] = $tested_value;

        $response = $this->postJson('api/v1/diseases', $target);
        $response->assertStatus(422);
    }

    public function test_update_exist_disease() {
        $disease = Disease::factory()->create();
        $disease->globalname = "New Update Disease Global";

        $response = $this->putJson("api/v1/diseases/$disease->id", $disease->toArray());
        $response->assertStatus(201);
    }

    public function test_invalid_categoryid_on_update_disease() {
        $disease = Disease::factory()->create();

        $this->checkInvalidFieldPutAction($disease, 'categoryid', '');
        $this->checkInvalidFieldPutAction($disease, 'categoryid', 's');
        $this->checkInvalidFieldPutAction($disease, 'categoryid', 0);
    }

    public function test_invalid_name_on_update_disease() {
        $disease = Disease::factory()->create();

        $this->checkInvalidFieldPutAction($disease, 'name', '');
        $this->checkInvalidFieldPutAction($disease, 'name', 's');
        $this->checkInvalidFieldPutAction($disease, 'name', $this->faker()->sentence(100));
    }

    public function test_invalid_globalname_on_update_disease() {
        $disease = Disease::factory()->create();

        $this->checkInvalidFieldPutAction($disease, 'globalname', 's');
        $this->checkInvalidFieldPutAction($disease, 'globalname', $this->faker()->sentence(100));
    }

    public function checkInvalidFieldPutAction($target, $property, $tested_value) {
        $target->{$property} = $tested_value;

        $response = $this->putJson("api/v1/diseases/$target->id", $target->toArray());
        $response->assertStatus(422);
    }

    public function test_update_notexist_disease() {
        $disease = Disease::factory()->create();
        $disease->globalname = "New Update Disease Global";
        $disease->delete();

        $response = $this->putJson("api/v1/diseases/$disease->id", $disease->toArray());
        $response->assertStatus(404);
    }

    public function test_destroy_exist_disease() {
        $disease = Disease::factory()->create();

        $response = $this->deleteJson("api/v1/diseases/$disease->id");
        $response->assertStatus(201);
    }

    public function test_destroy_notexist_disease() {
        $disease = Disease::factory()->create();
        $disease->delete();

        $response = $this->deleteJson("api/v1/diseases/$disease->id");
        $response->assertStatus(404);
    }
}
