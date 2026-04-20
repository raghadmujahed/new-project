<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Course::class, 'course');
    }

    public function index(Request $request)
    {
        $query = Course::with(['sections', 'department']); // 🔥 إضافة 'department'

        // فلتر حسب النوع
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // 🔥 فلتر حسب القسم (جديد)
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // فلتر البحث
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                    ->orWhere('name', 'like', '%' . $request->search . '%');
            });
        }

        $courses = $query->paginate($request->per_page ?? 15);

        ActivityLogger::log(
            'course',
            'view_list',
            'تم عرض قائمة المساقات',
            null,
            ['filters' => $request->only(['type', 'search', 'per_page', 'department_id'])], // 🔥 إضافة department_id للـ log
            $request->user()
        );

        return CourseResource::collection($courses);
    }

    public function store(StoreCourseRequest $request)
    {
        $course = Course::create($request->validated());

        ActivityLogger::log(
            'course',
            'created',
            'تم إنشاء مساق جديد',
            $course,
            [
                'code' => $course->code,
                'name' => $course->name,
                'department_id' => $course->department_id, // 🔥 إضافة
            ],
            $request->user()
        );

        return new CourseResource($course);
    }

    public function show(Course $course)
    {
        ActivityLogger::log(
            'course',
            'view',
            'تم عرض تفاصيل المساق',
            $course,
            [],
            auth()->user()
        );

        return new CourseResource($course->load(['sections', 'department'])); // 🔥 إضافة 'department'
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $old = $course->getOriginal();
        $course->update($request->validated());

        ActivityLogger::log(
            'course',
            'updated',
            'تم تحديث المساق',
            $course,
            ['old' => $old, 'new' => $course->toArray()],
            $request->user()
        );

        return new CourseResource($course);
    }

    public function destroy(Course $course)
    {
        $id = $course->id;
        $name = $course->name;
        $course->delete();

        ActivityLogger::log(
            'course',
            'deleted',
            'تم حذف المساق',
            null,
            ['course_id' => $id, 'name' => $name],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف المساق']);
    }
}