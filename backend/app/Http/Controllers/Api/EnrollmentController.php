<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use App\Http\Resources\EnrollmentResource;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Enrollment::class, 'enrollment');
    }

    public function index(Request $request)
    {
        $query = Enrollment::with(['user', 'section.course']);

        if ($request->has('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->paginate($request->per_page ?? 15);

        ActivityLogger::log(
            'enrollment',
            'viewed_list',
            'Viewed enrollments list',
            null,
            ['filters' => $request->only(['section_id', 'user_id', 'status'])],
            $request->user()
        );

        return EnrollmentResource::collection($enrollments);
    }

    public function store(StoreEnrollmentRequest $request)
    {
        $enrollment = Enrollment::create($request->validated());

        ActivityLogger::log(
            'enrollment',
            'created',
            'Enrollment created',
            $enrollment,
            [
                'enrollment_id' => $enrollment->id,
                'user_id' => $enrollment->user_id,
                'section_id' => $enrollment->section_id
            ],
            $request->user()
        );

        return new EnrollmentResource($enrollment);
    }

    public function show(Enrollment $enrollment)
    {
        ActivityLogger::log(
            'enrollment',
            'viewed',
            'Viewed enrollment',
            $enrollment,
            ['enrollment_id' => $enrollment->id],
            auth()->user()
        );

        return new EnrollmentResource(
            $enrollment->load(['user', 'section.course'])
        );
    }

    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment)
    {
        $enrollment->update($request->validated());

        ActivityLogger::log(
            'enrollment',
            'updated',
            'Enrollment updated',
            $enrollment,
            [
                'enrollment_id' => $enrollment->id,
                'changes' => $request->validated()
            ],
            $request->user()
        );

        return new EnrollmentResource($enrollment);
    }

    public function destroy(Enrollment $enrollment)
    {
        $id = $enrollment->id;

        $enrollment->delete();

        ActivityLogger::log(
            'enrollment',
            'deleted',
            'Enrollment deleted',
            null,
            ['enrollment_id' => $id],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف التسجيل']);
    }
}