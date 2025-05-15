<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionsByRole = [
            'superadmin' => [
                'access admin dashboard',
                'access employer dashboard',
                'access all candidates',
            ],
            'employer' => [
                'access employer dashboard',
                'access own company',
                'create own company',
                'edit own company',
                'delete own company',
            ],
            'job seeker' => [
                'build own resume',
                'apply job',
                'access tags list',
                'create tags',
                'access categories list',
            ]
        ];

        $insertPermissions = fn ($role) => collect($permissionsByRole[$role])
            ->map(fn ($name) => Permission::firstOrCreate(['name' => $name], ['guard_name' => 'web']));

        $permissionIdsByRole = [
            'superadmin' => $insertPermissions('superadmin'),
            'employer' => $insertPermissions('employer'),
            'job seeker' => $insertPermissions('job seeker')
        ];

        $count = 1;
        foreach ($permissionIdsByRole as $role => $permissionIds) {
            Role::unguard();
            $role = Role::updateOrCreate(['id' => $count], ['name' => $role]);
            $count++;
            Role::reguard();

            $permissionIdsArray = $permissionIds
                ->filter(fn ($permission) => !$role->hasPermissionTo($permission->name))
                ->map(fn ($permission) => $permission->id)
                ->toArray();

            DB::table('role_has_permissions')
                ->insert(
                    collect($permissionIdsArray)->map(fn ($id) => [
                        'role_id' => $role->id,
                        'permission_id' => $id
                    ])->toArray()
                );
        }
    }
}
