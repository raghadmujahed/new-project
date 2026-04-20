<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEvaluationRequest;
use App\Http\Requests\UpdateEvaluationRequest;
use App\Http\Resources\EvaluationResource;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class EvaluationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Evaluation::class, 'evaluation');
    }

    public function index(Request $request)
    {
        $query = Evaluation::with(['template', 'evaluator', 'scores.item']);

        if ($request->has('training_assignment_id')) {
            $query->where('training_assignment_id', $request->training_assignment_id);
        }

        $evaluations = $query->paginate($request->per_page ?? 15);

        ActivityLogger::log(
            'evaluation',
            'view_list',
            'Viewed evaluations list',
            null,
            [],
            $request->user()
        );

        return EvaluationResource::collection($evaluations);
    }

    public function store(StoreEvaluationRequest $request)
    {
        $evaluation = Evaluation::create($request->validated());

        ActivityLogger::log(
            'evaluation',
            'created',
            'Evaluation created',
            $evaluation,
            [
                'evaluation_id' => $evaluation->id,
                'training_assignment_id' => $evaluation->training_assignment_id ?? null
            ],
            $request->user()
        );

        return new EvaluationResource($evaluation);
    }

    public function show(Evaluation $evaluation)
    {
        ActivityLogger::log(
            'evaluation',
            'view',
            'Viewed evaluation',
            $evaluation,
            ['evaluation_id' => $evaluation->id],
            auth()->user()
        );

        return new EvaluationResource(
            $evaluation->load(['template.items', 'scores.item', 'evaluator'])
        );
    }

    public function update(UpdateEvaluationRequest $request, Evaluation $evaluation)
    {
        $evaluation->update($request->validated());

        ActivityLogger::log(
            'evaluation',
            'updated',
            'Evaluation updated',
            $evaluation,
            [
                'evaluation_id' => $evaluation->id,
                'changes' => $request->validated()
            ],
            $request->user()
        );

        return new EvaluationResource($evaluation);
    }

    public function destroy(Evaluation $evaluation)
    {
        $id = $evaluation->id;

        $evaluation->delete();

        ActivityLogger::log(
            'evaluation',
            'deleted',
            'Evaluation deleted',
            null,
            ['evaluation_id' => $id],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف التقييم']);
    }

    public function approve(Evaluation $evaluation)
    {
        $evaluation->update(['approved_at' => now()]);

        ActivityLogger::log(
            'evaluation',
            'approved',
            'Evaluation approved',
            $evaluation,
            [
                'evaluation_id' => $evaluation->id,
                'approved_at' => now()
            ],
            auth()->user()
        );

        return response()->json(['message' => 'تم اعتماد التقييم']);
    }
}