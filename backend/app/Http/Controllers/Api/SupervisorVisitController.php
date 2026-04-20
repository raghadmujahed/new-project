<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupervisorVisitRequest;
use App\Http\Requests\UpdateSupervisorVisitRequest;
use App\Http\Requests\CompleteSupervisorVisitRequest;
use App\Http\Resources\SupervisorVisitResource;
use App\Models\SupervisorVisit;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class SupervisorVisitController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(SupervisorVisit::class, 'supervisor_visit');
    }

    /**
     * عرض الزيارات + تسجيل دخول الصفحة
     */
    public function index(Request $request)
    {
        ActivityLogger::log(
            'supervisor_visit',
            'viewed_list',
            'Viewed supervisor visits page',
            null,
            [],
            $request->user()
        );

        $query = SupervisorVisit::with([
            'trainingAssignment.enrollment.user',
            'supervisor'
        ]);

        if ($request->has('training_assignment_id')) {
            $query->where('training_assignment_id', $request->training_assignment_id);
        }

        if ($request->has('supervisor_id')) {
            $query->where('supervisor_id', $request->supervisor_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $visits = $query->latest()->paginate($request->per_page ?? 15);

        return SupervisorVisitResource::collection($visits);
    }

    /**
     * إنشاء زيارة
     */
    public function store(StoreSupervisorVisitRequest $request)
    {
        $visit = SupervisorVisit::create([
            'training_assignment_id' => $request->training_assignment_id,
            'supervisor_id' => $request->user()->id,
            'scheduled_date' => $request->scheduled_date,
            'notes' => $request->notes,
            'status' => 'scheduled',
        ]);

        ActivityLogger::log(
            'supervisor_visit',
            'created',
            'Created supervisor visit',
            $visit,
            [
                'visit_id' => $visit->id,
                'training_assignment_id' => $visit->training_assignment_id,
            ],
            $request->user()
        );

        return new SupervisorVisitResource($visit);
    }

    /**
     * عرض زيارة واحدة
     */
    public function show(SupervisorVisit $supervisorVisit)
    {
        ActivityLogger::log(
            'supervisor_visit',
            'viewed',
            'Viewed supervisor visit',
            $supervisorVisit,
            ['visit_id' => $supervisorVisit->id],
            auth()->user()
        );

        return new SupervisorVisitResource(
            $supervisorVisit->load(['trainingAssignment', 'supervisor'])
        );
    }

    /**
     * تحديث زيارة
     */
    public function update(UpdateSupervisorVisitRequest $request, SupervisorVisit $supervisorVisit)
    {
        $supervisorVisit->update($request->validated());

        ActivityLogger::log(
            'supervisor_visit',
            'updated',
            'Updated supervisor visit',
            $supervisorVisit,
            ['visit_id' => $supervisorVisit->id],
            $request->user()
        );

        return new SupervisorVisitResource($supervisorVisit);
    }

    /**
     * إكمال زيارة
     */
    public function complete(CompleteSupervisorVisitRequest $request, SupervisorVisit $supervisorVisit)
    {
        $supervisorVisit->update([
            'notes' => $request->notes,
            'rating' => $request->rating,
            'status' => 'completed',
            'visit_date' => now(),
        ]);

        ActivityLogger::log(
            'supervisor_visit',
            'completed',
            'Completed supervisor visit',
            $supervisorVisit,
            [
                'visit_id' => $supervisorVisit->id,
                'rating' => $request->rating,
            ],
            $request->user()
        );

        return new SupervisorVisitResource($supervisorVisit);
    }

    /**
     * حذف زيارة
     */
    public function destroy(SupervisorVisit $supervisorVisit)
    {
        $id = $supervisorVisit->id;

        $supervisorVisit->delete();

        ActivityLogger::log(
            'supervisor_visit',
            'deleted',
            'Deleted supervisor visit',
            null,
            ['visit_id' => $id],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف الزيارة']);
    }
}