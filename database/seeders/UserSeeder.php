<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::UpdateorCreate([
            'uuid' => Str::orderedUuid(),
            'name' => 'Test Customer 1',
            'email' => 'testcusomer1@test.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123456'),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole('customer');

        $user = User::UpdateorCreate([
            'uuid' => Str::orderedUuid(),
            'name' => 'Test Customer2',
            'email' => 'testcusomer1@g.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123456'),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole('customer');

        $user = User::UpdateorCreate([
            'uuid' => Str::orderedUuid(),
            'name' => 'Thanh Do',
            'email' => 'dobaothanh@@test.com',
            'email_verified_at' => now(),
            'password' => bcrypt('123456'),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole('admin');
    }
}
