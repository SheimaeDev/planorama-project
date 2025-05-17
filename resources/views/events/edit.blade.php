@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center my-4">Editar Evento</h2>

    <form action="{{ route('events.update', $event->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="subject" class="form-label">Asunto</label>
            <input type="text" name="subject" class="form-control" value="{{ $event->subject }}" required>
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label">Fecha de Inicio</label>
            <input type="datetime-local" name="start_date" class="form-control" value="{{ $event->start_date }}" required>
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">Fecha de Fin</label>
            <input type="datetime-local" name="end_date" class="form-control" value="{{ $event->end_date }}" required>
        </div>

        <div class="mb-3">
            <label for="background_color" class="form-label">Color de Fondo</label>
            <input type="color" name="background_color" class="form-control" value="{{ $event->background_color }}">
        </div>

        <button type="submit" class="btn btn-success">Actualizar Evento</button>
    </form>

    <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="mt-3">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Eliminar Evento</button>
    </form>
</div>
@endsection
