@extends('layouts.app')

@section('content')


    <div class="calendar-main-wrapper">
        <h1 class="calendar-title">Calendar</h1>

        <div id="calendar-controls" class="flex justify-between items-center w-full mb-4" role="navigation" aria-label="Calendar navigation">
        
        <div class="flex items-center gap-4">
            <button id="prev-month" class="calendar-nav-btn" aria-label="Previous month">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <h2 id="calendar-title" class="text-xl font-semibold text-black" aria-live="polite" aria-atomic="true"></h2>

            <button id="next-month" class="calendar-nav-btn" aria-label="Next month">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>

        <div class="day-view-wrapper">
            <button id="day-view-btn" aria-label="View calendar in day view">        
                <img src="{{ asset('images/dia.png') }}" alt="Day View" class="h-9 w-auto">
            </button>
        </div>

    </div>

    <div id="calendar" class="calendar-grid" role="grid" aria-labelledby="calendar-title"></div>

    <div id="event-modal-root"></div>
</div>


@endsection

@section('scripts')
<script>
    const calendarEvents = @json($events);
    const currentAuthUserId = {{ Auth::id() }};

    document.addEventListener('DOMContentLoaded', () => {

        const dayViewBtn = document.getElementById('day-view-btn');
        dayViewBtn.addEventListener('click', () => {
            const today = new Date().toISOString().split('T')[0];
            window.location.href = `/events/day/${today}`;
        });
    });

</script>

<link rel="stylesheet" href="{{ asset('css/calendar.css') }}">
<link rel="stylesheet" href="{{ asset('css/day-calendar.css') }}">
<script src="{{ asset('js/calendar.js') }}"></script>
@endsection

