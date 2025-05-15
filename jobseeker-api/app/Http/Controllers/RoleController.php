<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = request()->query();
        $limit = $query['limit'] ?? 0;

        $query = Role::query()->withCount(['permissions', 'users']);
        $result = $query->get();
        if ($limit > 0) {
            $result = $query->paginate($limit);
        }

        return [
            'data' => $result
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();
        $role = new Role();
        $role->name = $validated['name'];
        $role->guard_name = 'web';
        $role->save();

        $role->syncPermissions($validated['permissions']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load(['permissions']);
        return [
            'data' => $role
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $validated = $request->validated();
        $role->name = $validated['name'];
        $role->save();

        $role->syncPermissions($validated['permissions']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (Roles::isImportantRole($role->id)) {
            abort(403);
            return;
        }
        $newRole = Role::findById(Roles::JOB_SEEKER, 'web');

        $role->load('users');
        $newRole->users()->attach($role->users);
        $role->users()->update(['users.role_id' => Roles::JOB_SEEKER]);

        $role->delete();
    }
}
