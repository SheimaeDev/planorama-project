@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/notes.css') }}">
@endsection

@section('content')
<div class="notes-page-container">
    <div class="top-controls">
        <a href="{{ route('notes.create') }}" class="floating-btn" aria-label="Add note">
            <span aria-hidden="true">+</span>
        </a>
    </div>

<h1 class="notes-title">Your Brilliant Notes</h1>

    <form id="filter-form" class="filter-form" aria-label="Filter notes" method="GET" action="{{ route('notes.index') }}">
        <div class="filter-row">
            <label for="filter-title">Subject:</label>
            <input type="text" id="filter-title" name="title" placeholder="Search by subject..." aria-label="Filter by subject" autocomplete="off" value="{{ request('title') }}" />
        </div>

        <div class="filter-actions">
            <button type="button" id="clear-filters" class="clear-btn" aria-label="Clear filters">✕</button>

        </div>
    </form>

    <div id="notes-container">
        <div class="notes-grid" role="list">
            @foreach ($notes as $note)
                <div
                    class="note-card"
                    role="listitem"
                    tabindex="0"
                    aria-label="Edit note: {{ $note->title }}"
                    data-title="{{ strtolower($note->title) }}"
                    data-created="{{ $note->created_at->format('Y-m-d') }}"
                    style="background-color: {{ $note->color->hex }}; color: {{ $note->color->text_color ?? '#000' }};" {{-- Assuming you have a text_color in the Color relationship --}}
                    onclick="window.location='{{ route('notes.edit', $note->id) }}'"
                    onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); this.click(); }"
                >
                    <h5 class="note-title" style="color: inherit;">{{ $note->title }}</h5>
                    <div class="note-date" style="color: inherit; font-size: 0.8rem; margin-top: 0.25rem;">
                        {{ $note->created_at->format('F d, Y') }} {{-- Changed to English date format --}}
                    </div>
                    <a href="{{ route('notes.edit', $note->id) }}" class="edit-btn" aria-label="Edit note {{ $note->title }}">✏️</a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const titleInput = document.getElementById('filter-title');
    const clearBtn = document.getElementById('clear-filters');
    const filterForm = document.getElementById('filter-form');

    function submitFilterForm() {
        filterForm.submit();
    }

    titleInput.addEventListener('input', () => {
        clearTimeout(titleInput.dataset.timeout);
        titleInput.dataset.timeout = setTimeout(submitFilterForm, 300);
    });

    clearBtn.addEventListener('click', () => {
        titleInput.value = '';
        submitFilterForm();
    });

});
</script>
@endsection
