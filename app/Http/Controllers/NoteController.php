<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\Color;
use App\Models\Note;

use Illuminate\Http\Request;

class NoteController extends Controller
{
   
    public function index()
    {
        $notes = Note::where('user_id', Auth::user()->id)->get();
        return view('notes.index', compact('notes'));
    }


    public function create()
    {
        $colors = Color::all(); // Obtiene los colores disponibles
        return view('notes.create', compact('colors'));
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'color_id' => 'required|exists:colors,id',
        ]);

        Note::create([
            'title' => $validated['title'],
            'color_id' => $validated['color_id'],
            'user_id' => Auth::user()->id,
        ]);

        return redirect()->route('notes.index')->with('success', 'Nota creada correctamente');
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
        ]);

        $note->update($validated);

        return redirect()->route('notes.index')->with('success', 'Nota actualizada correctamente');
    }

    
    public function destroy(Note $note)
    {
        $note->delete();
        return redirect()->route('notes.index')->with('success', 'Nota eliminada correctamente');
    }
}
