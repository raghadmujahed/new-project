<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Section::class, 'section');
    }

    /**
     * عرض الشعب + تسجيل دخول الصفحة
     */
    public function index(Request $request)
    {
        ActivityLogger::log(
            'section',
            'viewed_list',
            'Viewed sections page',
            null,
            [],
            $request->user()
        );

        $query = Section::with(['course', 'academicSupervisor']);

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $sections = $query->paginate($request->per_page ?? 15);

        return SectionResource::collection($sections);
    }

    /**
     * إنشاء شعبة
     */
    public function store(StoreSectionRequest $request)
    {
        $section = Section::create($request->validated());

        ActivityLogger::log(
            'section',
            'created',
            'Created section',
            $section,
            [
                'section_id' => $section->id,
                'course_id' => $section->course_id,
            ],
            $request->user()
        );

        return new SectionResource($section);
    }

    /**
     * عرض شعبة واحدة
     */
    public function show(Section $section)
    {
        ActivityLogger::log(
            'section',
            'viewed',
            'Viewed section details',
            $section,
            ['section_id' => $section->id],
            auth()->user()
        );

        return new SectionResource(
            $section->load(['course', 'academicSupervisor', 'enrollments.user'])
        );
    }

    /**
     * تحديث شعبة
     */
    public function update(UpdateSectionRequest $request, Section $section)
    {
        $section->update($request->validated());

        ActivityLogger::log(
            'section',
            'updated',
            'Updated section',
            $section,
            [
                'section_id' => $section->id,
                'updated_fields' => $request->validated(),
            ],
            $request->user()
        );

        return new SectionResource($section);
    }

    /**
     * حذف شعبة
     */
    public function destroy(Section $section)
    {
        $id = $section->id;

        $section->delete();

        ActivityLogger::log(
            'section',
            'deleted',
            'Deleted section',
            null,
            ['section_id' => $id],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف الشعبة']);
    }
}