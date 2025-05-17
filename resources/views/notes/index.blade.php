@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center my-4">Mis Notas</h2>
    
    <a href="{{ route('notes.create') }}" class="btn btn-primary mb-3">Crear Nota</a>

    <div class="d-flex flex-wrap gap-3">
        @foreach ($notes as $note)
            <div class="card p-3" style="background-color: {{ $note->color->hex }}; width: 200px;">
                <h5>{{ $note->subject }}</h5>
                <a href="{{ route('notes.edit', $note->id) }}" class="btn btn-light">Editar</a>
            </div>
        @endforeach
    </div>
</div>
@endsection
