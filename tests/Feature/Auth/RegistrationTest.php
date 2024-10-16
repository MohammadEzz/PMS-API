<?php

namespace Tests\Feature\Auth;

use ArrayObject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;
use Mockery\Undefined;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $data;

    protected function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'firstname' => $this->faker->name(),
            'middlename' => $this->faker->name(),
            'lastname' => $this->faker->name(),
            'gender' => 'male',
            'birthdate' => $this->faker->date(),
            'country' => 1,
            'city' => 1,
            'address' => "Part 2, El Saeed Nassar st, appartment 36, floor 4, flat 8",
            'nationalid' => strVal($this->faker->unique()->randomNumber(7, true)),
            'passportnum' => strVal($this->faker->unique()->randomNumber(7, true)),
            'username' => $this->faker->unique()->regexify('[A-Z]{5}[0-9]{3}'),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'mMm1509ez@',
            'password_confirmation' => 'mMm1509ez@',
            'remember_token' => Str::random(10),
            'status' => 1,
            'visible' => 'visible',
            'note' => "Some note related to user",
            'created_by' => 1,
        ];

    }

    public function test_new_users_can_register()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/register', $this->data);

        $response->assertStatus(200);
    }

    public function checkInvalidFieldPostAction($target, $property, $tested_value) {
        $target[$property] = $tested_value;
        $response = $this->postJson('/register', $target);
        $response->assertStatus(422);
    }

    public function test_new_user_cannot_register_when_first_name_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'firstname', '');
        $this->checkInvalidFieldPostAction($data, 'firstname', 's');
    }

    public function test_new_user_cannot_register_when_middle_name_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'middlename', 's');
    }

    public function test_new_user_cannot_register_when_last_name_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'lastname', '');
        $this->checkInvalidFieldPostAction($data, 'lastname', 's');
    }

    public function test_new_user_cannot_register_when_gender_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'gender', '');
        $this->checkInvalidFieldPostAction($data, 'gender', 's');
    }

    public function test_new_user_cannot_register_when_birth_date_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'birthdate', '');
        $this->checkInvalidFieldPostAction($data, 'birthdate', 's');
    }

    public function test_new_user_cannot_register_when_country_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'country', '');
        $this->checkInvalidFieldPostAction($data, 'country', 's');
        $this->checkInvalidFieldPostAction($data, 'country', '0');
    }

    public function test_new_user_cannot_register_when_city_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'city', '');
        $this->checkInvalidFieldPostAction($data, 'city', 's');
        $this->checkInvalidFieldPostAction($data, 'city', '0');
    }

    public function test_new_user_cannot_register_when_address_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'address', '');
        $this->checkInvalidFieldPostAction($data, 'address', 's');
    }

    public function test_new_user_cannot_register_when_nationalid_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'nationalid', '');
        $this->checkInvalidFieldPostAction($data, 'nationalid', 's');
    }

    public function test_new_user_cannot_register_when_passportnum_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'passportnum', '');
        $this->checkInvalidFieldPostAction($data, 'passportnum', 's');
    }

    public function test_new_user_cannot_register_when_username_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'username', '');
        $this->checkInvalidFieldPostAction($data, 'username', 's');
    }

    public function test_new_user_cannot_register_when_password_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();
        $data['password_confirmation'] = "mM1#d";

        $this->checkInvalidFieldPostAction($data, 'password', '');
        $this->checkInvalidFieldPostAction($data, 'password', 's');
        $this->checkInvalidFieldPostAction($data, 'password', 'mM1#d');
    }

    public function test_new_user_cannot_register_when_status_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'status', '');
        $this->checkInvalidFieldPostAction($data, 'status', 's');
        $this->checkInvalidFieldPostAction($data, 'status', 0);
    }

    public function test_new_user_cannot_register_when_visible_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'visible', '');
        $this->checkInvalidFieldPostAction($data, 'visible', 's');
    }

    public function test_new_user_cannot_register_when_email_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'email', '');
        $this->checkInvalidFieldPostAction($data, 'email', 's');
    }

    public function test_new_user_cannot_register_when_note_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'note', 's');
    }

    public function test_new_user_cannot_register_when_created_by_failed() {
        $data = (new ArrayObject($this->data))->getArrayCopy();

        $this->checkInvalidFieldPostAction($data, 'created_by', '');
        $this->checkInvalidFieldPostAction($data, 'created_by', 's');
        $this->checkInvalidFieldPostAction($data, 'created_by', 0);
    }



}
