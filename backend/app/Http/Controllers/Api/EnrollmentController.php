<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use App\Http\Resources\EnrollmentResource;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Enrollment::class, 'enrollment');
    }

    public function index(Request $request)
    {
        $query = Enrollment::with(['user', 'section.course']);
        if ($request->has('section_id')) $query->where('section_id', $request->section_id);
        if ($request->has('user_id')) $query->where('user_id', $request->user_id);
        if ($request->has('status')) $query->where('status', $request->status);
        
        $enrollments = $query->paginate($request->per_page ?? 15);
        return EnrollmentResource::collection($enrollments);
    }

    public function store(StoreEnrollmentRequest $request)
    {
        $enrollment = Enrollment::create($request->validated());
        return new EnrollmentResource($enrollment);
    }

    public function show(Enrollment $enrollment)
    {
        return new EnrollmentResource($enrollment->load(['user', 'section.course']));
    }

    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment)
    {
        $enrollment->update($request->validated());
        return new EnrollmentResource($enrollment);
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return response()->json(['message' => 'تم حذف التسجيل']);
    }
}