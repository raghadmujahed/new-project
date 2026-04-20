<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTrainingAssignmentRequest;
use App\Http\Resources\TrainingAssignmentResource;
use App\Models\TrainingAssignment;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class TrainingAssignmentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TrainingAssignment::class, 'training_assignment');
    }

    /**
     * INDEX
     */
    public function index(Request $request)
    {
        ActivityLogger::log(
            'training_assignment',
            'viewed_list',
            'Viewed training assignments list',
            null,
            [],
            $request->user()
        );

        $query = TrainingAssignment::with([
            'enrollment.user',
            'trainingSite',
            'teacher',
            'academicSupervisor'
        ]);

        if ($request->user()->role?->name === 'student') {
            $query->whereHas('enrollment', fn($q) =>
                $q->where('user_id', $request->user()->id)
            );
        } elseif ($request->user()->role?->name === 'teacher') {
            $query->where('teacher_id', $request->user()->id);
        } elseif ($request->user()->role?->name === 'academic_supervisor') {
            $query->where('academic_supervisor_id', $request->user()->id);
        }

        $assignments = $query->latest()->paginate($request->per_page ?? 15);

        return TrainingAssignmentResource::collection($assignments);
    }

    /**
     * SHOW
     */
    public function show(TrainingAssignment $trainingAssignment)
    {
        ActivityLogger::log(
            'training_assignment',
            'viewed',
            'Viewed training assignment',
            $trainingAssignment,
            ['training_assignment_id' => $trainingAssignment->id],
            auth()->user()
        );

        return new TrainingAssignmentResource(
            $trainingAssignment->load([
                'enrollment.user',
                'trainingSite',
                'teacher',
                'academicSupervisor',
                'trainingLogs',
                'attendances'
            ])
        );
    }

    /**
     * UPDATE
     */
    public function update(UpdateTrainingAssignmentRequest $request, TrainingAssignment $trainingAssignment)
    {
        $trainingAssignment->update($request->validated());

        ActivityLogger::log(
            'training_assignment',
            'updated',
            'Training assignment updated',
            $trainingAssignment,
            [
                'training_assignment_id' => $trainingAssignment->id
            ],
            $request->user()
        );

        return new TrainingAssignmentResource($trainingAssignment);
    }
}