<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Although not used, it's good to keep track if you're planning to use it.

use App\Models\Color;
use App\Models\Note;

use Illuminate\Http\Request;

/**
 * Class NoteController
 * @package App\Http\Controllers
 *
 * Controlador que maneja la lógica de negocio para la gestión de notas.
 * Incluye funcionalidades para listar, crear, editar, actualizar y eliminar notas,
 * con soporte para filtros de búsqueda.
 */
class NoteController extends Controller
{
    /**
     * Muestra una lista paginada de notas para el usuario autenticado,
     * aplicando filtros opcionales de título y fechas de creación/actualización.
     *
     * @param  \Illuminate\Http\Request  $request  La instancia de la petición HTTP, que puede contener parámetros de filtro.
     * @return \Illuminate\View\View Retorna la vista 'notes.index' con la colección de notas filtradas.
     */
    public function index(Request $request)
    {
        $query = Note::where('user_id', Auth::user()->id);

        // Aplica filtros si los parámetros correspondientes están presentes en la petición.
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

    /**
     * Muestra el formulario para crear una nueva nota.
     *
     * @return \Illuminate\View\View Retorna la vista 'notes.create' con todas los colores disponibles.
     */
    public function create()
    {
        $colors = Color::all();
        return view('notes.create', compact('colors'));
    }

    /**
     * Almacena una nueva nota en la base de datos.
     * Valida los datos de entrada y asocia la nota con el usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request  La instancia de la petición HTTP con los datos de la nota.
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta 'notes.index' con un mensaje de éxito.
     */
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

    /**
     * Muestra el formulario para editar una nota existente.
     *
     * @param  \App\Models\Note  $note  La instancia de la nota a editar.
     * @return \Illuminate\View\View Retorna la vista 'notes.edit' con la nota y todos los colores disponibles.
     */
    public function edit(Note $note)
    {
        $colors = Color::all();
        return view('notes.edit', compact('note', 'colors'));
    }

    /**
     * Actualiza una nota existente en la base de datos.
     * Valida los datos de entrada y aplica los cambios a la nota especificada.
     *
     * @param  \Illuminate\Http\Request  $request  La instancia de la petición HTTP con los datos actualizados.
     * @param  \App\Models\Note  $note  La instancia de la nota a actualizar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta 'notes.index' con un mensaje de éxito.
     */
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

    /**
     * Elimina una nota específica de la base de datos.
     *
     * @param  \App\Models\Note  $note  La instancia de la nota a eliminar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta 'notes.index' con un mensaje de éxito.
     */
    public function destroy(Note $note)
    {
        $note->delete();
        return redirect()->route('notes.index')->with('success', 'Note deleted successfully');
    }
}