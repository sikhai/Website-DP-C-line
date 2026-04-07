<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;

class SamplePermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'view_samples',
            'create_samples',
            'update_samples',
            'delete_samples',
        ];

        $permissionIds = [];

        foreach ($permissions as $key) {
            $permission = Permission::firstOrCreate(
                ['key' => $key],
                ['table_name' => 'samples']
            );

            $permissionIds[] = $permission->id;
        }

        // Gán cho admin
        $adminRole = Role::where('name', 'admin')->first();

        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching($permissionIds);
        }
    }
}