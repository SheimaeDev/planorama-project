@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/event-edit.css') }}">
@endsection

@section('content')
<div class="event-form-container">
    <form action="{{ route('events.update', $event->id) }}" method="POST" class="event-form">
        @csrf
        @method('PUT')

        <button type="button" id="close-form-btn" class="close-form-btn" aria-label="Cancel event editing">
            &times;
        </button>

        <h2 class="text-center text-xl font-bold mb-4">Edit Event</h2>

        <label for="title">Title:</label>
        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $event->title) }}" required aria-describedby="title-help">
        <small id="title-help" class="form-text text-muted">Enter a title for the event.</small>
        @error('title')
            <div class="error text-red-600">{{ $message }}</div>
        @enderror

        <label for="start_date">Start Date and Time:</label>
        <input type="datetime-local" name="start_date" id="start_date" class="form-control"
            value="{{ old('start_date', \Carbon\Carbon::parse($event->start_date)->format('Y-m-d\TH:i')) }}" required>
        @error('start_date')
            <div class="error text-red-600">{{ $message }}</div>
        @enderror

        <label for="end_date">End Date and Time:</label>
        <input type="datetime-local" name="end_date" id="end_date" class="form-control"
            value="{{ old('end_date', \Carbon\Carbon::parse($event->end_date)->format('Y-m-d\TH:i')) }}" required>
        @error('end_date')
            <div class="error text-red-600">{{ $message }}</div>
        @enderror

        <label for="color_id">Color:</label>
        <select name="color_id" id="color_id" class="form-control">
            @foreach($colors as $color)
                <option value="{{ $color->id }}" style="background-color: {{ $color->hex }}; color: {{ $color->text_color ?? '#000' }};"
                    {{ $color->id == $event->color_id ? 'selected' : '' }}>
                    {{ $color->name }}
                </option>
            @endforeach
        </select>
        @error('color_id')
            <div class="error text-red-600">{{ $message }}</div>
        @enderror

        <hr class="my-4">

        <h3 class="text-lg font-semibold mt-4 mb-2">Share Event</h3>

        <label for="share_emails">Add users by email (optional, emails separated by commas, max 4):</label>
        <input type="text" name="share_emails" id="share_emails" class="form-control"
                value="{{ old('share_emails') }}" placeholder="email1@example.com, email2@example.com" aria-describedby="share-help">
        <small id="share-help" class="form-text text-muted">Enter emails of registered users to share the event.</small>
        @if ($errors->has('share_emails'))
            <div class="error text-red-600">{{ $message }}</div>
        @endif

        @php
            $sharedUsers = $event->users->filter(function ($user) use ($event) {
                return $user->id !== $event->creator_id;
            });
        @endphp

        @if($sharedUsers->isNotEmpty() || Auth::id() === $event->creator_id)
            <h3 class="text-lg font-semibold mt-4 mb-2">Current Users:</h3>
            <ul class="shared-users-list">
                <li class="shared-user-item">
                    <strong>{{ $event->creator->name }} ({{ $event->creator->email }})</strong>
                    @if(Auth::id() !== $event->creator_id)
                        <span class="text-gray-500"> (Creator)</span>
                    @endif
                </li>
                
                @foreach($sharedUsers as $user)
                    <li class="shared-user-item">
                        <input type="hidden" name="shared_user_ids[]" value="{{ $user->id }}">
                        <span class="text-gray-700">{{ $user->name }} ({{ $user->email }})</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-600">This event is not shared with anyone else.</p>
        @endif

        <div class="button-container">
            <button type="submit" id="save-event-btn" class="btn btn-primary" aria-label="Update Event">Update Event</button>
            
            @if(Auth::id() === $event->creator_id)
                <button type="submit" form="delete-form" class="btn btn-danger" aria-label="Delete Event">Delete Event</button>
            @endif
        </div>
    </form>

    @if(Auth::id() === $event->creator_id)
        <form id="delete-form" action="{{ route('events.destroy', $event->id) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const formContainer = document.querySelector('.event-form-container');
    const eventForm = document.querySelector('.event-form');
    const closeFormBtn = document.getElementById('close-form-btn');

    closeFormBtn.addEventListener('click', function() {
        window.location.href = "{{ route('events.index') }}";
    });

    formContainer.addEventListener('click', function (event) {
        if (event.target === formContainer) {
            window.location.href = "{{ route('events.index') }}";
        }
    });

    const colorSelect = document.getElementById('color_id');
    const form = document.querySelector('.event-form');
    const titleHeading = document.querySelector('.event-form h2');
    const labels = document.querySelectorAll('.event-form label');

    function updateFormColors() {
        const selectedOption = colorSelect.selectedOptions[0];
        const hexColor = selectedOption.style.backgroundColor;
        const textColor = selectedOption.style.color || '#000';

        form.style.backgroundColor = hexColor;
        form.style.color = textColor;
        if (titleHeading) {
            titleHeading.style.color = textColor;
        }
        labels.forEach(label => {
            label.style.color = textColor;
        });
    }

    if (colorSelect && form) {
        colorSelect.addEventListener('change', updateFormColors);
        updateFormColors();
    }
});
</script>
