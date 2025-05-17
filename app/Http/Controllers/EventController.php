<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Models\Event;
use Illuminate\Http\Request;


class EventController extends Controller
{
    
    public function index()
    {
        $events = Event::all(); // Obtiene todos los eventos
        return view('events.index', compact('events')); // Envía los eventos a la vista
    }

    
    public function create()
    {
        return view('events.create'); // Devuelve la vista del formulario de creación
    }

  
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'background_color' => 'nullable|string'
        ]);

        $event = Event::create([
            'subject' => $validated['subject'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'background_color' => $validated['background_color'],
            'created_by' => Auth::user()->id, 
        ]);

        return redirect()->route('events.index')->with('success', 'Evento creado correctamente');
    }

    
    public function edit(Event $event)
    {
        return view('events.edit', compact('event')); // Envía el evento a la vista de edición
    }

   
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'background_color' => 'nullable|string'
        ]);

        $event->update($validated);

        return redirect()->route('events.index')->with('success', 'Evento actualizado correctamente');
    }

   
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Evento eliminado correctamente');
    }
}