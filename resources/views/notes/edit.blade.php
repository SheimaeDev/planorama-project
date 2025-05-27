@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/notes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/note-form.css') }}">
@endsection

@section('content')
<div class="note-form-container">
    <form action="{{ isset($note) ? route('notes.update', $note) : route('notes.store') }}" method="POST" class="note-form" style="background-color: {{ $note->color->hex ?? '#fff' }}; color: {{ $note->color->text_color ?? '#000' }};">
        @csrf
        @if(isset($note))
            @method('PUT')
            <p class="note-created-info" aria-live="polite" style="color: inherit;">Created on: {{ $note->created_at->format('d/m/Y H:i') }}</p>
        @endif

        <button type="button" id="close-form-btn" class="close-form-btn" aria-label="Cancel note editing">
            &times;
        </button>

        <div class="form-group">
            <label for="title" style="color: inherit;">Subject:</label>
            <input type="text" name="title" id="title" class="form-control"
                   value="{{ old('title', $note->title ?? '') }}" required aria-describedby="title-help">
            <small id="title-help" class="form-text text-muted">Enter a subject for the note.</small>
            @error('title')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="color" style="color: inherit;">Background Color</label>
            <select id="color" name="color_id" class="form-control">
                @foreach($colors as $color)
                    <option value="{{ $color->id }}"
                            data-hex="{{ $color->hex }}"
                            data-text-color="{{ $color->text_color ?? '#000' }}"
                            {{ old('color_id', $note->color_id ?? '') == $color->id ? 'selected' : '' }}>
                        {{ $color->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="button-container">
            <button type="submit" id="save-note-btn" class="btn btn-primary" aria-label="{{ isset($note) ? 'Update Note' : 'Save Note' }}">
                &#10003; {{ isset($note) ? 'Update' : 'Save' }}
            </button>

            @if(isset($note))
                <button type="submit" form="delete-form" class="btn btn-danger" aria-label="Delete Note">
                    Delete
                </button>
            @endif
        </div>
    </form>

    @if(isset($note))
        <form id="delete-form" action="{{ route('notes.destroy', $note->id) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endif
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
    const createdInfo = document.querySelector('.note-created-info');
    const titleLabel = document.querySelector('.note-form label[for="title"]');
    const colorLabel = document.querySelector('.note-form label[for="color"]');

    function updateBackgroundColorAndTextColor() {
        const selectedOption = colorSelect.selectedOptions[0];
        const hexColor = selectedOption.getAttribute('data-hex');
        const textColor = selectedOption.getAttribute('data-text-color') || '#000';

        form.style.backgroundColor = hexColor;
        form.style.color = textColor;
        if (createdInfo) {
            createdInfo.style.color = textColor;
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