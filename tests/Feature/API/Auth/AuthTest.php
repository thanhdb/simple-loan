<?php

namespace Tests\Feature\API\Auth;

use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Test user can register
     * vendor/bin/phpunit --filter test_user_can_register
     * php artisan test --filter=test_user_can_register
     *
     * @return void
     */
    public function test_user_can_register()
    {
        $data = [
            'name' => 'new user',
            'email' => 'newuser@mail.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'customer'
        ];

        //execute request
        $response = $this->postJson(route('register'), $data);
        $response->assertStatus(201);

        $responseArr = $response->json();
        $this->assertArrayHasKey('token', $responseArr['data']);
    }

    /**
     * Test user can login
     * vendor/bin/phpunit --filter test_user_can_login
     * php artisan test --filter=test_user_can_login
     *
     * @return void
     */
    public function test_user_can_login()
    {
        $data = [
            'name' => 'new user 2',
            'email' => 'newuser2@mail.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'customer'
        ];

        //execute request
        $registerResponse = $this->postJson(route('register'), $data);
        $registerResponse->assertStatus(201);

        $loginData = [
            'email' => 'newuser2@mail.com',
            'password' => '123456'
        ];

        $loginResponse = $this->postJson(route('login'), $loginData);
        $loginResponse->assertStatus(200);

        $dataResponse = $loginResponse->json();
        $this->assertArrayHasKey('token', $dataResponse['data']);
    }

    /**
     * Test user can logout
     * vendor/bin/phpunit --filter test_user_can_logout
     * php artisan test --filter=test_user_can_logout
     *
     * @return void
     */
    public function test_user_can_logout()
    {
        Sanctum::actingAs(
            \App\Models\User::factory()->create(),
            ['*']
        );

        $response = $this->postJson(route('logout'));
        $response->assertStatus(200);
    }
}
