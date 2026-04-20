<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrainingRequest;
use App\Http\Requests\UpdateTrainingRequest;
use App\Http\Requests\SendTrainingRequestToDirectorateRequest;
use App\Http\Requests\DirectorateApproveTrainingRequest;
use App\Http\Requests\SendTrainingRequestToSchoolRequest;
use App\Http\Requests\SchoolApproveTrainingRequest;
use App\Http\Requests\RejectTrainingRequestRequest;
use App\Http\Resources\TrainingRequestResource;
use App\Models\TrainingRequest;
use App\Services\TrainingRequestService;
use Illuminate\Http\Request;
use App\Models\TrainingRequestStudent;
use App\Helpers\ActivityLogger;

class TrainingRequestController extends Controller
{
    protected $trainingRequestService;

    public function __construct(TrainingRequestService $trainingRequestService)
    {
        $this->trainingRequestService = $trainingRequestService;
        $this->authorizeResource(TrainingRequest::class, 'training_request');
    }

    // ================= INDEX =================
    public function index(Request $request)
    {
        ActivityLogger::log(
            'training_request',
            'viewed_list',
            'Visited Training Requests page',
            null,
            [],
            $request->user()
        );

        $query = TrainingRequest::with([
            'trainingSite',
            'trainingRequestStudents.user',
            'trainingRequestStudents.course'
        ]);

        if ($request->has('book_status')) {
            $query->where('book_status', $request->book_status);
        }

        if ($request->has('training_site_id')) {
            $query->where('training_site_id', $request->training_site_id);
        }

        $trainingRequests = $query->latest()->paginate($request->per_page ?? 15);

        return TrainingRequestResource::collection($trainingRequests);
    }

    // ================= STORE =================
    public function store(StoreTrainingRequest $request)
    {
        $trainingRequest = $this->trainingRequestService->createTrainingRequest(
            $request->validated(),
            $request->user()->id
        );

        ActivityLogger::log(
            'training_request',
            'created',
            'Created Training Request',
            $trainingRequest,
            ['training_request_id' => $trainingRequest->id],
            $request->user()
        );

        return new TrainingRequestResource($trainingRequest);
    }

    // ================= SHOW =================
    public function show(TrainingRequest $trainingRequest)
    {
        ActivityLogger::log(
            'training_request',
            'viewed',
            'Viewed Training Request',
            $trainingRequest,
            ['training_request_id' => $trainingRequest->id],
            auth()->user()
        );

        return new TrainingRequestResource(
            $trainingRequest->load([
                'trainingSite',
                'trainingRequestStudents.user',
                'trainingRequestStudents.course'
            ])
        );
    }

    // ================= UPDATE =================
    public function update(UpdateTrainingRequest $request, TrainingRequest $trainingRequest)
    {
        $trainingRequest->update($request->validated());

        ActivityLogger::log(
            'training_request',
            'updated',
            'Updated Training Request',
            $trainingRequest,
            ['training_request_id' => $trainingRequest->id],
            $request->user()
        );

        return new TrainingRequestResource($trainingRequest);
    }

    // ================= DELETE =================
    public function destroy(TrainingRequest $trainingRequest)
    {
        $id = $trainingRequest->id;

        $trainingRequest->delete();

        ActivityLogger::log(
            'training_request',
            'deleted',
            'Deleted Training Request',
            null,
            ['training_request_id' => $id],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف الكتاب بنجاح']);
    }

    // ================= SEND TO DIRECTORATE =================
    public function sendToDirectorate(SendTrainingRequestToDirectorateRequest $request, TrainingRequest $trainingRequest)
    {
        $this->authorize('sendToDirectorate', $trainingRequest);

        $this->trainingRequestService->sendToDirectorate(
            $trainingRequest,
            $request->user()->id,
            $request->validated()
        );

        ActivityLogger::log(
            'training_request',
            'sent_to_directorate',
            'Sent Training Request to Directorate',
            $trainingRequest,
            ['training_request_id' => $trainingRequest->id],
            $request->user()
        );

        return response()->json(['message' => 'تم إرسال الكتاب إلى المديرية بنجاح']);
    }

    // ================= DIRECTORATE APPROVE =================
    public function directorateApprove(DirectorateApproveTrainingRequest $request, TrainingRequest $trainingRequest)
    {
        $this->authorize('approveByDirectorate', $trainingRequest);

        if ($request->status === 'rejected') {
            $this->trainingRequestService->reject(
                $trainingRequest,
                $request->rejection_reason,
                $request->user()->id
            );

            ActivityLogger::log(
                'training_request',
                'rejected_by_directorate',
                'Directorate Rejected Training Request',
                $trainingRequest,
                ['training_request_id' => $trainingRequest->id],
                $request->user()
            );

            return response()->json(['message' => 'تم رفض الكتاب']);
        }

        $this->trainingRequestService->directorateApprove($trainingRequest, $request->user()->id);

        ActivityLogger::log(
            'training_request',
            'approved_by_directorate',
            'Directorate Approved Training Request',
            $trainingRequest,
            ['training_request_id' => $trainingRequest->id],
            $request->user()
        );

        return response()->json(['message' => 'تمت موافقة المديرية على الكتاب']);
    }

    // ================= SEND TO SCHOOL =================
    public function sendToSchool(SendTrainingRequestToSchoolRequest $request, TrainingRequest $trainingRequest)
    {
        $this->authorize('sendToSchool', $trainingRequest);

        $this->trainingRequestService->sendToSchool(
            $trainingRequest,
            $request->user()->id,
            $request->validated()
        );

        ActivityLogger::log(
            'training_request',
            'sent_to_school',
            'Sent Training Request to School',
            $trainingRequest,
            ['training_request_id' => $trainingRequest->id],
            $request->user()
        );

        return response()->json(['message' => 'تم إرسال الكتاب إلى المدرسة بنجاح']);
    }

    // ================= SCHOOL APPROVE =================
    public function schoolApprove(SchoolApproveTrainingRequest $request, TrainingRequest $trainingRequest)
    {
        $this->authorize('approveBySchool', $trainingRequest);

        if ($request->status === 'rejected') {
            $this->trainingRequestService->reject(
                $trainingRequest,
                $request->rejection_reason,
                $request->user()->id
            );

            ActivityLogger::log(
                'training_request',
                'rejected_by_school',
                'School Rejected Training Request',
                $trainingRequest,
                ['training_request_id' => $trainingRequest->id],
                $request->user()
            );

            return response()->json(['message' => 'تم رفض الكتاب من قبل المدرسة']);
        }

        $this->trainingRequestService->schoolApprove(
            $trainingRequest,
            $request->user()->id,
            $request->students
        );

        ActivityLogger::log(
            'training_request',
            'approved_by_school',
            'School Approved Training Request',
            $trainingRequest,
            ['training_request_id' => $trainingRequest->id],
            $request->user()
        );

        return response()->json(['message' => 'تمت موافقة المدرسة وتعيين المعلمين بنجاح']);
    }

    // ================= REJECT =================
    public function reject(RejectTrainingRequestRequest $request, TrainingRequest $trainingRequest)
    {
        $this->authorize('update', $trainingRequest);

        $this->trainingRequestService->reject(
            $trainingRequest,
            $request->rejection_reason,
            $request->user()->id
        );

        ActivityLogger::log(
            'training_request',
            'rejected',
            'Rejected Training Request',
            $trainingRequest,
            ['training_request_id' => $trainingRequest->id],
            $request->user()
        );

        return response()->json(['message' => 'تم رفض الكتاب']);
    }

    // ================= STUDENT INDEX =================
    public function studentIndex()
    {
        ActivityLogger::log(
            'training_request_student',
            'viewed_list',
            'Visited Student Training Requests page',
            null,
            [],
            auth()->user()
        );

        $user = auth()->user();

        $requests = TrainingRequestStudent::where('user_id', $user->id)
            ->with('trainingRequest.trainingSite')
            ->get();

        return response()->json(['data' => $requests]);
    }

    // ================= STUDENT STORE =================
    public function studentStore(Request $request)
    {
        $data = $request->validate([
            'training_site_id' => 'required|exists:training_sites,id',
            'notes' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $trainingRequest = TrainingRequest::create([
            'training_site_id' => $data['training_site_id'],
            'status' => 'pending',
            'book_status' => 'draft',
            'requested_at' => now(),
        ]);

        $studentRequest = TrainingRequestStudent::create([
            'training_request_id' => $trainingRequest->id,
            'user_id' => auth()->id(),
            'course_id' => $request->course_id,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'notes' => $data['notes'],
            'status' => 'pending',
        ]);

        ActivityLogger::log(
            'training_request_student',
            'created',
            'Student Created Training Request',
            $studentRequest,
            ['training_request_id' => $trainingRequest->id],
            auth()->user()
        );

        return response()->json(['data' => $studentRequest], 201);
    }
}