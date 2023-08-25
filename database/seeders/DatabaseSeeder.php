<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        User::Create([
            'name' => 'admin',
            'email' => 'admin@iotech.co.id',
            'password' => Hash::make('adminiot'),
        ]);

        Permission::create(['name' => 'create-drivers']);
        Permission::create(['name' => 'edit-drivers']);
        Permission::create(['name' => 'delete-drivers']);
        Permission::create(['name' => 'create-routes']);
        Permission::create(['name' => 'edit-routes']);
        Permission::create(['name' => 'delete-routes']);
        Permission::create(['name' => 'create-shifts']);
        Permission::create(['name' => 'edit-shifts']);
        Permission::create(['name' => 'delete-shifts']);
        $adminRole = Role::create(['name' => 'Admin']);

        $adminRole->givePermissionTo([
            'create-drivers',
            'edit-drivers',
            'delete-drivers',
            'create-routes',
            'edit-routes',
            'delete-routes',
            'create-shifts',
            'edit-shifts',
            'delete-shifts',
        ]);
        $user = User::where('email', 'admin@iotech.co.id')->first();
        $user->assignRole('Admin');
    }
}
