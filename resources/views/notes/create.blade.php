@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/notes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/note-form.css') }}">
@endsection

@section('content')
<div class="note-form-container">
    <form action="{{ route('notes.store') }}" method="POST" class="note-form" style="background-color: #fff; color: #000;">
        @csrf

        <button type="button" id="close-form-btn" class="close-form-btn" aria-label="Cancel note creation">
            &times;
        </button>

        <h2 class="text-center text-xl font-bold mb-4" style="color: inherit;">Create Note</h2>

        <div class="form-group">
            <label for="title" style="color: inherit;">Subject:</label>
            <input type="text" name="title" id="title" class="form-control" required
                   value="{{ old('title') }}" aria-describedby="title-help">
            <small id="title-help" class="form-text text-muted">Enter a subject for the note.</small>
            @error('title')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="color" style="color: inherit;">Background Color</label>
            <select id="color" name="color_id" class="form-control">
                @foreach($colors as $color)
                    <option value="{{ $color->id }}" data-hex="{{ $color->hex }}" data-text-color="{{ $color->text_color ?? '#000' }}"
                            {{ old('color_id') == $color->id ? 'selected' : '' }}>
                        {{ $color->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="button-container">
            <button type="submit" id="save-note-btn" class="btn btn-primary" aria-label="Save Note">
                &#10003; Save
            </button>
        </div>
    </form>
</div>

@if ($errors->any())
    <div class="alert alert-danger mb-4" role="alert">
        <ul class="list-disc list-inside text-red-600 text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const colorSelect = document.getElementById('color');
    const form = document.querySelector('.note-form');
    const titleHeading = document.querySelector('.note-form h2');
    const titleLabel = document.querySelector('.note-form label[for="title"]');
    const colorLabel = document.querySelector('.note-form label[for="color"]');

    function updateBackgroundColorAndTextColor() {
        const selectedOption = colorSelect.selectedOptions[0];
        const hexColor = selectedOption.getAttribute('data-hex');
        const textColor = selectedOption.getAttribute('data-text-color') || '#000';

        form.style.backgroundColor = hexColor;
        form.style.color = textColor;
        if (titleHeading) {
            titleHeading.style.color = textColor;
        }
        titleLabel.style.color = textColor;
        colorLabel.style.color = textColor;
    }

    colorSelect.addEventListener('input', updateBackgroundColorAndTextColor);
    updateBackgroundColorAndTextColor();

    const formContainer = document.querySelector('.note-form-container');
    const noteForm = document.querySelector('.note-form');
    const closeFormBtn = document.getElementById('close-form-btn');

    closeFormBtn.addEventListener('click', function() {
        window.location.href = "{{ route('notes.index') }}";
    });

    formContainer.addEventListener('click', function (event) {
        if (event.target === formContainer) {
            window.location.href = "{{ route('notes.index') }}";
        }
    });
});
</script>
@endsection