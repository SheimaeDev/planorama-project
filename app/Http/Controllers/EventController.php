<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Color;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Class EventController
 * @package App\Http\Controllers
 *
 * Controlador que gestiona todas las operaciones relacionadas con los eventos.
 * Esto incluye la visualización, creación, edición, actualización y eliminación de eventos,
 * así como la gestión de usuarios asociados a los eventos.
 */
class EventController extends Controller
{
    /**
     * Muestra una lista de todos los eventos en los que el usuario autenticado es el creador
     * o está asociado como participante.
     *
     * @return \Illuminate\View\View Retorna la vista 'events.index' con los eventos y colores.
     */
    public function index()
    {
        $userId = Auth::id();

        $events = Event::where('creator_id', $userId)
            ->orWhereHas('users', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->with(['color', 'users', 'creator'])
            ->get()
            ->map(function ($event) use ($userId) {
                $event->is_creator = ($event->creator_id == $userId);
                return $event;
            });

        $colors = Color::all();

        return view('events.index', compact('events', 'colors'));
    }

    /**
     * Muestra el formulario para crear un nuevo evento.
     *
     * @param  \Illuminate\Http\Request  $request  La instancia de la petición HTTP.
     * @return \Illuminate\View\View Retorna la vista 'events.create' con la fecha seleccionada
     * y los colores disponibles.
     */
    public function create(Request $request)
    {
        $selectedDate = $request->query('date');
        $colors = Color::all();

        $now = Carbon::now();
        $defaultStart = $selectedDate ? Carbon::parse($selectedDate)->setTime(9, 0)->format('Y-m-d\TH:i') : $now->format('Y-m-d\TH:i');
        $defaultEnd = $selectedDate ? Carbon::parse($selectedDate)->setTime(10, 0)->format('Y-m-d\TH:i') : $now->addHour()->format('Y-m-d\TH:i'); // Default end 1 hour after start

        return view('events.create', compact('selectedDate', 'colors', 'defaultStart', 'defaultEnd'));
    }

    /**
     * Almacena un nuevo evento en la base de datos.
     * Realiza la validación de los datos de entrada, crea el evento y asocia
     * a los usuarios compartidos si se proporcionan sus correos electrónicos.
     *
     * @param  \Illuminate\Http\Request  $request  La instancia de la petición HTTP con los datos del evento.
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta 'events.index' con un mensaje de éxito.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'color_id' => 'nullable|exists:colors,id',
            'share_emails' => ['nullable', 'string', function ($attribute, $value, $fail) {
                $emails = array_filter(array_map('trim', explode(',', $value)));
                if (count($emails) > 4) {
                    return $fail('You can only share with a maximum of 4 users.');
                }
                foreach ($emails as $email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return $fail("The email {$email} is not valid.");
                    }
                    if (!User::where('email', $email)->exists()) {
                        return $fail("The email {$email} does not belong to a registered user.");
                    }
                }
            }],
        ]);

        $creator = Auth::user();

        $event = Event::create([
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'creator_id' => $creator->id,
            'color_id' => $request->color_id,
        ]);

        // Adjunta al creador como participante del evento
        $event->users()->attach($creator->id, ['is_creator' => true]);

        if ($request->filled('share_emails')) {
            $emails = array_filter(array_map('trim', explode(',', $request->share_emails)));
            foreach ($emails as $email) {
                $userToShare = User::where('email', $email)->first();
                // Adjunta usuarios si existen y no son el creador
                if ($userToShare && $userToShare->id !== $creator->id) {
                    $event->users()->attach($userToShare->id, ['is_creator' => false]);
                }
            }
        }

        return redirect()->route('events.index')->with('success', 'Event created successfully');
    }

    /**
     * Muestra el formulario para editar un evento existente.
     *
     * @param  \App\Models\Event  $event  La instancia del evento a editar.
     * @param  \Illuminate\Http\Request  $request  La instancia de la petición HTTP.
     * @return \Illuminate\View\View Retorna la vista 'events.edit' con el evento, colores y fecha seleccionada.
     */
    public function edit(Event $event, Request $request)
    {
        $selectedDate = $request->query('date');
        $colors = Color::all();

        return view('events.edit', compact('event', 'colors', 'selectedDate'));
    }

    /**
     * Actualiza un evento existente en la base de datos.
     * Realiza la validación de los datos, actualiza los detalles del evento y sincroniza
     * los usuarios asociados al evento, incluyendo la posibilidad de añadir nuevos usuarios por email.
     *
     * @param  \Illuminate\Http\Request  $request  La instancia de la petición HTTP con los datos actualizados.
     * @param  \App\Models\Event  $event  La instancia del evento a actualizar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta 'events.index' con un mensaje de éxito.
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([ // Se renombra la variable de $validator a $request para seguir usando el helper validate()
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'color_id' => 'nullable|exists:colors,id',
            'shared_user_ids' => 'nullable|array',
            'shared_user_ids.*' => 'exists:users,id',
            'share_emails' => ['nullable', 'string', function ($attribute, $value, $fail) {
                $emails = array_filter(array_map('trim', explode(',', $value)));
                foreach ($emails as $email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        return $fail("The email {$email} is not valid.");
                    }
                    if (!User::where('email', $email)->exists()) {
                        return $fail("The email {$email} does not belong to a registered user.");
                    }
                }
            }],
        ]);

        $event->update($request->only('title', 'start_date', 'end_date', 'color_id'));

        $creatorId = $event->creator_id;
        $sharedUserIds = $request->input('shared_user_ids', []);
        $finalUserIds = array_unique(array_merge([$creatorId], $sharedUserIds));

        $syncData = [];
        foreach ($finalUserIds as $userId) {
            $syncData[$userId] = ['is_creator' => ($userId == $creatorId)];
        }
        // Sincroniza los usuarios existentes, manteniendo el creador
        $event->users()->sync($syncData);

        // Añade nuevos usuarios compartidos por email que no estén ya asociados
        if ($request->share_emails) {
            $emails = array_filter(array_map('trim', explode(',', $request->share_emails)));
            foreach ($emails as $email) {
                $userToShare = User::where('email', $email)->first();
                if ($userToShare && !array_key_exists($userToShare->id, $syncData)) {
                    $event->users()->attach($userToShare->id);
                }
            }
        }

        return redirect()->route('events.index')->with('success', 'Event updated successfully');
    }

    /**
     * Elimina un evento de la base de datos.
     *
     * @param  \App\Models\Event  $event  La instancia del evento a eliminar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta 'events.index' con un mensaje de éxito.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully');
    }

    /**
     * Muestra la vista de un día específico con sus eventos.
     * Recupera todos los eventos que se superponen con el día seleccionado.
     *
     * @param  string|null  $date  La fecha a mostrar en formato 'YYYY-MM-DD'. Si es nulo, usa la fecha actual.
     * @return \Illuminate\View\View Retorna la vista 'events.day' con la fecha seleccionada y los eventos de ese día.
     */
    public function showDayView(?string $date = null)
    {
        $selectedDate = $date ? Carbon::parse($date) : Carbon::now();
        $startOfDay = $selectedDate->copy()->startOfDay();
        $endOfDay = $selectedDate->copy()->endOfDay();

        $events = Event::where(function ($query) use ($startOfDay, $endOfDay) {
            $query->where('start_date', '<', $endOfDay)->where('end_date', '>', $startOfDay);
        })->orderBy('start_date')->with('color', 'creator', 'users')->get();

        return view('events.day', [
            'selectedDate' => $selectedDate,
            'events' => $events,
        ]);
    }

    /**
     * Redirige al índice de eventos. Este método está en desuso temporalmente.
     *
     * @param  int  $id  El ID del evento (actualmente no utilizado).
     * @return \Illuminate\Http\RedirectResponse Redirige a la ruta 'events.index'.
     */
    public function show($id)
    {
        return redirect()->route('events.index');
    }
}