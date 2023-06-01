<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Flush cache before seeding
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //Create permissions for loan
        Permission::create(['name' => 'view_loan_list']);
        Permission::create(['name' => 'create_loan']);
        Permission::create(['name' => 'approve_loan']);
        Permission::create(['name' => 'view_loan']);
        Permission::create(['name' => 'repay_loan']);

        //Create roles admin and assign all permissions for admin
        Role::create(['name' => 'admin'])->givePermissionTo(Permission::all());

        //Create roles customer and assign enough permissions for customer
        Role::create(['name' => 'customer'])
            ->givePermissionTo(['view_loan_list', 'create_loan', 'view_loan', 'repay_loan']);
    }
}
