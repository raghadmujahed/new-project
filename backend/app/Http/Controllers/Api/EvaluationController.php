<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEvaluationRequest;
use App\Http\Requests\UpdateEvaluationRequest;
use App\Http\Resources\EvaluationResource;
use App\Models\Evaluation;
use App\Models\TrainingAssignment;
use Illuminate\Http\Request;

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
        return EvaluationResource::collection($evaluations);
    }

    public function store(StoreEvaluationRequest $request)
    {
        $data = $request->validated();
        $evaluation = Evaluation::create($data);
        return new EvaluationResource($evaluation);
    }

    public function show(Evaluation $evaluation)
    {
        return new EvaluationResource($evaluation->load(['template.items', 'scores.item', 'evaluator']));
    }

    public function update(UpdateEvaluationRequest $request, Evaluation $evaluation)
    {
        $this->authorize('update', $evaluation);
        $evaluation->update($request->validated());
        return new EvaluationResource($evaluation);
    }

    public function destroy(Evaluation $evaluation)
    {
        $this->authorize('delete', $evaluation);
        $evaluation->delete();
        return response()->json(['message' => 'تم حذف التقييم']);
    }

    public function approve(Evaluation $evaluation)
    {
        $this->authorize('approve', $evaluation);
        $evaluation->update(['approved_at' => now()]);
        return response()->json(['message' => 'تم اعتماد التقييم']);
    }
}