<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    public function index(Request $request)
    {
        ActivityLogger::log(
            'role',
            'view_list',
            'Viewed roles page',
            null,
            [],
            $request->user()
        );

        $roles = Role::with('permissions')
            ->paginate($request->per_page ?? 15);

        return RoleResource::collection($roles);
    }

    public function store(StoreRoleRequest $request)
    {
        $role = Role::create($request->validated());

        ActivityLogger::log(
            'role',
            'created',
            'Created role',
            $role,
            [
                'role_id' => $role->id,
                'role_name' => $role->name,
            ],
            $request->user()
        );

        return new RoleResource($role);
    }

    public function show($id, Request $request)
    {
        $role = Role::with('permissions')->findOrFail($id);

        ActivityLogger::log(
            'role',
            'view',
            'Viewed role details',
            $role,
            ['role_id' => $role->id],
            $request->user()
        );

        return response()->json($role);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->only('name'));

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        ActivityLogger::log(
            'role',
            'updated',
            'Updated role',
            $role,
            [
                'role_id' => $role->id,
                'updated_fields' => $request->only('name'),
            ],
            $request->user()
        );

        return new RoleResource($role->load('permissions'));
    }

    public function destroy(Role $role, Request $request)
    {
        $id = $role->id;

        $role->delete();

        ActivityLogger::log(
            'role',
            'deleted',
            'Deleted role',
            null,
            ['role_id' => $id],
            $request->user()
        );

        return response()->json(['message' => 'تم حذف الدور']);
    }
}