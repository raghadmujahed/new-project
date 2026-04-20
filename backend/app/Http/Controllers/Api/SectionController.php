<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Section::class, 'section');
    }

    public function index(Request $request)
    {
        $query = Section::with(['course', 'academicSupervisor']);
        if ($request->has('course_id')) $query->where('course_id', $request->course_id);
        if ($request->has('semester')) $query->where('semester', $request->semester);
        if ($request->has('academic_year')) $query->where('academic_year', $request->academic_year);
        
        $sections = $query->paginate($request->per_page ?? 15);
        return SectionResource::collection($sections);
    }

    public function store(StoreSectionRequest $request)
    {
        $section = Section::create($request->validated());
        return new SectionResource($section);
    }

    public function show(Section $section)
    {
        return new SectionResource($section->load(['course', 'academicSupervisor', 'enrollments.user']));
    }

    public function update(UpdateSectionRequest $request, Section $section)
    {
        $section->update($request->validated());
        return new SectionResource($section);
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return response()->json(['message' => 'تم حذف الشعبة']);
    }
}