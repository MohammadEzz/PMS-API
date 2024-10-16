<?php

namespace Tests\Feature;

use App\Models\Option;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OptionTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
   public function test_read_all_options() {
      $this->seed();
      $response = $this->getJson('api/v1/options');
      $response->assertStatus(200);
    }
}
