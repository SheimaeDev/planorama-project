@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/event-edit.css') }}">
@endsection

@section('content')
<div class="event-form-container">
    <form action="{{ route('events.store') }}" method="POST" class="event-form">
        @csrf

        <button type="button" id="close-form-btn" class="close-form-btn" aria-label="Cancel event creation">
            &times;
        </button>

        <h2 class="text-center text-xl font-bold mb-4">Create Event</h2>

        <label for="title">Title:</label>
        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required aria-describedby="title-help">
        <small id="title-help" class="form-text text-muted">Enter a title for the event.</small>
        @error('title')
            <div class="error text-red-600">{{ $message }}</div>
        @enderror

        <label for="start_date">Start Date and Time:</label>
        <input type="datetime-local" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $defaultStart) }}" required>
        @error('start_date')
            <div class="error text-red-600">{{ $message }}</div>
        @enderror

        <label for="end_date">End Date and Time:</label>
        <input type="datetime-local" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $defaultEnd) }}" required>
        @error('end_date')
            <div class="error text-red-600">{{ $message }}</div>
        @enderror

        <label for="color_id">Color:</label>
        <select name="color_id" id="color_id" class="form-control">
            @foreach($colors as $color)
                <option value="{{ $color->id }}" style="background-color: {{ $color->hex }}; color: {{ $color->text_color ?? '#000' }};">{{ $color->name }}</option>
            @endforeach
        </select>
        @error('color_id')
            <div class="error text-red-600">{{ $message }}</div>
        @enderror

        <label for="share_emails">Share with (optional, emails separated by commas):</label>
        <input type="text" name="share_emails" id="share_emails" class="form-control" value="{{ old('share_emails') }}" placeholder="email1@example.com, email2@example.com" aria-describedby="share-help">
        <small id="share-help" class="form-text text-muted">Enter emails of registered users to share the event.</small>
        @if ($errors->has('share_emails'))
            <div class="error text-red-600">{{ $errors->first('share_emails') }}</div>
        @endif

        <div class="button-container">
            <button type="submit" id="save-event-btn" class="btn btn-primary" aria-label="Save Event">Save Event</button>
        </div>
    </form>
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
        if (event.target === formContainer && !event.target.closest('.event-form')) {
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
