<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Department::class, 'department');
    }

    /**
     * عرض الأقسام
     */
    public function index(Request $request)
    {
        $departments = Department::paginate($request->per_page ?? 15);

        ActivityLogger::log(
            'department',
            'view_list',
            'Viewed departments list',
            null,
            [],
            $request->user()
        );

        return DepartmentResource::collection($departments);
    }

    /**
     * إنشاء قسم
     */
    public function store(StoreDepartmentRequest $request)
    {
        $department = Department::create($request->validated());

        ActivityLogger::log(
            'department',
            'created',
            'Department created',
            $department,
            [
                'department_id' => $department->id,
                'name' => $department->name
            ],
            $request->user()
        );

        return new DepartmentResource($department);
    }

    /**
     * عرض قسم واحد
     */
    public function show(Department $department)
    {
        ActivityLogger::log(
            'department',
            'view',
            'Viewed department',
            $department,
            ['department_id' => $department->id],
            auth()->user()
        );

        return new DepartmentResource($department);
    }

    /**
     * تحديث قسم
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $department->update($request->validated());

        ActivityLogger::log(
            'department',
            'updated',
            'Department updated',
            $department,
            [
                'department_id' => $department->id,
                'changes' => $request->validated()
            ],
            $request->user()
        );

        return new DepartmentResource($department);
    }

    /**
     * حذف قسم
     */
    public function destroy(Department $department)
    {
        $id = $department->id;
        $name = $department->name;

        $department->delete();

        ActivityLogger::log(
            'department',
            'deleted',
            'Department deleted',
            null,
            [
                'department_id' => $id,
                'name' => $name
            ],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف القسم']);
    }
}