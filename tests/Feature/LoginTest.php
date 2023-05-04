<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

use \App\Models\User;

class LoginTest extends TestCase
{
	use RefreshDatabase, WithFaker;

    public function test_login_should_return_access_token_on_success(): void
	{
		$loginData = [
			'email' => $this->faker->unique()->safeEmail,
			'password' => $this->faker->password(6, 191),
		];

		User::factory()->create([
			'email' => $loginData['email'],
			'password' => bcrypt($loginData['password']),
		]);

		$response = $this->postJson(route('login'), $loginData);

		$response->assertStatus(200);

		$response->assertJson(function( AssertableJson $json ) {
			$json->has('data.plainTextToken');
			$json->has('data.user');
		});
	}

    public function test_login_should_return_error_message_when_email_not_found(): void
	{
		$loginData = [
			'email' => $this->faker->unique()->safeEmail,
			'password' => $this->faker->password(6, 191),
		];

		User::factory()->create([
			'email' => 'foo@bar.com',
			'password' => bcrypt($loginData['password']),
		]);

		$response = $this->postJson(route('login'), $loginData);

		$response->assertStatus(401);

		$response->assertJson(function( AssertableJson $json ) {
			$json->where('message', 'E-mail e/ou senha incorretos');
		});
	}

    public function test_login_should_return_error_message_when_password_do_not_match(): void
	{
		$loginData = [
			'email' => $this->faker->unique()->safeEmail,
			'password' => '246810',
		];

		User::factory()->create([
			'email' => $loginData['email'],
			'password' => bcrypt('123456'),
		]);

		$response = $this->postJson(route('login'), $loginData);

		$response->assertStatus(401);

		$response->assertJson(function( AssertableJson $json ) {
			$json->where('message', 'E-mail e/ou senha incorretos');
		});
	}

    public function test_login_should_return_error_when_password_is_empty(): void
	{
		$loginData = [
			'email' => $this->faker->unique()->safeEmail,
			'password' => '',
		];

		$response = $this->postJson(route('login'), $loginData);

		$response->assertStatus(422);
	}

    public function test_login_should_return_error_when_password_is_smaller_than_6_characters(): void
	{
		$loginData = [
			'email' => $this->faker->unique()->safeEmail,
			'password' => '12345',
		];

		$response = $this->postJson(route('login'), $loginData);

		$response->assertStatus(422);
	}

    public function test_login_should_return_error_when_password_is_bigger_than_191_characters(): void
	{
		$loginData = [
			'email' => $this->faker->unique()->safeEmail,
			'password' => str_repeat('a', 192),
		];

		$response = $this->postJson(route('login'), $loginData);

		$response->assertStatus(422);
	}

    public function test_login_should_return_error_when_email_is_empty(): void
	{
		$loginData = [
			'email'    => '',
			'password' => $this->faker->password(6, 191),
		];

		$response = $this->postJson(route('login'), $loginData);

		$response->assertStatus(422);
	}

    public function test_login_should_return_error_when_email_format_is_invalid(): void
	{
		$loginData = [
			'email'    => 'maria1234',
			'password' => $this->faker->password(6, 191),
		];

		$response = $this->postJson(route('login'), $loginData);

		$response->assertStatus(422);
	}
}
