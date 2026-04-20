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
use App\Helpers\ActivityLogger;

class EvaluationTemplateController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(EvaluationTemplate::class, 'evaluation_template');
    }

    public function index(Request $request)
    {
        $query = EvaluationTemplate::with('items');

        if (!auth()->user()->isAdmin()) {
            $query->where('department_id', auth()->user()->department_id);
        }

        if ($request->has('form_type')) {
            $query->where('form_type', $request->form_type);
        }

        $templates = $query->paginate($request->per_page ?? 15);

        ActivityLogger::log(
            'evaluation_template',
            'view_list',
            'Viewed evaluation templates list',
            null,
            [],
            $request->user()
        );

        return EvaluationTemplateResource::collection($templates);
    }

    public function store(StoreEvaluationTemplateRequest $request)
    {
        $data = $request->validated();

        if (!isset($data['department_id']) && auth()->user()->department_id) {
            $data['department_id'] = auth()->user()->department_id;
        }

        $template = EvaluationTemplate::create($data);

        ActivityLogger::log(
            'evaluation_template',
            'created',
            'Evaluation template created',
            $template,
            [
                'template_id' => $template->id,
                'form_type' => $template->form_type ?? null
            ],
            $request->user()
        );

        return new EvaluationTemplateResource($template);
    }

    public function show(EvaluationTemplate $evaluationTemplate)
    {
        ActivityLogger::log(
            'evaluation_template',
            'view',
            'Viewed evaluation template',
            $evaluationTemplate,
            ['template_id' => $evaluationTemplate->id],
            auth()->user()
        );

        return new EvaluationTemplateResource(
            $evaluationTemplate->load('items')
        );
    }

    public function update(UpdateEvaluationTemplateRequest $request, EvaluationTemplate $evaluationTemplate)
    {
        $evaluationTemplate->update($request->validated());

        ActivityLogger::log(
            'evaluation_template',
            'updated',
            'Evaluation template updated',
            $evaluationTemplate,
            [
                'template_id' => $evaluationTemplate->id,
                'changes' => $request->validated()
            ],
            $request->user()
        );

        return new EvaluationTemplateResource($evaluationTemplate);
    }

    public function destroy(EvaluationTemplate $evaluationTemplate)
    {
        $id = $evaluationTemplate->id;

        $evaluationTemplate->delete();

        ActivityLogger::log(
            'evaluation_template',
            'deleted',
            'Evaluation template deleted',
            null,
            ['template_id' => $id],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف القالب']);
    }

    // ITEMS MANAGEMENT

    public function addItem(StoreEvaluationItemRequest $request, EvaluationTemplate $evaluationTemplate)
    {
        $item = $evaluationTemplate->items()->create($request->validated());

        ActivityLogger::log(
            'evaluation_item',
            'created',
            'Evaluation item added',
            $item,
            [
                'template_id' => $evaluationTemplate->id,
                'item_id' => $item->id
            ],
            $request->user()
        );

        return response()->json($item, 201);
    }

    public function updateItem(UpdateEvaluationItemRequest $request, EvaluationItem $item)
    {
        $item->update($request->validated());

        ActivityLogger::log(
            'evaluation_item',
            'updated',
            'Evaluation item updated',
            $item,
            [
                'item_id' => $item->id,
                'changes' => $request->validated()
            ],
            $request->user()
        );

        return response()->json($item);
    }

    public function deleteItem(EvaluationItem $item)
    {
        $id = $item->id;

        $item->delete();

        ActivityLogger::log(
            'evaluation_item',
            'deleted',
            'Evaluation item deleted',
            null,
            ['item_id' => $id],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف البند']);
    }
}