<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_register_should_return_user_data_on_success(): void
    {
        $userData = [
            'name'     => $this->faker->name,
            'email'    => $this->faker->unique()->safeEmail,
            'password' => $this->faker->password(6, 191),
        ];

        $response = $this->postJson(route('user.store'), $userData);

        $response->assertStatus(201);

        $response->assertJson(function( AssertableJson $json ) use ($userData) {
            $json->where('data.id', 1);
            $json->where('data.name', $userData['name']);
            $json->where('data.email', $userData['email']);
            $json->has('data.created_at');
            $json->has('data.updated_at');
        });
    }

    public function test_user_register_should_return_error_message_when_name_is_empty(): void
    {
        $userData = [
            'name'     => '',
            'email'    => $this->faker->unique()->safeEmail,
            'password' => $this->faker->password(6, 191),
        ];

        $response = $this->postJson(route('user.store'), $userData);

        $response->assertStatus(422);
        
        $response->assertJsonValidationErrorFor('name');
    }

    public function test_user_register_should_return_error_message_when_name_smaller_than_3_characters(): void
    {
        $userData = [
            'name'     => str_repeat('a', 2),
            'email'    => $this->faker->unique()->safeEmail,
            'password' => $this->faker->password(6, 191),
        ];

        $response = $this->postJson(route('user.store'), $userData);

        $response->assertStatus(422);
        
        $response->assertJsonValidationErrorFor('name');
    }

    public function test_user_register_should_return_error_message_when_name_bigger_than_191_characters(): void
    {
        $userData = [
            'name'     => str_repeat('a', 192),
            'email'    => $this->faker->unique()->safeEmail,
            'password' => $this->faker->password(6, 191),
        ];

        $response = $this->postJson(route('user.store'), $userData);

        $response->assertStatus(422);
        
        $response->assertJsonValidationErrorFor('name');
    }

    public function test_user_register_should_return_error_message_when_email_empty(): void
    {
        $userData = [
            'name'     => $this->faker->name(),
            'email'    => '',
            'password' => $this->faker->password(6, 191),
        ];

        $response = $this->postJson(route('user.store'), $userData);

        $response->assertStatus(422);
        
        $response->assertJsonValidationErrorFor('email');
    }

    public function test_user_register_should_return_error_message_when_email_has_invalid_format(): void
    {
        $userData = [
            'name'     => $this->faker->name,
            'email'    => $this->faker->words(5, true),
            'password' => $this->faker->password(6, 191),
        ];

        $response = $this->postJson(route('user.store'), $userData);

        $response->assertStatus(422);
        
        $response->assertJsonValidationErrorFor('email');
    }

    public function test_user_register_should_return_error_message_when_email_already_exists(): void
    {
        $userData = [
            'name'     => $this->faker->name(),
            'email'    => $this->faker->unique()->safeEmail,
            'password' => $this->faker->password(6, 191),
        ];

        User::factory(1)->create($userData);

        $response = $this->postJson(route('user.store'), $userData);

        $response->assertStatus(422);
        
        $response->assertJsonValidationErrorFor('email');
    }

    public function test_user_register_should_return_error_message_when_email_bigger_than_191_characters(): void
    {
        $userData = [
            'name'     => $this->faker->name(),
            'email'    => str_repeat('a', 191) . $this->faker->unique()->safeEmail,
            'password' => $this->faker->password(6, 191),
        ];

        $response = $this->postJson(route('user.store'), $userData);

        $response->assertStatus(422);
        
        $response->assertJsonValidationErrorFor('email');
    }

    public function test_user_register_should_return_error_message_when_password_empty(): void
    {
        $userData = [
            'name'     => $this->faker->name(),
            'email'    => $this->faker->unique()->safeEmail,
            'password' => '',
        ];

        $response = $this->postJson(route('user.store'), $userData);

        $response->assertStatus(422);
        
        $response->assertJsonValidationErrorFor('password');
    }

    public function test_user_register_should_return_error_message_when_password_bigger_than_191_characters(): void
    {
        $userData = [
            'name'     => $this->faker->name(),
            'email'    => $this->faker->unique()->safeEmail,
            'password' => $this->faker->password(192, 255),
        ];

        $response = $this->postJson(route('user.store'), $userData);

        $response->assertStatus(422);
        
        $response->assertJsonValidationErrorFor('password');
    }

    public function test_user_register_should_return_error_message_when_password_smaller_than_6_characters(): void
    {
        $userData = [
            'name'     => $this->faker->name(),
            'email'    => $this->faker->unique()->safeEmail,
            'password' => $this->faker->password(1, 5),
        ];

        $response = $this->postJson(route('user.store'), $userData);

        $response->assertStatus(422);
        
        $response->assertJsonValidationErrorFor('password');
    }

    public function test_after_insert_user_password_must_be_encrypted(): void
    {
        $userData = [
            'name'     => $this->faker->name(),
            'email'    => $this->faker->unique()->safeEmail,
            'password' => $this->faker->password(6, 191),
        ];

        $response = $this->postJson(route('user.store'), $userData);

        $user = User::where('email', $userData['email'])->first();

        $response->assertStatus(201);
        
        $this->assertNotEquals($userData['password'], $user->password);
    }
}
