<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingSiteRequest;
use App\Http\Requests\UpdateTrainingSiteRequest;
use App\Http\Resources\TrainingSiteResource;
use App\Models\TrainingSite;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class TrainingSiteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TrainingSite::class, 'training_site');
    }

    // ================= INDEX =================
    public function index(Request $request)
    {
        ActivityLogger::log(
            'training_site',
            'viewed_list',
            'Visited Training Sites page',
            null,
            [],
            $request->user()
        );

        $query = TrainingSite::query();

        if ($request->has('site_type')) {
            $query->where('site_type', $request->site_type);
        }

        if ($request->has('governing_body')) {
            $query->where('governing_body', $request->governing_body);
        }

        if ($request->has('directorate')) {
            $query->where('directorate', $request->directorate);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $sites = $query->latest()->paginate($request->per_page ?? 15);

        return TrainingSiteResource::collection($sites);
    }

    // ================= STORE =================
    public function store(StoreTrainingSiteRequest $request)
    {
        $site = TrainingSite::create($request->validated());

        ActivityLogger::log(
            'training_site',
            'created',
            'Created Training Site',
            $site,
            ['training_site_id' => $site->id],
            $request->user()
        );

        return new TrainingSiteResource($site);
    }

    // ================= SHOW =================
    public function show(TrainingSite $trainingSite)
    {
        ActivityLogger::log(
            'training_site',
            'viewed',
            'Viewed Training Site',
            $trainingSite,
            ['training_site_id' => $trainingSite->id],
            auth()->user()
        );

        return new TrainingSiteResource($trainingSite);
    }

    // ================= UPDATE =================
    public function update(UpdateTrainingSiteRequest $request, TrainingSite $trainingSite)
    {
        $trainingSite->update($request->validated());

        ActivityLogger::log(
            'training_site',
            'updated',
            'Updated Training Site',
            $trainingSite,
            ['training_site_id' => $trainingSite->id],
            $request->user()
        );

        return new TrainingSiteResource($trainingSite);
    }

    // ================= DELETE =================
    public function destroy(TrainingSite $trainingSite)
    {
        $id = $trainingSite->id;

        $trainingSite->delete();

        ActivityLogger::log(
            'training_site',
            'deleted',
            'Deleted Training Site',
            null,
            ['training_site_id' => $id],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف موقع التدريب']);
    }
}