<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingPeriodRequest;
use App\Http\Requests\UpdateTrainingPeriodRequest;
use App\Http\Resources\TrainingPeriodResource;
use App\Models\TrainingPeriod;
use Illuminate\Http\Request;

class TrainingPeriodController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TrainingPeriod::class, 'training_period');
    }

    public function index(Request $request)
    {
        $query = TrainingPeriod::query();
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        $periods = $query->latest()->paginate($request->per_page ?? 15);
        return TrainingPeriodResource::collection($periods);
    }

    public function store(StoreTrainingPeriodRequest $request)
    {
        $period = TrainingPeriod::create($request->validated());
        return new TrainingPeriodResource($period);
    }

    public function show(TrainingPeriod $trainingPeriod)
    {
        return new TrainingPeriodResource($trainingPeriod);
    }

    public function update(UpdateTrainingPeriodRequest $request, TrainingPeriod $trainingPeriod)
    {
        $trainingPeriod->update($request->validated());
        return new TrainingPeriodResource($trainingPeriod);
    }

    public function destroy(TrainingPeriod $trainingPeriod)
    {
        $trainingPeriod->delete();
        return response()->json(['message' => 'تم حذف الفترة التدريبية']);
    }

    public function setActive(TrainingPeriod $trainingPeriod)
    {
        TrainingPeriod::where('is_active', true)->update(['is_active' => false]);
        $trainingPeriod->update(['is_active' => true]);
        return new TrainingPeriodResource($trainingPeriod);
    }
}