<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class NoteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Note::class, 'note');
    }

    public function index(Request $request)
    {
        ActivityLogger::log(
            'note',
            'view_list',
            'Opened notes page',
            null,
            [],
            $request->user()
        );

        $query = Note::with(['user', 'trainingAssignment']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('training_assignment_id')) {
            $query->where('training_assignment_id', $request->training_assignment_id);
        }

        $notes = $query->latest()->paginate($request->per_page ?? 15);

        ActivityLogger::log(
            'note',
            'view_data',
            'Fetched notes list',
            null,
            ['count' => $notes->count()],
            $request->user()
        );

        return NoteResource::collection($notes);
    }

    public function store(StoreNoteRequest $request)
    {
        $note = Note::create($request->validated());

        ActivityLogger::log(
            'note',
            'created',
            'Created note',
            $note,
            [
                'note_id' => $note->id,
                'user_id' => $note->user_id,
                'training_assignment_id' => $note->training_assignment_id
            ],
            $request->user()
        );

        return new NoteResource($note);
    }

    public function show(Note $note)
    {
        ActivityLogger::log(
            'note',
            'view',
            'Viewed note',
            $note,
            ['note_id' => $note->id],
            auth()->user()
        );

        return new NoteResource($note);
    }

    public function update(UpdateNoteRequest $request, Note $note)
    {
        $note->update($request->validated());

        ActivityLogger::log(
            'note',
            'updated',
            'Updated note',
            $note,
            ['note_id' => $note->id],
            $request->user()
        );

        return new NoteResource($note);
    }

    public function destroy(Note $note)
    {
        $noteId = $note->id;

        $note->delete();

        ActivityLogger::log(
            'note',
            'deleted',
            'Deleted note',
            null,
            ['note_id' => $noteId],
            auth()->user()
        );

        return response()->json(['message' => 'تم حذف الملاحظة']);
    }
}