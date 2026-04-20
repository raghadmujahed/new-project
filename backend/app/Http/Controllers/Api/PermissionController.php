<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Permission::class, 'permission');
    }

    /**
     * عرض الصلاحيات
     */
    public function index(Request $request)
    {
        ActivityLogger::log(
            'permission',
            'viewed_list',
            'Opened permissions page',
            null,
            [],
            $request->user()
        );

        $permissions = Permission::paginate($request->per_page ?? 15);

        ActivityLogger::log(
            'permission',
            'viewed_data',
            'Fetched permissions list',
            null,
            ['count' => $permissions->count()],
            $request->user()
        );

        return PermissionResource::collection($permissions);
    }

    /**
     * إنشاء صلاحية جديدة
     */
    public function store(StorePermissionRequest $request)
    {
        $permission = Permission::create($request->validated());

        ActivityLogger::log(
            'permission',
            'created',
            'Created permission',
            $permission,
            [
                'permission_id' => $permission->id,
                'name' => $permission->name ?? null
            ],
            $request->user()
        );

        return new PermissionResource($permission);
    }

    /**
     * عرض صلاحية واحدة
     */
    public function show(Permission $permission)
    {
        ActivityLogger::log(
            'permission',
            'viewed',
            'Viewed permission',
            $permission,
            ['permission_id' => $permission->id],
            auth()->user()
        );

        return new PermissionResource($permission);
    }

    /**
     * حذف صلاحية
     */
    public function destroy(Permission $permission)
    {
        $permissionId = $permission->id;
        $permissionName = $permission->name ?? null;

        $permission->delete();

        ActivityLogger::log(
            'permission',
            'deleted',
            'Deleted permission',
            null,
            [
                'permission_id' => $permissionId,
                'name' => $permissionName
            ],
            auth()->user()
        );

        return response()->json([
            'message' => 'تم حذف الصلاحية'
        ]);
    }
}