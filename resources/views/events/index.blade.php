@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center my-4">Calendario de Eventos</h2>
    
    <div id="calendar"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: "{{ route('events.index') }}",
            selectable: true,
            editable: true,
            eventClick: function(info) {
                window.location.href = "{{ url('events') }}/" + info.event.id + "/edit";
            }
        });
        calendar.render();
    });
</script>
@endsection
