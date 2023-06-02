<?php

namespace Tests;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use App\Models\User;

class BaseApiTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->registerPermissions();
        $this->seed();
    }

    //make sanctum login
    public function fakeLoginUser($role = 'customer')
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        Sanctum::actingAs($user);
        return $user;
    }
}
