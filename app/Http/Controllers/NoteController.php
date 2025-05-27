<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Color;
use App\Models\Note;

use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Note::where('user_id', Auth::user()->id);

        // Filters
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->input('created_from'));
        }

        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->input('created_to'));
        }

        if ($request->filled('updated_from')) {
            $query->whereDate('updated_at', '>=', $request->input('updated_from'));
        }

        if ($request->filled('updated_to')) {
            $query->whereDate('updated_at', '<=', $request->input('updated_to'));
        }

        $notes = $query->orderBy('created_at', 'desc')->get();
        return view('notes.index', compact('notes'));
    }
    public function create()
    {
        $colors = Color::all(); 
        return view('notes.create', compact('colors'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'color_id' => 'required|exists:colors,id',
        ], [
            'title.max' => 'The subject cannot be longer than 255 characters.',
        ]);

        Note::create([
            'title' => $validated['title'],
            'color_id' => $validated['color_id'],
            'user_id' => Auth::user()->id,
        ]);

        return redirect()->route('notes.index')->with('success', 'Note created successfully');
    }


    public function edit(Note $note)
    {
        $colors = Color::all();
        return view('notes.edit', compact('note', 'colors'));
    }


    public function update(Request $request, Note $note)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'color_id' => 'required|exists:colors,id',
        ], [
            'title.max' => 'The subject cannot be longer than 255 characters.',
        ]);

        $note->update($validated);

        return redirect()->route('notes.index')->with('success', 'Note updated successfully');
    }


    public function destroy(Note $note)
    {
        $note->delete();
        return redirect()->route('notes.index')->with('success', 'Note deleted successfully');
    }
}
