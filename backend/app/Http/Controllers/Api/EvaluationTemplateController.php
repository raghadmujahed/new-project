<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEvaluationTemplateRequest;
use App\Http\Requests\UpdateEvaluationTemplateRequest;
use App\Http\Requests\StoreEvaluationItemRequest;
use App\Http\Requests\UpdateEvaluationItemRequest;
use App\Http\Resources\EvaluationTemplateResource;
use App\Models\EvaluationTemplate;
use App\Models\EvaluationItem;
use Illuminate\Http\Request;
class EvaluationTemplateController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(EvaluationTemplate::class, 'evaluation_template');
    }

    public function store(StoreEvaluationTemplateRequest $request)
    {
        $data = $request->validated();
        if (!isset($data['department_id']) && auth()->user()->department_id) {
            $data['department_id'] = auth()->user()->department_id;
        }
        $evaluationTemplate = EvaluationTemplate::create($data);
        return new EvaluationTemplateResource($evaluationTemplate);
    }



    public function update(UpdateEvaluationTemplateRequest $request, EvaluationTemplate $evaluationTemplate)
    {
        $evaluationTemplate->update($request->validated());
        return new EvaluationTemplateResource($evaluationTemplate);
    }

    public function destroy(EvaluationTemplate $evaluationTemplate)
    {
        $evaluationTemplate->delete();
        return response()->json(['message' => 'تم حذف القالب']);
    }
    public function index(Request $request)
{
    $query = EvaluationTemplate::with('items'); // إضافة with('items')
    
    if (!auth()->user()->isAdmin()) {
        $query->where('department_id', auth()->user()->department_id);
    }
    
    if ($request->has('form_type')) {
        $query->where('form_type', $request->form_type);
    }
    
    $templates = $query->paginate($request->per_page ?? 15);
    return EvaluationTemplateResource::collection($templates);
}

public function show(EvaluationTemplate $evaluationTemplate)
{
    return new EvaluationTemplateResource($evaluationTemplate->load('items'));
}

    public function addItem(StoreEvaluationItemRequest $request, EvaluationTemplate $evaluationTemplate)
    {
        $item = $evaluationTemplate->items()->create($request->validated());
        return response()->json($item, 201);
    }

    public function updateItem(UpdateEvaluationItemRequest $request, EvaluationItem $item)
    {
        $item->update($request->validated());
        return response()->json($item);
    }

    public function deleteItem(EvaluationItem $item)
    {
        $item->delete();
        return response()->json(['message' => 'تم حذف البند']);
    }
    
}