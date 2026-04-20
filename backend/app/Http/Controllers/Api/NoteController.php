<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Note::class, 'note');
    }

    public function index(Request $request)
    {
        $query = Note::with(['user', 'trainingAssignment']);
        if ($request->has('user_id')) $query->where('user_id', $request->user_id);
        if ($request->has('training_assignment_id')) $query->where('training_assignment_id', $request->training_assignment_id);
        
        $notes = $query->latest()->paginate($request->per_page ?? 15);
        return NoteResource::collection($notes);
    }

    public function store(StoreNoteRequest $request)
    {
        $note = Note::create($request->validated());
        return new NoteResource($note);
    }

    public function show(Note $note)
    {
        return new NoteResource($note);
    }

    public function update(UpdateNoteRequest $request, Note $note)
    {
        $note->update($request->validated());
        return new NoteResource($note);
    }

    public function destroy(Note $note)
    {
        $note->delete();
        return response()->json(['message' => 'تم حذف الملاحظة']);
    }
}