<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arrayOfPermissions = [
          'show current user',
          'edit settings',
          'edit contacts'  
        ];

        $permissions = collect($arrayOfPermissions)->map(function ($permission) {
            return [
                'name' => $permission,
                'guard_name' => 'api'
            ];
        });

        Permission::insert($permissions->toArray());
        $superAdminRole = Role::create(['name' => 'super admin','guard_name' => 'api'])
        ->givePermissionTo($arrayOfPermissions);
        $adminRole = Role::create(['name' => 'admin','guard_name' => 'api']);
        $userRole = Role::create(['name' => 'user','guard_name' => 'api']);
    }
}
