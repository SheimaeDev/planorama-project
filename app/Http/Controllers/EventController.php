<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Color;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon; // Import Carbon for date handling

class EventController extends Controller
{
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

    public function create(Request $request)
    {
        $selectedDate = $request->query('date');
        $colors = Color::all();

        // Define default start and end times for the form
        $now = Carbon::now();
        $defaultStart = $selectedDate ? Carbon::parse($selectedDate)->setTime(9, 0)->format('Y-m-d\TH:i') : $now->format('Y-m-d\TH:i');
        $defaultEnd = $selectedDate ? Carbon::parse($selectedDate)->setTime(10, 0)->format('Y-m-d\TH:i') : $now->addHour()->format('Y-m-d\TH:i'); // Default end 1 hour after start

        return view('events.create', compact('selectedDate', 'colors', 'defaultStart', 'defaultEnd'));
    }

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

        // Create event
        $event = Event::create([
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'creator_id' => $creator->id,
            'color_id' => $request->color_id,
        ]);

        // Associate creator
        $event->users()->attach($creator->id, ['is_creator' => true]);

        // Process shared users by email
        if ($request->filled('share_emails')) {
            $emails = array_filter(array_map('trim', explode(',', $request->share_emails)));
            foreach ($emails as $email) {
                $userToShare = User::where('email', $email)->first();
                if ($userToShare && $userToShare->id !== $creator->id) {
                    $event->users()->attach($userToShare->id, ['is_creator' => false]);
                }
            }
        }

        return redirect()->route('events.index')->with('success', 'Event created successfully');
    }


    public function edit(Event $event, Request $request)
    {
        $selectedDate = $request->query('date');
        $colors = Color::all();

        return view('events.edit', compact('event', 'colors', 'selectedDate'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'color_id' => 'nullable|exists:colors,id',
            'shared_user_ids' => 'nullable|array',
            'shared_user_ids.*' => 'exists:users,id',
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

        // Update basic data
        $event->update($request->only('title', 'start_date', 'end_date', 'color_id'));

        // Sync shared users
        $creatorId = $event->creator_id;
        $sharedUserIds = $request->input('shared_user_ids', []);
        $finalUserIds = array_unique(array_merge([$creatorId], $sharedUserIds));

        $syncData = [];
        foreach ($finalUserIds as $userId) {
            $syncData[$userId] = ['is_creator' => ($userId == $creatorId)];
        }
        $event->users()->sync($syncData);

        // Process new emails to share
        if ($request->share_emails) {
            $emails = array_filter(array_map('trim', explode(',', $request->share_emails)));
            foreach ($emails as $email) {
                $userToShare = User::where('email', $email)->first();
                if ($userToShare && !array_key_exists($userToShare->id, $syncData)) {
                    $event->users()->attach($userToShare->id);
                }
            }
        }

        return redirect()->route('events.index', $event->id)->with('success', 'Event updated successfully');
    }


    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully');
    }
}
