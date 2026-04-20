<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOfficialLetterRequest;
use App\Http\Requests\SendOfficialLetterRequest;
use App\Http\Requests\ReceiveOfficialLetterRequest;
use App\Http\Requests\ApproveOfficialLetterRequest;
use App\Http\Resources\OfficialLetterResource;
use App\Models\OfficialLetter;
use App\Services\OfficialLetterService;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class OfficialLetterController extends Controller
{
    protected $officialLetterService;

    public function __construct(OfficialLetterService $officialLetterService)
    {
        $this->officialLetterService = $officialLetterService;
        $this->authorizeResource(OfficialLetter::class, 'official_letter');
    }

    /**
     * عرض الكتب الرسمية
     */
    public function index(Request $request)
    {
        ActivityLogger::log(
            'official_letter',
            'viewed_list',
            'Opened official letters page',
            null,
            [],
            $request->user()
        );

        $query = OfficialLetter::with([
            'trainingRequest',
            'sentBy',
            'receivedBy',
            'trainingSite'
        ]);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $letters = $query->latest()->paginate($request->per_page ?? 15);

        ActivityLogger::log(
            'official_letter',
            'viewed_data',
            'Fetched official letters list',
            null,
            ['count' => $letters->count()],
            $request->user()
        );

        return OfficialLetterResource::collection($letters);
    }

    /**
     * إنشاء كتاب رسمي
     */
    public function store(StoreOfficialLetterRequest $request)
    {
        $letter = $this->officialLetterService->createLetter(
            $request->validated(),
            $request->user()->id
        );

        ActivityLogger::log(
            'official_letter',
            'created',
            'Created official letter',
            $letter,
            [
                'letter_id' => $letter->id,
                'type' => $letter->type,
                'status' => $letter->status
            ],
            $request->user()
        );

        return new OfficialLetterResource($letter);
    }

    /**
     * عرض كتاب
     */
    public function show(OfficialLetter $officialLetter)
    {
        ActivityLogger::log(
            'official_letter',
            'viewed',
            'Viewed official letter',
            $officialLetter,
            ['letter_id' => $officialLetter->id],
            auth()->user()
        );

        return new OfficialLetterResource(
            $officialLetter->load([
                'trainingRequest',
                'sentBy',
                'receivedBy',
                'trainingSite'
            ])
        );
    }

    /**
     * إرسال كتاب
     */
    public function send(SendOfficialLetterRequest $request, OfficialLetter $officialLetter)
    {
        $this->authorize('send', $officialLetter);

        $oldStatus = $officialLetter->status;

        $letter = $this->officialLetterService->sendLetter(
            $officialLetter,
            $request->status
        );

        ActivityLogger::log(
            'official_letter',
            'sent',
            'Sent official letter',
            $letter,
            [
                'letter_id' => $letter->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status
            ],
            $request->user()
        );

        return new OfficialLetterResource($letter);
    }

    /**
     * استلام كتاب
     */
    public function receive(ReceiveOfficialLetterRequest $request, OfficialLetter $officialLetter)
    {
        $this->authorize('receive', $officialLetter);

        $letter = $this->officialLetterService->receiveLetter(
            $officialLetter,
            $request->user()->id
        );

        ActivityLogger::log(
            'official_letter',
            'received',
            'Received official letter',
            $letter,
            [
                'letter_id' => $letter->id,
                'received_by' => $request->user()->id
            ],
            $request->user()
        );

        return new OfficialLetterResource($letter);
    }

    /**
     * اعتماد / رفض كتاب
     */
    public function approve(ApproveOfficialLetterRequest $request, OfficialLetter $officialLetter)
    {
        $oldStatus = $officialLetter->status;

        $officialLetter->update([
            'status' => $request->status === 'approved'
                ? 'directorate_approved'
                : 'rejected',

            'rejection_reason' => $request->rejection_reason,
        ]);

        ActivityLogger::log(
            'official_letter',
            'approved',
            'Processed official letter approval',
            $officialLetter,
            [
                'letter_id' => $officialLetter->id,
                'old_status' => $oldStatus,
                'new_status' => $officialLetter->status,
                'rejection_reason' => $request->rejection_reason
            ],
            $request->user()
        );

        return new OfficialLetterResource($officialLetter);
    }
}