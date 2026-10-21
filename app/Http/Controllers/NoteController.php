<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query  = Note::forUser($userId);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $notes = $query->orderByDesc('is_pinned')
                       ->orderByDesc('updated_at')
                       ->get();

        return view('notes.index', compact('notes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'color'   => 'nullable|string|max:10',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['color']   = $validated['color'] ?? '#7c3aed';
        Note::create($validated);

        return redirect()->route('notes.index')->with('success', 'Note created!');
    }

    public function update(Request $request, Note $note)
    {
        abort_if($note->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'color'   => 'required|string|max:10',
        ]);

        $note->update($validated);

        return redirect()->route('notes.index')->with('success', 'Note updated!');
    }

    public function togglePin(Note $note)
    {
        abort_if($note->user_id !== Auth::id(), 403);
        $note->update(['is_pinned' => !$note->is_pinned]);
        return back()->with('success', 'Note pin toggled!');
    }

    public function destroy(Note $note)
    {
        abort_if($note->user_id !== Auth::id(), 403);
        $note->delete();
        return back()->with('success', 'Note deleted!');
    }
}
