@extends('layouts.app')

@section('content')
<div class="day-calendar-wrapper">
    <div class="day-navigation" style="display: flex; align-items: center; justify-content: center; gap: 1rem;">
        <button id="prev-day" aria-label="Previous day" style="order: 1;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <h1 id="date-heading" class="day-calendar-title" aria-live="polite" style="order: 2;">
            {{ \Carbon\Carbon::parse($selectedDate)->format('l, j F Y') }}
        </h1>
        <button id="next-day" aria-label="Next day" style="order: 3;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    <div id="day-events-container" class="hourly-grid" tabindex="0">
        @for ($hour = 6; $hour <= 29; $hour++)
            @php
                $displayHour = $hour % 24;
                $slotStart = $selectedDate->copy()->setTime($displayHour, 0);
                $slotEnd = $slotStart->copy()->addHour();
            @endphp

            <div class="hour-slot" data-hour="{{ $displayHour }}">
                <div class="hour-label">{{ sprintf('%02d:00', $displayHour) }}</div>
                <div class="events-in-slot" data-slot-hour="{{ $displayHour }}" style="position: relative;">
                    @php $eventIndex = 0; @endphp
                    @foreach ($events as $event)
                        @php
                            $start = \Carbon\Carbon::parse($event->start_date);
                            $end = \Carbon\Carbon::parse($event->end_date);

                            if ($start->toDateString() !== $selectedDate->toDateString()) continue;

                            if ($start >= $slotStart && $start < $slotEnd) {
                                $durationMinutes = min($end->diffInMinutes($start), 60 - $start->minute);
                                $topPercentage = ($start->minute / 60) * 100;
                                $heightPercentage = ($durationMinutes / 60) * 100;
                                $leftPercentage = ($eventIndex % 5) * 20;
                                $eventIndex++;
                            } else {
                                continue;
                            }
                        @endphp

                        <div class="day-event-item"
                            style="
                                background-color: {{ $event->color?->hex ?? '#f0f0f0' }};
                                color: {{ $event->color?->text_color ?? '#333' }};
                                top: {{ $topPercentage }}%;
                                height: {{ $heightPercentage }}%;
                                left: {{ $leftPercentage }}%;
                                width: 80%;
                                position: absolute;
                            "
                            onclick="window.location.href='/events/{{ $event->id }}/edit'">
                            {{ $event->title }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endfor
    </div>

    <div class="mt-4" style="display: flex; justify-content: space-between; gap: 1rem;">
        <a href="{{ route('events.create', ['date' => $selectedDate->format('Y-m-d')]) }}" class="button secondary" style="flex-grow: 1; max-width: 200px;">
            Create new event
        </a>
       <a href="#" onclick="history.back(); return false;" class="button secondary" style="max-width: 200px; margin-left: auto;">
            Go back
        </a>
    </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="{{ asset('css/day-calendar.css') }}">
<script>
document.addEventListener('DOMContentLoaded', function () {
    const date = new Date('{{ $selectedDate->format('Y-m-d') }}');
    const prevDay = document.getElementById('prev-day');
    const nextDay = document.getElementById('next-day');

    function format(date) {
        return date.toISOString().split('T')[0];
    }

    function navigateDay(delta) {
        date.setDate(date.getDate() + delta);
        window.location.href = `/events/day/${format(date)}`;
    }

    prevDay.addEventListener('click', () => navigateDay(-1));
    nextDay.addEventListener('click', () => navigateDay(1));

    document.querySelectorAll('.day-event-item').forEach(eventEl => {
        eventEl.addEventListener('click', function (e) {
            e.stopPropagation();
            const url = this.getAttribute('onclick').match(/'(.*?)'/)[1];
            window.location.href = url;
        });
    });

    document.querySelectorAll('.events-in-slot').forEach(slot => {
        slot.addEventListener('click', function (e) {
            if (e.target.closest('.day-event-item')) return;

            const hour = this.getAttribute('data-slot-hour');
            const selectedDate = '{{ $selectedDate->format('Y-m-d') }}';

            const startDateTime = `${selectedDate}T${hour.padStart(2, '0')}:00`;

            window.location.href = `/events/create?date=${startDateTime}`;
        });
    });
});
</script>
@endsection
