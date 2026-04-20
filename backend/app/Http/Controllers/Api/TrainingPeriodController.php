<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingPeriodRequest;
use App\Http\Requests\UpdateTrainingPeriodRequest;
use App\Http\Resources\TrainingPeriodResource;
use App\Models\TrainingPeriod;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class TrainingPeriodController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TrainingPeriod::class, 'training_period');
    }

    // ================= INDEX =================
    public function index(Request $request)
    {
        ActivityLogger::log(
            'training_period',
            'viewed_list',
            'Viewed training periods page',
            null,
            [],
            $request->user()
        );

        $query = TrainingPeriod::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $periods = $query->latest()->paginate($request->per_page ?? 15);

        return TrainingPeriodResource::collection($periods);
    }

    // ================= STORE =================
    public function store(StoreTrainingPeriodRequest $request)
    {
        $period = TrainingPeriod::create($request->validated());

        ActivityLogger::log(
            'training_period',
            'created',
            'Created training period',
            $period,
            ['training_period_id' => $period->id],
            $request->user()
        );

        return new TrainingPeriodResource($period);
    }

    // ================= SHOW =================
    public function show(TrainingPeriod $trainingPeriod)
    {
        ActivityLogger::log(
            'training_period',
            'viewed',
            'Viewed training period',
            $trainingPeriod,
            ['training_period_id' => $trainingPeriod->id],
            auth()->user()
        );

        return new TrainingPeriodResource($trainingPeriod);
    }

    // ================= UPDATE =================
    public function update(UpdateTrainingPeriodRequest $request, TrainingPeriod $trainingPeriod)
    {
        $trainingPeriod->update($request->validated());

        ActivityLogger::log(
            'training_period',
            'updated',
            'Updated training period',
            $trainingPeriod,
            ['training_period_id' => $trainingPeriod->id],
            $request->user()
        );

        return new TrainingPeriodResource($trainingPeriod);
    }

    // ================= DELETE =================
    public function destroy(TrainingPeriod $trainingPeriod)
    {
        $id = $trainingPeriod->id;

        $trainingPeriod->delete();

        ActivityLogger::log(
            'training_period',
            'deleted',
            'Deleted training period',
            null,
            ['training_period_id' => $id],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف الفترة التدريبية']);
    }

    // ================= SET ACTIVE (IMPORTANT 🔥) =================
    public function setActive(TrainingPeriod $trainingPeriod)
    {
        TrainingPeriod::where('is_active', true)->update(['is_active' => false]);

        $trainingPeriod->update(['is_active' => true]);

        ActivityLogger::log(
            'training_period',
            'activated',
            'Set training period as active',
            $trainingPeriod,
            ['training_period_id' => $trainingPeriod->id],
            auth()->user()
        );

        return new TrainingPeriodResource($trainingPeriod);
    }
}