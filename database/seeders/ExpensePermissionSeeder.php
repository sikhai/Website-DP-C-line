<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;

class ExpensePermissionSeeder extends Seeder
{
    public function run()
    {
        // =========================
        // 1. Define permissions
        // =========================
        $permissions = [
            'view_expenses',
            'create_expenses',
            'approve_expenses',
            'pay_expenses',
            'reimburse_expenses',
        ];

        // =========================
        // 2. Create permissions
        // =========================
        foreach ($permissions as $key) {
            Permission::firstOrCreate([
                'key' => $key,
                'table_name' => 'expenses',
            ]);
        }

        // =========================
        // 3. Get roles
        // =========================
        $roles = [
            'admin' => Role::where('name', 'admin')->first(),
            'staff' => Role::where('name', 'staff')->first(),
            'manager' => Role::where('name', 'manager')->first(),
            'accountant' => Role::where('name', 'accountant')->first(),
        ];

        // =========================
        // 4. Assign permissions
        // =========================

        // Admin: all
        if ($roles['admin']) {
            $roles['admin']->permissions()->syncWithoutDetaching(
                Permission::whereIn('key', $permissions)->pluck('id')
            );
        }

        // Staff: create + view
        if ($roles['staff']) {
            $roles['staff']->permissions()->syncWithoutDetaching(
                Permission::whereIn('key', [
                    'view_expenses',
                    'create_expenses',
                ])->pluck('id')
            );
        }

        // Manager: approve
        if ($roles['manager']) {
            $roles['manager']->permissions()->syncWithoutDetaching(
                Permission::whereIn('key', [
                    'view_expenses',
                    'approve_expenses',
                ])->pluck('id')
            );
        }

        // Accountant: pay + reimburse
        if ($roles['accountant']) {
            $roles['accountant']->permissions()->syncWithoutDetaching(
                Permission::whereIn('key', [
                    'view_expenses',
                    'pay_expenses',
                    'reimburse_expenses',
                ])->pluck('id')
            );
        }
    }
}