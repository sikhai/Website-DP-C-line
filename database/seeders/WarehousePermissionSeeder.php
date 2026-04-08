<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;

class WarehousePermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Warehouses
            ['key' => 'view_warehouses', 'table_name' => 'warehouses'],

            // Imports
            ['key' => 'view_warehouse_imports', 'table_name' => 'warehouse_imports'],
            ['key' => 'create_warehouse_imports', 'table_name' => 'warehouse_imports'],
            ['key' => 'update_warehouse_imports', 'table_name' => 'warehouse_imports'],
            ['key' => 'delete_warehouse_imports', 'table_name' => 'warehouse_imports'],

            // Exports
            ['key' => 'view_warehouse_exports', 'table_name' => 'warehouse_exports'],
            ['key' => 'create_warehouse_exports', 'table_name' => 'warehouse_exports'],
            ['key' => 'update_warehouse_exports', 'table_name' => 'warehouse_exports'],
            ['key' => 'delete_warehouse_exports', 'table_name' => 'warehouse_exports'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['key' => $perm['key']],
                ['table_name' => $perm['table_name']]
            );
        }

        // Gán cho admin
        $admin = Role::where('name', 'admin')->first();

        if ($admin) {
            $admin->permissions()->syncWithoutDetaching(
                Permission::whereIn('key', array_column($permissions, 'key'))
                    ->pluck('id')
                    ->toArray()
            );
        }
    }
}