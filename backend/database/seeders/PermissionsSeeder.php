<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guardName = 'api';

        // create permissions
        $permissions = [
            'create products',
            'view products',
            'edit products',
            'delete products',
            'create orders',
            'view orders',
            'edit orders',
            'delete orders',
            'create customers',
            'view customers',
            'edit customers',
            'delete customers',
            'create analytics',
            'view analytics',
            'edit analytics',
            'delete analytics',
            'create users',
            'view users',
            'edit users',
            'delete users',
            'create roles-permission',
            'view roles-permission',
            'edit roles-permission',
            'delete roles-permission',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => $guardName]);
        }

        // Access to own inbox, own customers, own products
        // $role1 = Role::create(['name' => 'vendor', 'guard_name' => $guardName]);
        // $role1->givePermissionTo(['create products', 'view products', 'edit own products', 'delete own products', 'publish own products', 'unpublish own products']);

        // Access to inbox, customers, products
        $role1 = Role::create(['name' => 'Support', 'desc' => 'Support staff', 'guard_name' => $guardName]);
        $role1->givePermissionTo(['view products', 'view customers', 'view orders']);

        // Manage products and stock
        $role2 = Role::create(['name' => 'Inventory Staff', 'desc' => 'Inventory staff', 'guard_name' => $guardName]);
        $role2->givePermissionTo(['create products', 'view products', 'edit products', 'delete products']);

        // Can manage shop items and orders
        $role3 = Role::create(['name' => User::ROLE_ADMIN, 'desc' => 'System administrator', 'guard_name' => $guardName]);
        $role3->givePermissionTo(['create products', 'view products', 'edit products', 'delete products', 'create orders', 'view orders', 'edit orders', 'delete orders']);

        // Full system access
        $role4 = Role::create(['name' => User::ROLE_SUPER_ADMIN, 'desc' => 'Super administrator', 'guard_name' => $guardName]);
        $role4->givePermissionTo(Permission::all());
        // Gate::before in AppServiceProvider also grants this role every ability.

        // Support
        $user = User::factory()->create([
            'name' => 'Jules Conn',
            'email' => 'jules.conn@bentadoor.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Password@1234'),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole($role1);

        // Inventory Staff
        $user = User::factory()->create([
            'name' => 'Bartell Toni',
            'email' => 'bartell.toni@bentadoor.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Password@1234'),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole($role2);

        // Admin/Manager
        $user = User::factory()->create([
            'name' => 'Bryce Douglas',
            'email' => 'bryce.douglas@bentadoor.org',
            'email_verified_at' => now(),
            'password' => Hash::make('Password@1234'),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole($role3);

        // Super Admin
        $user = User::factory()->create([
            'name' => 'Cyrus Manatad',
            'email' => 'cyrusmanatad@bentadoor.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Password@1234'),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole($role4);
    }
}
